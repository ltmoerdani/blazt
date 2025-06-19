<x-guest-layout>
    <h1 class="text-2xl text-center">Verify Your Email</h1>
    <div class="text-center text-sm text-slate-500">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="mt-5 space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">Resend Verification Email</button>
        </form>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-slate-500 hover:underline">Log Out</button>
            </form>
        </div>
    </div>
</x-guest-layout>
