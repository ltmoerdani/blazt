<div x-data="{
        accountId: '{{ $record->id }}',
        recordId: {{ $record->id }},
        qrCode: null,
        loading: true,
        error: false,
        errorMessage: '',
        status: 'loading',
        statusText: 'Loading...',
        countdown: 60,
        refreshInterval: null,
        statusInterval: null,

        async init() {
            await this.loadQRCode();
            this.startStatusCheck();
        },

        async loadQRCode() {
            this.loading = true;
            this.error = false;

            try {
                const response = await fetch(`/admin/whatsapp/qr-account/${this.recordId}`);
                const data = await response.json();

                if (data.success) {
                    if (data.connected) {
                        this.status = 'connected';
                        this.statusText = 'Connected';
                        this.stopIntervals(); // Stop all polling immediately
                        return; // Exit early, no need to load QR code
                    } else if (data.qr_code) {
                        this.qrCode = data.qr_code;
                        this.status = 'connecting';
                        this.statusText = 'Waiting for scan...';
                        this.startCountdown();
                    } else {
                        throw new Error('QR code not available');
                    }
                } else {
                    throw new Error(data.error || 'Failed to load QR code');
                }
            } catch (error) {
                this.error = true;
                this.errorMessage = error.message;
                this.status = 'disconnected';
                this.statusText = 'Error';
            } finally {
                this.loading = false;
            }
        },

        async checkStatus() {
            try {
                const response = await fetch(`/admin/whatsapp/qr-account/${this.recordId}`);
                const data = await response.json();

                if (data.success) {
                    if (data.connected) {
                        this.status = 'connected';
                        this.statusText = 'Connected';
                        this.stopIntervals(); // Stop all polling immediately
                        await this.updateRecordStatus('connected');
                        return; // Exit early to prevent further polling
                    } else if (data.status && data.status.hasQR) {
                        this.status = 'connecting';
                        this.statusText = 'Waiting for scan...';
                    } else {
                        this.status = 'disconnected';
                        this.statusText = 'Disconnected';
                    }
                } else {
                    this.status = 'disconnected';
                    this.statusText = 'Service unavailable';
                }
            } catch (error) {
                this.status = 'disconnected';
                this.statusText = 'Connection error';
            }
        },

        async updateRecordStatus(newStatus) {
            try {
                const csrfToken = '{{ csrf_token() }}';
                
                const response = await fetch(`/admin/whatsapp/update-status/${this.recordId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to update record status');
                }
            } catch (error) {
                // Silent fail for status update
            }
        },

        async refreshQR() {
            // Don't refresh if already connected
            if (this.status === 'connected') {
                return;
            }
            await this.loadQRCode();
        },

        startCountdown() {
            this.countdown = 60;
            if (this.refreshInterval) clearInterval(this.refreshInterval);
            
            // Only start countdown if not connected
            if (this.status !== 'connected') {
                this.refreshInterval = setInterval(() => {
                    // Check status before decrementing countdown
                    if (this.status === 'connected') {
                        this.stopIntervals();
                        return;
                    }
                    
                    this.countdown--;
                    if (this.countdown <= 0) {
                        // Only reload QR if still not connected
                        if (this.status !== 'connected') {
                            this.loadQRCode();
                        }
                    }
                }, 1000);
            }
        },

        startStatusCheck() {
            if (this.statusInterval) clearInterval(this.statusInterval);
            
            // Only start status checking if not already connected
            if (this.status !== 'connected') {
                this.statusInterval = setInterval(() => {
                    // Double check status before each poll
                    if (this.status !== 'connected') {
                        this.checkStatus();
                    } else {
                        // Stop polling if somehow status changed to connected
                        this.stopIntervals();
                    }
                }, 3000);
            }
        },

        stopIntervals() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
            if (this.statusInterval) {
                clearInterval(this.statusInterval);
                this.statusInterval = null;
            }
        }
    }"
    x-init="init()"
    class="flex flex-col items-center space-y-4 p-6">
    <div class="text-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
            Connect WhatsApp Account
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Scan the QR code below with your WhatsApp mobile app
        </p>
    </div>

    <!-- Status indicator -->
    <div class="flex items-center space-x-2 mb-4">
        <div
            x-bind:class="{
                'bg-yellow-100 text-yellow-800': status === 'connecting',
                'bg-green-100 text-green-800': status === 'connected',
                'bg-red-100 text-red-800': status === 'disconnected',
                'bg-gray-100 text-gray-800': status === 'loading'
            }"
            class="px-3 py-1 rounded-full text-xs font-medium"
        >
            <span x-text="statusText"></span>
        </div>
        <div
            x-show="status === 'loading' || status === 'connecting'"
            class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"
        ></div>
    </div>

    <!-- QR Code display area -->
    <div class="bg-white p-6 rounded-lg shadow-sm border-2 border-gray-200 min-h-[300px] flex items-center justify-center">
        <!-- Loading state -->
        <div x-show="loading" class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Generating QR code...</p>
        </div>

        <!-- QR Code -->
        <div x-show="!loading && qrCode && status !== 'connected'" class="text-center">
            <img
                x-bind:src="qrCode"
                alt="WhatsApp QR Code"
                class="w-64 h-64 object-contain mx-auto"
                style="image-rendering: pixelated;"
            />
            <div class="mt-3 text-xs text-gray-500">
                <p>QR Code expires in <span x-text="countdown"></span> seconds</p>
                <p class="mt-1">Will auto-refresh when expired</p>
            </div>
        </div>

        <!-- Connected state -->
        <div x-show="status === 'connected'" class="text-center">
            <div class="text-green-600 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h4 class="text-lg font-semibold text-green-800 mb-2">Successfully Connected!</h4>
            <p class="text-gray-600 text-sm">Your WhatsApp account is now connected and ready to use.</p>
        </div>

        <!-- Error state -->
        <div x-show="error" class="text-center text-red-600">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="font-medium mb-2">Connection Error</p>
            <p class="text-sm" x-text="errorMessage"></p>
            <button @click="loadQRCode()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded text-sm">
                Retry
            </button>
        </div>
    </div>

    <!-- Control buttons -->
    <div class="flex space-x-3">
        <button
            x-show="status !== 'connected'"
            @click="refreshQR()"
            :disabled="loading"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
        >
            <span x-show="!loading">Refresh QR Code</span>
            <span x-show="loading">Refreshing...</span>
        </button>

        <button
            @click="checkStatus()"
            :disabled="loading"
            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium"
        >
            Check Status
        </button>
    </div>

    <!-- Instructions -->
    <div class="text-xs text-gray-500 dark:text-gray-400 text-center space-y-1 max-w-md">
        <div class="flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Open WhatsApp on your phone → Settings → Linked Devices → Link a Device</span>
        </div>
        <p>Make sure your phone has internet connection and WhatsApp is up to date</p>
    </div>
</div>
