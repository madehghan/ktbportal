<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use App\Models\AgileBoard;
use App\Models\AgileBoardTab;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\LoginLog;
use App\Services\BotService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get tasks where user is assigned (not completed)
        $tasks = ProjectTask::whereHas('assignedUsers', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('is_completed', false)
        ->with(['column.project', 'assignedUsers', 'completedBy'])
        ->orderBy('created_at', 'desc')
        ->get();
        
        // Group tasks by project, then by column
        $tasksByProject = $tasks->groupBy(function($task) {
            return $task->column->project->id;
        })->map(function($projectTasks, $projectId) {
            $project = $projectTasks->first()->column->project;
            
            // Group tasks by column within each project
            $tasksByColumn = $projectTasks->groupBy(function($task) {
                return $task->column->id;
            })->map(function($columnTasks, $columnId) {
                $column = $columnTasks->first()->column;
                return [
                    'column' => [
                        'id' => $column->id,
                        'name' => $column->name,
                        'color' => $column->color,
                    ],
                    'tasks' => $columnTasks->map(function($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'description' => $task->description,
                            'priority' => $task->priority,
                            'due_date_jalali' => $task->due_date_jalali,
                            'assigned_users' => $task->assignedUsers->map(function($user) {
                                return [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                ];
                            })->toArray(),
                        ];
                    })->values(),
                ];
            })->values();
            
            return [
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
                'columns' => $tasksByColumn,
            ];
        })->values();
        
        // Get tabs that user has access to (or all tabs if admin)
        $isAdmin = $user->role && $user->role->name === 'admin';
        
        if ($isAdmin) {
            $tabs = AgileBoardTab::with('users', 'creator')
                ->orderBy('order')
                ->get();
        } else {
            $tabs = AgileBoardTab::whereHas('users', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with('users', 'creator')
            ->orderBy('order')
            ->get();
        }
        
        // Get all users for tab management (only for admin)
        $allUsers = $isAdmin ? User::all() : collect();
        
        // Count incomplete tasks assigned to user
        $incompleteTasksCount = ProjectTask::whereHas('assignedUsers', function($query) use ($user) {
            $query->where('users.id', $user->id);
        })
        ->where('is_completed', false)
        ->count();
        
        // Count unread messages for user
        // Get all conversations where user is a participant
        $conversationIds = Conversation::where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id)
                  ->orWhereHas('project', function($q) use ($user) {
                      $q->whereHas('users', function($q2) use ($user) {
                          $q2->where('users.id', $user->id);
                      });
                  });
        })->pluck('id');
        
        $unreadMessagesCount = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();
        
        // Get last 5 login logs
        $loginLogs = LoginLog::where('user_id', $user->id)
            ->orderBy('login_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($log) {
                return [
                    'type' => 'login',
                    'title' => 'ورود به سیستم',
                    'description' => 'ورود از ' . ($log->ip_address ?? 'نامشخص'),
                    'created_at' => $log->login_at ?? $log->created_at,
                    'icon' => 'login',
                ];
            });
        
        // Get bot user
        $bot = BotService::getBotUser();
        
        // Get conversation with bot
        $botConversation = Conversation::where(function($query) use ($user, $bot) {
            $query->where(function($q) use ($user, $bot) {
                $q->where('user1_id', $bot->id)
                  ->where('user2_id', $user->id);
            })->orWhere(function($q) use ($user, $bot) {
                $q->where('user1_id', $user->id)
                  ->where('user2_id', $bot->id);
            });
        })->first();
        
        // Get last 5 bot messages
        $botMessages = collect();
        if ($botConversation) {
            $botMessages = Message::where('conversation_id', $botConversation->id)
                ->where('sender_id', $bot->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($message) {
                    return [
                        'type' => 'bot_message',
                        'title' => 'پیام ربات',
                        'description' => mb_substr(strip_tags($message->body ?? ''), 0, 100) . (mb_strlen($message->body ?? '') > 100 ? '...' : ''),
                        'created_at' => $message->created_at,
                        'icon' => 'message',
                    ];
                });
        }
        
        // Merge and sort by date
        $recentActivities = $loginLogs->merge($botMessages)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
        
        return view('dashboard', compact('tasksByProject', 'tabs', 'isAdmin', 'allUsers', 'incompleteTasksCount', 'unreadMessagesCount', 'recentActivities'));
    }

    /**
     * Store a new tab.
     */
    public function storeTab(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'شما دسترسی به این بخش را ندارید',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $maxOrder = AgileBoardTab::max('order') ?? 0;

        $tab = AgileBoardTab::create([
            'name' => $validated['name'],
            'order' => $maxOrder + 1,
            'created_by' => $user->id,
        ]);

        // Attach users
        if (!empty($validated['user_ids'])) {
            $tab->users()->attach($validated['user_ids']);
        }

        $tab->load('users', 'creator');

        return response()->json([
            'success' => true,
            'tab' => [
                'id' => $tab->id,
                'name' => $tab->name,
                'content' => $tab->content,
                'order' => $tab->order,
                'users' => $tab->users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })->toArray(),
            ],
        ]);
    }

    /**
     * Update tab content.
     */
    public function updateTabContent(Request $request, AgileBoardTab $tab)
    {
        $user = Auth::user();
        
        // Check if user is admin (only admin can edit)
        $isAdmin = $user->role && $user->role->name === 'admin';
        
        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'فقط مدیر سیستم می‌تواند محتوا را ویرایش کند',
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'nullable|string',
        ]);

        $tab->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'محتوای تب با موفقیت به‌روزرسانی شد',
        ]);
    }

    /**
     * Update tab (name and users) (only admin).
     */
    public function updateTab(Request $request, AgileBoardTab $tab)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'شما دسترسی به این بخش را ندارید',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $tab->update([
            'name' => $validated['name'],
        ]);

        $tab->users()->sync($validated['user_ids'] ?? []);

        $tab->load('users');

        return response()->json([
            'success' => true,
            'message' => 'تب با موفقیت به‌روزرسانی شد',
            'tab' => [
                'id' => $tab->id,
                'name' => $tab->name,
                'content' => $tab->content,
                'order' => $tab->order,
                'users' => $tab->users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })->toArray(),
            ],
        ]);
    }

    /**
     * Update tab users (only admin).
     */
    public function updateTabUsers(Request $request, AgileBoardTab $tab)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'شما دسترسی به این بخش را ندارید',
            ], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $tab->users()->sync($validated['user_ids'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'کاربران تب با موفقیت به‌روزرسانی شدند',
        ]);
    }

    /**
     * Delete a tab (only admin).
     */
    public function deleteTab(AgileBoardTab $tab)
    {
        $user = Auth::user();
        
        // Check if user is admin
        if (!$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'شما دسترسی به این بخش را ندارید',
            ], 403);
        }

        $tab->delete();

        return response()->json([
            'success' => true,
            'message' => 'تب با موفقیت حذف شد',
        ]);
    }
}
