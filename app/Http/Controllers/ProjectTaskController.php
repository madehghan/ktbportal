<?php

namespace App\Http\Controllers;

use App\Models\ProjectColumn;
use App\Models\ProjectTask;
use App\Models\Project;
use App\Services\DateConverterService;
use App\Services\BotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ProjectColumn $column)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*' => 'exists:users,id',
            'due_date_jalali' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $maxOrder = $column->tasks()->max('order') ?? -1;

        // Convert Jalali date to Gregorian if provided
        $dueDate = null;
        if (!empty($validated['due_date_jalali'])) {
            try {
                $dueDate = DateConverterService::jalaliToGregorian($validated['due_date_jalali']);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'تاریخ وارد شده معتبر نیست',
                ], 422);
            }
        }

        $task = $column->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $dueDate,
            'due_date_jalali' => $validated['due_date_jalali'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'order' => $maxOrder + 1,
        ]);

        // Attach assigned users
        if (!empty($validated['assigned_user_ids'])) {
            $task->assignedUsers()->attach($validated['assigned_user_ids']);
            
            // Send notification messages to newly assigned users
            $task->load('column.project');
            $project = $task->column->project;
            foreach ($validated['assigned_user_ids'] as $userId) {
                $message = "✅ تسک جدید به شما اختصاص داده شد:\n\n";
                $message .= "📋 عنوان: {$task->title}\n";
                $message .= "📁 پروژه: {$project->name}\n";
                if ($task->description) {
                    $message .= "\n📝 توضیحات:\n{$task->description}\n";
                }
                if ($task->due_date_jalali) {
                    $message .= "\n📅 مهلت انجام: {$task->due_date_jalali}";
                }
                if ($task->priority) {
                    $priorityLabels = [
                        'low' => 'پایین',
                        'medium' => 'متوسط',
                        'high' => 'بالا',
                    ];
                    $message .= "\n⚡ اولویت: " . ($priorityLabels[$task->priority] ?? $task->priority);
                }
                BotService::sendMessage($userId, $message);
            }
        }

        $task->load('assignedUsers');
        $taskData = $task->toArray();
        $taskData['assigned_users'] = $task->assignedUsers->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'task' => $taskData,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectTask $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*' => 'exists:users,id',
            'due_date_jalali' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'project_column_id' => 'sometimes|exists:project_columns,id',
            'order' => 'sometimes|integer|min:0',
        ]);

        // Convert Jalali date to Gregorian if provided
        if (isset($validated['due_date_jalali'])) {
            if (!empty($validated['due_date_jalali'])) {
                try {
                    $validated['due_date'] = DateConverterService::jalaliToGregorian($validated['due_date_jalali']);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تاریخ وارد شده معتبر نیست',
                    ], 422);
                }
            } else {
                $validated['due_date'] = null;
            }
        }

        $task->update($validated);

        // Sync assigned users
        if (isset($validated['assigned_user_ids'])) {
            $oldUserIds = $task->assignedUsers->pluck('id')->toArray();
            $newUserIds = $validated['assigned_user_ids'] ?? [];
            $task->assignedUsers()->sync($newUserIds);
            
            // Find newly added users
            $newlyAddedUserIds = array_diff($newUserIds, $oldUserIds);
            
            // Send notification messages to newly assigned users
            if (!empty($newlyAddedUserIds)) {
                $task->load('column.project');
                $project = $task->column->project;
                foreach ($newlyAddedUserIds as $userId) {
                    $message = "✅ تسک جدید به شما اختصاص داده شد:\n\n";
                    $message .= "📋 عنوان: {$task->title}\n";
                    $message .= "📁 پروژه: {$project->name}\n";
                    if ($task->description) {
                        $message .= "\n📝 توضیحات:\n{$task->description}\n";
                    }
                    if ($task->due_date_jalali) {
                        $message .= "\n📅 مهلت انجام: {$task->due_date_jalali}";
                    }
                    if ($task->priority) {
                        $priorityLabels = [
                            'low' => 'پایین',
                            'medium' => 'متوسط',
                            'high' => 'بالا',
                        ];
                        $message .= "\n⚡ اولویت: " . ($priorityLabels[$task->priority] ?? $task->priority);
                    }
                    BotService::sendMessage($userId, $message);
                }
            }
        }

        $task->load('assignedUsers');
        $taskData = $task->toArray();
        $taskData['assigned_users'] = $task->assignedUsers->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'task' => $taskData,
        ]);
    }

    /**
     * Reorder tasks within a column or move between columns.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:project_tasks,id',
            'tasks.*.order' => 'required|integer|min:0',
            'tasks.*.project_column_id' => 'required|exists:project_columns,id',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            ProjectTask::where('id', $taskData['id'])->update([
                'order' => $taskData['order'],
                'project_column_id' => $taskData['project_column_id'],
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Create a task from anywhere (with project and column selection).
     */
    public function createFromAnywhere(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_column_id' => 'required|exists:project_columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*' => 'exists:users,id',
            'due_date_jalali' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Verify that the column belongs to the project
        $column = ProjectColumn::where('id', $validated['project_column_id'])
            ->where('project_id', $validated['project_id'])
            ->firstOrFail();

        $maxOrder = $column->tasks()->max('order') ?? -1;

        // Convert Jalali date to Gregorian if provided
        $dueDate = null;
        if (!empty($validated['due_date_jalali'])) {
            try {
                $dueDate = DateConverterService::jalaliToGregorian($validated['due_date_jalali']);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'تاریخ وارد شده معتبر نیست',
                ], 422);
            }
        }

        $task = $column->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $dueDate,
            'due_date_jalali' => $validated['due_date_jalali'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'order' => $maxOrder + 1,
        ]);

        // Attach assigned users
        if (!empty($validated['assigned_user_ids'])) {
            $task->assignedUsers()->attach($validated['assigned_user_ids']);
            
            // Send notification messages to newly assigned users
            $task->load('column.project');
            $project = $task->column->project;
            foreach ($validated['assigned_user_ids'] as $userId) {
                $message = "✅ تسک جدید به شما اختصاص داده شد:\n\n";
                $message .= "📋 عنوان: {$task->title}\n";
                $message .= "📁 پروژه: {$project->name}\n";
                if ($task->description) {
                    $message .= "\n📝 توضیحات:\n{$task->description}\n";
                }
                if ($task->due_date_jalali) {
                    $message .= "\n📅 مهلت انجام: {$task->due_date_jalali}";
                }
                if ($task->priority) {
                    $priorityLabels = [
                        'low' => 'پایین',
                        'medium' => 'متوسط',
                        'high' => 'بالا',
                    ];
                    $message .= "\n⚡ اولویت: " . ($priorityLabels[$task->priority] ?? $task->priority);
                }
                BotService::sendMessage($userId, $message);
            }
        }

        $task->load('assignedUsers', 'column.project');
        $taskData = $task->toArray();
        $taskData['assigned_users'] = $task->assignedUsers->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        })->toArray();
        
        return response()->json([
            'success' => true,
            'message' => 'تسک با موفقیت ایجاد شد',
            'task' => $taskData,
        ]);
    }

    /**
     * Get columns for a specific project.
     */
    public function getColumns(Project $project)
    {
        $columns = $project->columns()->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'columns' => $columns->map(function($column) {
                return [
                    'id' => $column->id,
                    'name' => $column->name,
                    'color' => $column->color,
                ];
            }),
        ]);
    }

    /**
     * Toggle the completion status of the task.
     */
    public function toggle(ProjectTask $task)
    {
        $task->is_completed = !$task->is_completed;
        
        if ($task->is_completed) {
            $task->completed_at = now();
            $task->completed_by = Auth::id();
        } else {
            $task->completed_at = null;
            $task->completed_by = null;
        }
        
        $task->save();
        $task->load('completedBy');

        $response = [
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'is_completed' => $task->is_completed,
                'completed_at' => $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : null,
                'completed_at_shamsi' => $task->completed_at ? DateConverterService::gregorianToJalali($task->completed_at->format('Y-m-d')) : null,
                'completed_by' => $task->completedBy ? [
                    'id' => $task->completedBy->id,
                    'name' => $task->completedBy->name,
                ] : null,
            ],
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectTask $task)
    {
        $task->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
