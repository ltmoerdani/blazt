<x-guest-layout>
    <h1 class="text-2xl text-center">Confirm Password</h1>
    <div class="text-center text-sm text-slate-500">This is a secure area of the application. Please confirm your password before continuing.</div>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-5">
        @csrf

        <div class="mt-5 space-y-4">
            <!-- Password -->
            <div class="col-span-3">
                <label for="password" class="block text-sm leading-6 text-gray-900">Password</label>
                <div>
                    <input id="password"
                        class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Confirm Password</button>
        </div>
    </form>
</x-guest-layout>
