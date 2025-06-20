<x-guest-layout>
    <h1 class="text-2xl text-center">Reset Password</h1>
    <div class="text-center text-sm text-slate-500">Enter your new password below.</div>

    <form method="POST" action="{{ route('password.store') }}" class="mt-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mt-5 space-y-4">
            <!-- Email Address -->
            <div class="col-span-3">
                <label for="email" class="block text-sm leading-6 text-gray-900">Email</label>
                <div>
                    <input id="email"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300 bg-gray-50"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autofocus
                        autocomplete="username"
                        readonly />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="col-span-3">
                <label for="password" class="block text-sm leading-6 text-gray-900">New Password</label>
                <div>
                    <input id="password"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div class="col-span-3">
                <label for="password_confirmation" class="block text-sm leading-6 text-gray-900">Confirm New Password</label>
                <div>
                    <input id="password_confirmation"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Reset Password</button>
        </div>

        <!-- Footer Link -->
        <div class="text-center mt-5">
            <div class="text-sm text-slate-500">Remember your password? <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="{{ route('login') }}">Back to sign in</a></div>
        </div>
    </form>
</x-guest-layout>
