<?php

namespace App\Domain\Contact\Services;

use App\Domain\User\Models\User;
use App\Domain\Contact\Models\Contact;
use App\Domain\Contact\Models\ContactGroup;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class SegmentationService
{
    public function createSegment(User $user, string $name, ?string $description = null, array $criteria = []): ContactGroup
    {
        // Criteria could be an array like: [['field' => 'last_message_at', 'operator' => '>', 'value' => '2023-01-01']]
        return $user->contactGroups()->create([
            'name' => $name,
            'description' => $description,
            'criteria' => json_encode($criteria), // Store criteria as JSON
        ]);
        // Optionally, update contact_count based on criteria immediately or via a job
    }

    public function updateSegment(ContactGroup $group, array $data): bool
    {
        if (isset($data['criteria'])) {
            $data['criteria'] = json_encode($data['criteria']);
        }
        return $group->update($data);
    }

    public function getContactsInSegment(ContactGroup $group): Collection
    {
        $query = $group->contacts()->where('is_active', true);

        // Apply dynamic criteria if available
        if ($group->criteria) {
            $criteria = json_decode($group->criteria, true);
            foreach ($criteria as $criterion) {
                if (isset($criterion['field'], $criterion['operator'], $criterion['value'])) {
                    $query->where($criterion['field'], $criterion['operator'], $criterion['value']);
                }
            }
        }

        return $query->get();
    }

    public function refreshSegmentContactsCount(ContactGroup $group): int
    {
        $count = $this->getContactsInSegment($group)->count();
        $group->update(['contact_count' => $count]);
        return $count;
    }
}
