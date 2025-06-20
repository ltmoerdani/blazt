<!-- Sidebar component -->
<div class="flex flex-col h-0 flex-1 bg-white dark:bg-gray-800">
    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
        <div class="flex items-center flex-shrink-0 px-4">
            <img class="h-8 w-auto" src="{{ asset('images/logo.svg') }}" alt="BlazT">
            <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">BlazT</span>
        </div>
        
        <nav class="mt-5 flex-1 px-2 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('dashboard')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                </svg>
                Dashboard
            </a>

            <!-- WhatsApp Accounts -->
            <a href="{{ route('whatsapp-accounts.index') }}" class="@if(request()->routeIs('whatsapp-accounts.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('whatsapp-accounts.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                WhatsApp Accounts
            </a>

            <!-- Campaigns -->
            <a href="{{ route('campaigns.index') }}" class="@if(request()->routeIs('campaigns.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('campaigns.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                Campaigns
            </a>

            <!-- Contacts -->
            <a href="{{ route('contacts.index') }}" class="@if(request()->routeIs('contacts.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('contacts.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Contacts
            </a>

            <!-- Messages -->
            <a href="{{ route('messages.index') }}" class="@if(request()->routeIs('messages.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('messages.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                Messages
            </a>

            <!-- Analytics -->
            <a href="{{ route('analytics.index') }}" class="@if(request()->routeIs('analytics.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                <svg class="@if(request()->routeIs('analytics.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Analytics
            </a>

            <div class="mt-6">
                <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Settings
                </h3>
                
                <!-- AI Configuration -->
                <a href="{{ route('ai.index') }}" class="@if(request()->routeIs('ai.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md mt-1">
                    <svg class="@if(request()->routeIs('ai.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    AI Configuration
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}" class="@if(request()->routeIs('profile.*')) bg-indigo-100 dark:bg-indigo-900 border-r-4 border-indigo-500 text-indigo-700 dark:text-indigo-300 @else text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white @endif group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                    <svg class="@if(request()->routeIs('profile.*')) text-indigo-500 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                </a>
            </div>
        </nav>
    </div>
    
    <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center">
            <div>
                <img class="inline-block h-9 w-9 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>
</div>
