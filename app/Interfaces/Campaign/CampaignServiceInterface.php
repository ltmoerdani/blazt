<?php

namespace App\Interfaces\Campaign;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\Campaign;

interface CampaignServiceInterface
{
    public function createCampaign(User $user, array $data): Campaign;
    public function updateCampaign(Campaign $campaign, array $data): bool;
    public function executeCampaign(Campaign $campaign): bool;
    public function deleteCampaign(Campaign $campaign): bool;
    public function getCampaignStats(Campaign $campaign): array;
}
