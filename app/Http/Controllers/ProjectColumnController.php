<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectColumn;
use Illuminate\Http\Request;

class ProjectColumnController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $maxOrder = $project->columns()->max('order') ?? -1;

        $column = $project->columns()->create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'column' => $column->load('tasks.assignedUser'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectColumn $column)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:7',
            'order' => 'sometimes|integer|min:0',
        ]);

        $column->update($validated);

        return response()->json([
            'success' => true,
            'column' => $column,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectColumn $column)
    {
        $column->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
