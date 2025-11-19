<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\DateConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $project->tasks()->max('order') ?? 0;

        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        $task->load('completedBy');
        
        return response()->json([
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
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Project $project, ProjectTask $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'وظیفه متعلق به این پروژه نیست'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($validated);

        $task->load('completedBy');
        
        return response()->json([
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
        ]);
    }

    /**
     * Toggle the completion status of the task.
     */
    public function toggle(Project $project, ProjectTask $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'وظیفه متعلق به این پروژه نیست'], 403);
        }

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
     * Remove the specified task from storage.
     */
    public function destroy(Project $project, ProjectTask $task)
    {
        // Ensure task belongs to project
        if ($task->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'وظیفه متعلق به این پروژه نیست'], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
