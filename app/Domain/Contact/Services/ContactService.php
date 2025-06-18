<?php

namespace App\Domain\Contact\Services;

use App\Domain\User\Models\User;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use Illuminate\Support\Facades\DB;
use Exception;

class ContactService
{
    public function createContact(User $user, array $data): Contact
    {
        return DB::transaction(function () use ($user, $data) {
            $contact = $user->contacts()->create([
                'phone_number' => $data['phone_number'],
                'name' => $data['name'] ?? null,
                'group_id' => $data['group_id'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if ($contact->group_id) {
                $contact->group->increment('contact_count');
            }

            return $contact;
        });
    }

    public function updateContact(Contact $contact, array $data): bool
    {
        return DB::transaction(function () use ($contact, $data) {
            $oldGroupId = $contact->group_id;
            $updated = $contact->update([
                'phone_number' => $data['phone_number'] ?? $contact->phone_number,
                'name' => $data['name'] ?? $contact->name,
                'group_id' => $data['group_id'] ?? $contact->group_id,
                'is_active' => $data['is_active'] ?? $contact->is_active,
            ]);

            if ($updated) {
                if ($oldGroupId && $oldGroupId !== $contact->group_id) {
                    ContactGroup::find($oldGroupId)->decrement('contact_count');
                }
                if ($contact->group_id && $oldGroupId !== $contact->group_id) {
                    $contact->group->increment('contact_count');
                }
            }

            return $updated;
        });
    }

    public function deleteContact(Contact $contact): ?bool
    {
        return DB::transaction(function () use ($contact) {
            if ($contact->group_id) {
                $contact->group->decrement('contact_count');
            }
            return $contact->delete();
        });
    }

    public function createGroup(User $user, array $data): ContactGroup
    {
        return $user->contactGroups()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function updateGroup(ContactGroup $group, array $data): bool
    {
        return $group->update([
            'name' => $data['name'] ?? $group->name,
            'description' => $data['description'] ?? $group->description,
        ]);
    }

    public function deleteGroup(ContactGroup $group): ?bool
    {
        return $group->delete();
    }
}
