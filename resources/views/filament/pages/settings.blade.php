<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Profile Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                Profile Information
            </x-slot>
            
            <x-slot name="description">
                Update your account's profile information and email address.
            </x-slot>

            <x-filament-panels::form wire:submit="updateProfile">
                {{ $this->profileForm }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit">
                        Update Profile
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </x-filament::section>

        {{-- Password Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                Update Password
            </x-slot>
            
            <x-slot name="description">
                Ensure your account is using a long, random password to stay secure.
            </x-slot>

            <x-filament-panels::form wire:submit="updatePassword">
                {{ $this->passwordForm }}
                
                <div class="flex justify-end">
                    <x-filament::button type="submit" color="warning">
                        Update Password
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </x-filament::section>

        {{-- Subscription Information --}}
        <x-filament::section>
            <x-slot name="heading">
                Subscription Information
            </x-slot>
            
            <x-slot name="description">
                Your current subscription plan and usage limits.
            </x-slot>

            <div class="space-y-4">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Plan</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 capitalize">
                            {{ auth()->user()->subscription_plan }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->subscription_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst(auth()->user()->subscription_status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
                
                @if(auth()->user()->subscription_expires_at)
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expires At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ auth()->user()->subscription_expires_at->format('M d, Y') }}
                        </dd>
                    </dl>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
