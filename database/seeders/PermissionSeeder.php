<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'dashboard',
                'display_name' => 'داشبورد',
                'route_name' => 'dashboard',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>',
                'order' => 1,
            ],
            [
                'name' => 'users.index',
                'display_name' => 'مدیریت کاربران',
                'route_name' => 'users.index',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
                'order' => 2,
            ],
            [
                'name' => 'users.create',
                'display_name' => 'افزودن کاربر',
                'route_name' => 'users.create',
                'icon' => null,
                'order' => 3,
            ],
            [
                'name' => 'users.edit',
                'display_name' => 'ویرایش کاربر',
                'route_name' => 'users.edit',
                'icon' => null,
                'order' => 4,
            ],
            [
                'name' => 'users.delete',
                'display_name' => 'حذف کاربر',
                'route_name' => 'users.destroy',
                'icon' => null,
                'order' => 5,
            ],
            [
                'name' => 'customers.index',
                'display_name' => 'مدیریت مشتریان',
                'route_name' => 'customers.index',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
                'order' => 6,
            ],
            [
                'name' => 'customers.create',
                'display_name' => 'افزودن مشتری',
                'route_name' => 'customers.create',
                'icon' => null,
                'order' => 7,
            ],
            [
                'name' => 'customers.edit',
                'display_name' => 'ویرایش مشتری',
                'route_name' => 'customers.edit',
                'icon' => null,
                'order' => 8,
            ],
            [
                'name' => 'customers.delete',
                'display_name' => 'حذف مشتری',
                'route_name' => 'customers.destroy',
                'icon' => null,
                'order' => 9,
            ],
            [
                'name' => 'projects.index',
                'display_name' => 'مدیریت پروژه‌ها',
                'route_name' => 'projects.index',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>',
                'order' => 10,
            ],
            [
                'name' => 'projects.create',
                'display_name' => 'افزودن پروژه',
                'route_name' => 'projects.create',
                'icon' => null,
                'order' => 11,
            ],
            [
                'name' => 'projects.edit',
                'display_name' => 'ویرایش پروژه',
                'route_name' => 'projects.edit',
                'icon' => null,
                'order' => 12,
            ],
            [
                'name' => 'projects.delete',
                'display_name' => 'حذف پروژه',
                'route_name' => 'projects.destroy',
                'icon' => null,
                'order' => 13,
            ],
            [
                'name' => 'roles.index',
                'display_name' => 'مدیریت نقش‌ها',
                'route_name' => 'roles.index',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
                'order' => 14,
            ],
            [
                'name' => 'roles.create',
                'display_name' => 'افزودن نقش',
                'route_name' => 'roles.create',
                'icon' => null,
                'order' => 15,
            ],
            [
                'name' => 'roles.edit',
                'display_name' => 'ویرایش نقش',
                'route_name' => 'roles.edit',
                'icon' => null,
                'order' => 16,
            ],
            [
                'name' => 'roles.delete',
                'display_name' => 'حذف نقش',
                'route_name' => 'roles.destroy',
                'icon' => null,
                'order' => 17,
            ],
            [
                'name' => 'sms-logs.index',
                'display_name' => 'اطلاع رسانی پیامکی',
                'route_name' => 'sms-logs.index',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
                'order' => 18,
            ],
            [
                'name' => 'profile.edit',
                'display_name' => 'پروفایل',
                'route_name' => 'profile.edit',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
                'order' => 19,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}

