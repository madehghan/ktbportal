<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Customer;
use App\Services\DateConverterService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::with(['users', 'customers'])
            ->latest()
            ->paginate(10);
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $users = User::where('role_id', '!=', null)->get();
        $customers = Customer::all();
        
        return view('projects.create', compact('users', 'customers'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date_jalali' => 'required|string',
            'end_date_jalali' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        // Convert Jalali dates to Gregorian
        try {
            $validated['start_date'] = DateConverterService::jalaliToGregorian($validated['start_date_jalali']);
            $validated['end_date'] = DateConverterService::jalaliToGregorian($validated['end_date_jalali']);
        } catch (\Exception $e) {
            return back()->withErrors(['date' => 'تاریخ وارد شده معتبر نیست'])->withInput();
        }

        $project = Project::create($validated);

        // Attach users (employees) to project
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $project->users()->attach($request->user_ids);
        }

        // Attach customers to project
        if ($request->has('customer_ids') && is_array($request->customer_ids)) {
            $project->customers()->attach($request->customer_ids);
        }

        return redirect()->route('projects.index')->with('success', 'پروژه با موفقیت ایجاد شد');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load(['users', 'customers', 'tasks.completedBy']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $users = User::where('role_id', '!=', null)->get();
        $customers = Customer::all();
        $project->load(['users', 'customers']);
        
        // Convert Gregorian to Jalali if Jalali dates are not set
        if (empty($project->start_date_jalali) && $project->start_date) {
            $project->start_date_jalali = DateConverterService::gregorianToJalali($project->start_date->format('Y-m-d'));
        }
        if (empty($project->end_date_jalali) && $project->end_date) {
            $project->end_date_jalali = DateConverterService::gregorianToJalali($project->end_date->format('Y-m-d'));
        }
        
        return view('projects.edit', compact('project', 'users', 'customers'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date_jalali' => 'required|string',
            'end_date_jalali' => 'required|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        // Convert Jalali dates to Gregorian
        try {
            $validated['start_date'] = DateConverterService::jalaliToGregorian($validated['start_date_jalali']);
            $validated['end_date'] = DateConverterService::jalaliToGregorian($validated['end_date_jalali']);
        } catch (\Exception $e) {
            return back()->withErrors(['date' => 'تاریخ وارد شده معتبر نیست'])->withInput();
        }

        $project->update($validated);

        // Sync users (employees)
        if ($request->has('user_ids')) {
            $project->users()->sync($request->user_ids ?? []);
        } else {
            $project->users()->detach();
        }

        // Sync customers
        if ($request->has('customer_ids')) {
            $project->customers()->sync($request->customer_ids ?? []);
        } else {
            $project->customers()->detach();
        }

        return redirect()->route('projects.index')->with('success', 'پروژه با موفقیت به‌روزرسانی شد');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'پروژه با موفقیت حذف شد');
    }
}
