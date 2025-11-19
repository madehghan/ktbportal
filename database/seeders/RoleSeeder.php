<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role - Full Access
        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'مدیر سیستم',
                'description' => 'دسترسی کامل به تمام بخش‌های سیستم',
            ]
        );

        // Attach all permissions to admin
        $allPermissions = Permission::all();
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // Manager Role - Limited Access
        $manager = Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'مدیر',
                'description' => 'دسترسی به مدیریت کاربران و مشاهده داشبورد',
            ]
        );

        // Attach specific permissions to manager
        $managerPermissions = Permission::whereIn('name', [
            'dashboard',
            'users.index',
            'users.create',
            'users.edit',
            'customers.index',
            'customers.create',
            'customers.edit',
            'profile.edit',
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);

        // Employee Role - Basic Access
        $employee = Role::updateOrCreate(
            ['name' => 'employee'],
            [
                'display_name' => 'کارمند',
                'description' => 'دسترسی محدود به داشبورد و پروفایل شخصی',
            ]
        );

        // Attach basic permissions to employee
        $employeePermissions = Permission::whereIn('name', [
            'dashboard',
            'profile.edit',
        ])->pluck('id');
        $employee->permissions()->sync($employeePermissions);
    }
}

