<x-guest-layout>
    <h1 class="text-2xl text-center">Create your account</h1>
    <div class="text-center text-sm text-slate-500">Already have an account? <a class="text-sm text-primary-600 border-b hover:border-gray-500" href="{{ route('login') }}">Sign in here</a></div>

    <form method="POST" action="{{ route('register') }}" class="mt-5">
        @csrf
        
        <div class="mt-5 space-y-4">
            <!-- Name -->
            <div class="col-span-3">
                <label for="name" class="block text-sm leading-6 text-gray-900">Name</label>
                <div>
                    <input id="name"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

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
                        autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Timezone -->
            <div class="col-span-3">
                <label for="timezone" class="block text-sm leading-6 text-gray-900">Timezone</label>
                <div>
                    <select id="timezone"
                        name="timezone"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        required>
                        <option value="">Select your timezone</option>
                        <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                        <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                        <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                        <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                        <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                        <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                        <option value="Asia/Jakarta" {{ old('timezone') == 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Asia/Tokyo" {{ old('timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                    </select>
                </div>
                <x-input-error :messages="$errors->get('timezone')" class="mt-1" />
            </div>

            <!-- Password -->
            <div class="col-span-3">
                <label for="password" class="block text-sm leading-6 text-gray-900">Password</label>
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
                <label for="password_confirmation" class="block text-sm leading-6 text-gray-900">Confirm Password</label>
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
            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Create your account</button>
        </div>
    </form>
</x-guest-layout>
