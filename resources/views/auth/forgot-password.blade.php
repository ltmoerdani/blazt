<x-guest-layout>
    <h1 class="text-2xl text-center">Forgot your password?</h1>
    <div class="text-center text-sm text-slate-500">No problem. Just let us know your email address and we will email you a password reset link.</div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-5">
        @csrf

        <div class="mt-5 space-y-4">
            <!-- Email Address -->
            <div class="col-span-3">
                <label for="email" class="block text-sm leading-6 text-gray-900">Email</label>
                <div>
                    <input id="email"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Email Password Reset Link</button>
        </div>

        <!-- Footer Link -->
        <div class="text-center mt-5">
            <div class="text-sm text-slate-500">Remember your password? <a class="text-sm text-primary-600 border-b hover:border-gray-500" href="{{ route('login') }}">Back to sign in</a></div>
        </div>
    </form>
</x-guest-layout>
