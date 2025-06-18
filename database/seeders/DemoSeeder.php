<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use App\Domain\Campaign\Models\Campaign;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo user
        $user = User::firstOrCreate([
            'email' => 'demo@blazt.app'
        ], [
            'uuid' => Str::uuid(),
            'name' => 'Demo User',
            'email' => 'demo@blazt.app',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'subscription_plan' => 'pro',
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addMonths(12),
            'timezone' => 'Asia/Jakarta',
        ]);

        // Create WhatsApp account
        $whatsappAccount = WhatsAppAccount::firstOrCreate([
            'user_id' => $user->id,
            'phone_number' => '+1234567890'
        ], [
            'user_id' => $user->id,
            'phone_number' => '+1234567890',
            'display_name' => 'Demo WhatsApp Account',
            'status' => 'connected',
            'last_connected_at' => now(),
        ]);

        // Create contact groups
        $customerGroup = ContactGroup::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Customers'
        ], [
            'user_id' => $user->id,
            'name' => 'Customers',
            'description' => 'All customers',
        ]);

        $prospectGroup = ContactGroup::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Prospects'
        ], [
            'user_id' => $user->id,
            'name' => 'Prospects',
            'description' => 'Potential customers',
        ]);

        // Create contacts
        $contacts = [
            [
                'name' => 'John Doe',
                'phone_number' => '+1234567891',
                'group_id' => $customerGroup->id,
            ],
            [
                'name' => 'Jane Smith',
                'phone_number' => '+1234567892',
                'group_id' => $customerGroup->id,
            ],
            [
                'name' => 'Bob Johnson',
                'phone_number' => '+1234567893',
                'group_id' => $prospectGroup->id,
            ],
            [
                'name' => 'Alice Brown',
                'phone_number' => '+1234567894',
                'group_id' => $prospectGroup->id,
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::firstOrCreate([
                'user_id' => $user->id,
                'phone_number' => $contactData['phone_number']
            ], [
                'user_id' => $user->id,
                'name' => $contactData['name'],
                'phone_number' => $contactData['phone_number'],
                'group_id' => $contactData['group_id'],
                'is_active' => true,
            ]);
        }

        // Create demo campaigns
        Campaign::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Welcome Campaign'
        ], [
            'user_id' => $user->id,
            'whatsapp_account_id' => $whatsappAccount->id,
            'name' => 'Welcome Campaign',
            'template_content' => 'Hello {contact_name}! Welcome to our service. We\'re excited to have you!',
            'target_type' => 'group',
            'target_group_id' => $customerGroup->id,
            'status' => 'completed',
            'messages_sent' => 2,
            'messages_delivered' => 2,
            'messages_failed' => 0,
            'total_contacts' => 2,
            'started_at' => now()->subDays(7),
            'completed_at' => now()->subDays(7),
        ]);

        Campaign::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'Product Launch'
        ], [
            'user_id' => $user->id,
            'whatsapp_account_id' => $whatsappAccount->id,
            'name' => 'Product Launch',
            'template_content' => 'Hi {contact_name}! Check out our exciting new product launch. Limited time offer!',
            'target_type' => 'all',
            'status' => 'draft',
            'messages_sent' => 0,
            'messages_delivered' => 0,
            'messages_failed' => 0,
            'total_contacts' => 0,
        ]);

        $this->command->info('Demo data seeded successfully!');
    }
}
