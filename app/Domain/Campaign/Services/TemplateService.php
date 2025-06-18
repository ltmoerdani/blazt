<?php

namespace App\Domain\Campaign\Services;

use App\Domain\User\Models\User;
use App\Domain\Campaign\Models\MessageTemplate;
use Exception;

class TemplateService
{
    public function createTemplate(User $user, array $data): MessageTemplate
    {
        return $user->messageTemplates()->create([
            'name' => $data['name'],
            'content' => $data['content'],
            'category' => $data['category'] ?? 'general',
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function updateTemplate(MessageTemplate $template, array $data): bool
    {
        return $template->update([
            'name' => $data['name'] ?? $template->name,
            'content' => $data['content'] ?? $template->content,
            'category' => $data['category'] ?? $template->category,
            'status' => $data['status'] ?? $template->status,
        ]);
    }

    public function deleteTemplate(MessageTemplate $template): ?bool
    {
        return $template->delete();
    }

    public function getTemplatesByUser(User $user, ?string $category = null)
    {
        $query = $user->messageTemplates();

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get();
    }
}
