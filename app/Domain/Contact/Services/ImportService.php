<?php

namespace App\Domain\Contact\Services;

use App\Domain\User\Models\User;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportService
{
    public function importContacts(User $user, array $contactsData, ?int $groupId = null): array
    {
        $importedContacts = [];
        DB::beginTransaction();
        try {
            foreach ($contactsData as $data) {
                $contact = $user->contacts()->firstOrCreate(
                    ['phone_number' => $data['phone_number']],
                    [
                        'name' => $data['name'] ?? null,
                        'group_id' => $groupId,
                        'is_active' => $data['is_active'] ?? true,
                    ]
                );
                $importedContacts[] = $contact;
            }

            if ($groupId) {
                ContactGroup::find($groupId)->increment('contact_count', count($importedContacts));
            }

            DB::commit();
            Log::info('Successfully imported ' . count($importedContacts) . ' contacts for user ' . $user->id);
            return $importedContacts;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error importing contacts for user ' . $user->id . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
