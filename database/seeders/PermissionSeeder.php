<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management Permissions
            [
                'name' => 'users.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'users.delete',
                'guard_name' => 'web'
            ],
            [
                'name' => 'roles.manage',
                'guard_name' => 'web'
            ],

            // WhatsApp Permissions
            [
                'name' => 'whatsapp.connect',
                'guard_name' => 'web'
            ],
            [
                'name' => 'whatsapp.send',
                'guard_name' => 'web'
            ],
            [
                'name' => 'whatsapp.view',
                'guard_name' => 'web'
            ],

            // Contact Management Permissions
            [
                'name' => 'contacts.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'contacts.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'contacts.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'contacts.delete',
                'guard_name' => 'web'
            ],
            [
                'name' => 'contacts.import',
                'guard_name' => 'web'
            ],

            // Campaign Permissions (for future phases)
            [
                'name' => 'campaigns.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'campaigns.create',
                'guard_name' => 'web'
            ],
            [
                'name' => 'campaigns.edit',
                'guard_name' => 'web'
            ],
            [
                'name' => 'campaigns.delete',
                'guard_name' => 'web'
            ],

            // System Permissions
            [
                'name' => 'dashboard.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'analytics.view',
                'guard_name' => 'web'
            ],
            [
                'name' => 'system.settings',
                'guard_name' => 'web'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                $permission
            );
        }
    }
}
