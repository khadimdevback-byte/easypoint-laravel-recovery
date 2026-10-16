<x-guest-layout>
    <div class="mb-4 text-md text-black">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="w-full">
        @csrf

        <!-- Email Address -->
        <div class="my-10">
            <x-input-label for="email" :value="__('Email')" />
            <input id="email"
                class="block w-full bg-white border drop-shadow-sm border-transparent focus:border-transparent rounded-md  shadow-md mt-2  focus:ring-0  border-none py-3  px-4"
                type="email" name="email" :value="old('email')" placeholder="{{ __('alex@gmail.com') }}" required
                autofocus />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
