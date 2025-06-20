<x-guest-layout>
    <h1 class="text-2xl text-center">Login to your account</h1>
    <div class="text-center text-sm text-slate-500">Don't have an account? <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="{{ route('register') }}">Create one here</a></div>
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-5">
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

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-5">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="remember"
                        aria-describedby="remember"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                </div>
                <div class="ml-3 text-sm">
                    <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                </div>
            </div>
            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Login to your account</button>
        </div>
    </form>
</x-guest-layout>
