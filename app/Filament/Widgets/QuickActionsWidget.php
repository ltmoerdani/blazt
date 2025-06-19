<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getActions(): array
    {
        return [
            [
                'label' => 'Connect WhatsApp',
                'url' => '/admin/whats-app-accounts/create',
                'icon' => 'heroicon-o-plus',
                'color' => 'primary',
                'description' => 'Add a new WhatsApp account'
            ],
            [
                'label' => 'Add Contact',
                'url' => '/admin/contacts/create',
                'icon' => 'heroicon-o-user-plus',
                'color' => 'success',
                'description' => 'Add a new contact to your database'
            ],
            [
                'label' => 'Send Message',
                'url' => '#', // Will be implemented in messaging phase
                'icon' => 'heroicon-o-chat-bubble-left',
                'color' => 'warning',
                'description' => 'Send a message to your contacts'
            ],
            [
                'label' => 'View Analytics',
                'url' => '#', // Will be implemented in analytics phase
                'icon' => 'heroicon-o-chart-bar',
                'color' => 'info',
                'description' => 'View your messaging analytics'
            ]
        ];
    }
    
    public function getColorClasses(string $color): string
    {
        return match($color) {
            'primary' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
            'success' => 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400',
            'warning' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400',
            'info' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
            default => 'bg-gray-50 text-gray-700 dark:bg-gray-900/20 dark:text-gray-400',
        };
    }
}
