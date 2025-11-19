<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $menuItems = collect();

        if (Auth::check() && Auth::user()->role) {
            // Get user's permissions that have icons (menu items)
            $menuItems = Auth::user()->role->permissions()
                ->whereNotNull('icon')
                ->orderBy('order')
                ->get()
                ->map(function ($permission) {
                    return [
                        'name' => $permission->display_name,
                        'route' => $permission->route_name,
                        'icon' => $permission->icon,
                        'active' => request()->routeIs($permission->route_name . '*'),
                    ];
                });
        }

        // Get projects and users for task creation modal
        $projects = Project::orderBy('name')->get(['id', 'name']);
        $users = User::where('role_id', '!=', null)->orderBy('name')->get(['id', 'name']);

        $view->with([
            'sidebarMenuItems' => $menuItems,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}

