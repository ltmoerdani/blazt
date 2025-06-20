<div class="flex flex-col items-center space-y-4 p-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        Scan QR Code to Connect WhatsApp
    </h3>
    
    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
        Open WhatsApp on your phone and scan this QR code to connect your account.
    </p>
    
    @if($qrCodePath)
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <img
                src="{{ asset('storage/' . $qrCodePath) }}"
                alt="WhatsApp QR Code"
                class="w-64 h-64 object-contain"
                style="image-rendering: pixelated;"
            />
        </div>
        
        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
            <p>QR Code will expire in 1 minute.</p>
            <p>Refresh the page if the QR code expires.</p>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400">
            <p>QR Code is being generated...</p>
            <p class="text-xs mt-2">Please wait a moment and try again.</p>
        </div>
    @endif
    
    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Make sure your phone has internet connection</span>
    </div>
</div>
