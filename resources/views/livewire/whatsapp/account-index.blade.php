<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('WhatsApp Accounts') }}
            </h2>
            <button wire:click="showConnectModal" 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>{{ __('Connect WhatsApp') }}</span>
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <!-- Accounts Grid -->
            @if(count($accounts) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($accounts as $account)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="p-6">
                                <!-- Account Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.309"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $account->name ?: 'WhatsApp Account' }}
                                            </h3>
                                            @if($account->phone_number)
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $account->phone_number }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($account->status === 'connected') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($account->status === 'connecting') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Account Details -->
                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    @if($account->connected_at)
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Connected: {{ $account->connected_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-1h8v1z"></path>
                                        </svg>
                                        <span>Created: {{ $account->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    @if($account->status === 'connected')
                                        <button wire:click="disconnectAccount({{ $account->id }})" 
                                                wire:confirm="Are you sure you want to disconnect this WhatsApp account?"
                                                class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                                            Disconnect
                                        </button>
                                    @else
                                        <button wire:click="startConnection({{ $account->id }})" 
                                                class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                                            Connect
                                        </button>
                                    @endif
                                    
                                    <button wire:click="deleteAccount({{ $account->id }})" 
                                            wire:confirm="Are you sure you want to delete this account? This action cannot be undone."
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No WhatsApp accounts</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by connecting your first WhatsApp account.</p>
                        <div class="mt-6">
                            <button wire:click="showConnectModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Connect WhatsApp Account
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Connect Modal -->
    <div x-data="{ show: @entangle('showConnectModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         style="display: none;">
        
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Connect WhatsApp Account</h3>
                <button wire:click="hideConnectModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="mt-4">
                @if($connectionStatus === 'connecting')
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">Initializing WhatsApp connection...</p>
                    </div>
                @elseif($connectionStatus === 'qr_ready' && $qrCode)
                    <div class="text-center">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Scan QR Code</h4>
                        <div class="bg-white p-4 rounded-lg inline-block border">
                            <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" class="w-64 h-64 mx-auto">
                        </div>
                        <div class="mt-4 space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                1. Open WhatsApp on your phone<br>
                                2. Go to Settings > Linked Devices<br>
                                3. Tap "Link a Device"<br>
                                4. Scan this QR code
                            </p>
                            <button wire:click="refreshQrCode" 
                                    class="mt-4 text-blue-600 hover:text-blue-800 text-sm underline">
                                Refresh QR Code
                            </button>
                        </div>
                    </div>
                @elseif($connectionStatus === 'connected')
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Connected Successfully!</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Your WhatsApp account has been connected.</p>
                    </div>
                @elseif($connectionStatus === 'error')
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Connection Failed</h4>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            @error('connection') {{ $message }} @enderror
                        </p>
                        <button wire:click="startConnection" 
                                class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Try Again
                        </button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Ready to connect a new WhatsApp account?</p>
                        <button wire:click="startConnection" 
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Start Connection
                        </button>
                    </div>
                @endif
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                <button wire:click="hideConnectModal" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Close
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh QR code every 30 seconds
        let qrRefreshInterval;
        
        Livewire.on('qrCodeUpdated', () => {
            clearInterval(qrRefreshInterval);
            qrRefreshInterval = setInterval(() => {
                if (@js($connectionStatus) === 'qr_ready') {
                    @this.refreshQrCode();
                }
            }, 30000);
        });
        
        // Clear interval when modal is closed
        Livewire.on('modalClosed', () => {
            clearInterval(qrRefreshInterval);
        });
    </script>
    @endpush
</x-app-layout>
