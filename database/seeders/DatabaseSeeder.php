<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed permissions and roles
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            BotSeeder::class,
        ]);

        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Create admin user
        User::factory()->create([
            'name' => 'Karmania Admin',
            'email' => 'admin@karmania.local',
            'mobile' => '09123456789',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);
    }
}
