<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            WhatsApp Status
        </x-slot>
        
        <div class="space-y-4">
            @php
                $status = $this->getWhatsAppStatus();
            @endphp
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if($status['connection_status'] === 'connected')
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        @else
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            WhatsApp Connection
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ ucfirst($status['connection_status']) }}
                        </p>
                    </div>
                </div>
                
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $status['connected_accounts'] }}/{{ $status['total_accounts'] }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Accounts Connected
                    </p>
                </div>
            </div>
            
            @if($status['connection_status'] === 'disconnected')
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                No WhatsApp accounts connected
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Connect your WhatsApp account to start sending messages.</p>
                            </div>
                            <div class="mt-4">
                                <div class="-mx-2 -my-1.5 flex">
                                    <a href="/admin/whats-app-accounts/create" class="bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:bg-yellow-100 dark:hover:bg-yellow-900/50">
                                        Connect WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
