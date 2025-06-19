<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            [
                'name' => 'admin',
                'guard_name' => 'web'
            ]
        );

        // Create User Role
        $userRole = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            [
                'name' => 'user',
                'guard_name' => 'web'
            ]
        );

        // Assign all permissions to Admin
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        // Assign basic permissions to User
        $userPermissions = Permission::whereIn('name', [
            'dashboard.view',
            'whatsapp.connect',
            'whatsapp.send',
            'whatsapp.view',
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',
            'contacts.import',
            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
            'analytics.view'
        ])->get();

        $userRole->syncPermissions($userPermissions);
    }
}
