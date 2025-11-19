<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectFileController extends Controller
{
    /**
     * Get all files for a project.
     */
    public function index(Project $project)
    {
        // Check if user is a member of the project
        $user = auth()->user();
        $isMember = $project->users()->where('users.id', $user->id)->exists();
        
        if (!$isMember) {
            return response()->json(['error' => 'شما عضو این پروژه نیستید'], 403);
        }

        $files = $project->files()->with('uploader')->get()->map(function($file) {
            return [
                'id' => $file->id,
                'title' => $file->title,
                'description' => $file->description,
                'file_name' => $file->file_name,
                'original_name' => $file->original_name,
                'file_type' => $file->file_type,
                'file_size' => $file->file_size,
                'url' => asset('storage/' . $file->file_path),
                'uploaded_by' => $file->uploader->name,
                'uploaded_by_id' => $file->uploader->id,
                'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                'created_at_formatted' => $file->created_at->format('Y/m/d H:i'),
            ];
        });

        return response()->json($files);
    }

    /**
     * Upload a new file for a project.
     */
    public function store(Request $request, Project $project)
    {
        // Check if user is a member of the project
        $user = auth()->user();
        $isMember = $project->users()->where('users.id', $user->id)->exists();
        
        if (!$isMember) {
            return response()->json(['error' => 'شما عضو این پروژه نیستید'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('project_files', $fileName, 'public');

        $projectFile = ProjectFile::create([
            'project_id' => $project->id,
            'uploaded_by' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $projectFile->load('uploader');

        return response()->json([
            'id' => $projectFile->id,
            'title' => $projectFile->title,
            'description' => $projectFile->description,
            'file_name' => $projectFile->file_name,
            'original_name' => $projectFile->original_name,
            'file_type' => $projectFile->file_type,
            'file_size' => $projectFile->file_size,
            'url' => asset('storage/' . $projectFile->file_path),
            'uploaded_by' => $projectFile->uploader->name,
            'uploaded_by_id' => $projectFile->uploader->id,
            'created_at' => $projectFile->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $projectFile->created_at->format('Y/m/d H:i'),
        ], 201);
    }

    /**
     * Delete a project file.
     */
    public function destroy(Project $project, ProjectFile $projectFile)
    {
        // Check if user is a member of the project
        $user = auth()->user();
        $isMember = $project->users()->where('users.id', $user->id)->exists();
        
        if (!$isMember) {
            return response()->json(['error' => 'شما عضو این پروژه نیستید'], 403);
        }

        // Check if file belongs to this project
        if ($projectFile->project_id !== $project->id) {
            return response()->json(['error' => 'فایل متعلق به این پروژه نیست'], 404);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($projectFile->file_path)) {
            Storage::disk('public')->delete($projectFile->file_path);
        }

        $projectFile->delete();

        return response()->json(['message' => 'فایل با موفقیت حذف شد']);
    }

    /**
     * Download a project file.
     */
    public function download(Project $project, ProjectFile $projectFile)
    {
        // Check if user is a member of the project
        $user = auth()->user();
        $isMember = $project->users()->where('users.id', $user->id)->exists();
        
        if (!$isMember) {
            abort(403, 'شما عضو این پروژه نیستید');
        }

        // Check if file belongs to this project
        if ($projectFile->project_id !== $project->id) {
            abort(404, 'فایل متعلق به این پروژه نیست');
        }

        $filePath = storage_path('app/public/' . $projectFile->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'فایل یافت نشد');
        }

        return response()->download($filePath, $projectFile->original_name);
    }
}
