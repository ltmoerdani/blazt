<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // Create default test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]
        );

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]
        );

        // Assign roles to users using Spatie methods
        $testUser->assignRole('user');
        $adminUser->assignRole('admin');

        // Seed demo data
        $this->call([
            DemoSeeder::class,
        ]);
    }
}
