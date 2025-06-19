<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@blazt.dev'
        ], [
            'name' => 'Admin User',
            'email' => 'admin@blazt.dev',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'subscription_plan' => 'enterprise',
            'subscription_status' => 'active',
            'timezone' => 'Asia/Jakarta',
        ]);

        // Assign admin role
        $admin->assignRole($adminRole);
        
        $this->command->info('Admin user created: admin@blazt.dev / password');
    }
}
