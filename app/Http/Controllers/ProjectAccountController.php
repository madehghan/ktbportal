<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAccount;
use Illuminate\Http\Request;

class ProjectAccountController extends Controller
{
    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'url' => 'nullable|url|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $account = $project->accounts()->create($validated);

        return response()->json([
            'success' => true,
            'account' => $account,
        ]);
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, Project $project, $id)
    {
        $account = ProjectAccount::where('project_id', $project->id)->findOrFail($id);

        $validated = $request->validate([
            'url' => 'nullable|url|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $account->update($validated);

        return response()->json([
            'success' => true,
            'account' => $account->fresh(),
        ]);
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy(Project $project, $id)
    {
        $account = ProjectAccount::where('project_id', $project->id)->findOrFail($id);
        $account->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
