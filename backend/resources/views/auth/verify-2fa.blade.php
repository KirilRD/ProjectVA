<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('We have sent a 6-digit verification code to your email. Enter it below.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('verify-2fa.store') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Verification code')" />
            <x-text-input
                id="code"
                class="block mt-1 w-full text-center text-lg tracking-[0.5em]"
                type="text"
                name="code"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                autocomplete="one-time-code"
                autofocus
                placeholder="000000"
                :value="old('code')"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Use a different account') }}
                </button>
            </form>
            <x-primary-button type="submit">
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
