<?php

namespace App\Domain\AI\Services;

use App\Domain\AI\Models\Conversation;
use App\Domain\AI\Models\ConversationMessage;
use Illuminate\Support\Collection;

class ContextManager
{
    public function buildContextFromConversation(Conversation $conversation, int $messageLimit = 10): string
    {
        // Retrieve the latest messages to build context
        $messages = $conversation->messages()
            ->latest('timestamp')
            ->limit($messageLimit)
            ->get()
            ->reverse(); // Reverse to maintain chronological order

        return $this->formatMessagesForContext($messages);
    }

    public function addMessageToContext(): void
    {
        // This method could persist context updates if needed, e.g., in `context_data` JSON column
        // For now, context is built dynamically in `buildContextFromConversation`
    }

    protected function formatMessagesForContext(Collection $messages): string
    {
        return $messages->map(function ($message) {
            // Format each message into a string suitable for AI context
            return sprintf("%s: %s", ucfirst($message->sender_type), $message->message_content);
        })->implode("\n"); // Join messages with a newline
    }

    public function clearContext(Conversation $conversation): bool
    {
        // If context was stored persistently, this would clear it.
        // For now, as context is dynamic, this might not have a direct effect unless `context_data` column is used.
        return $conversation->update(['context_data' => null]);
    }
}
