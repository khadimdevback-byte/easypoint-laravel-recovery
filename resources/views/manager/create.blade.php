<x-app-layout>

    <!-- cards -->
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">

        <div class="px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                {{ __('Adding a Manager') }}
                </h1>
        </div>

        <div class=" my-10 px-6 py-6">
            <!-- card 1 -->

            <form method="POST" action="{{ route('super-admin.store') }}">
                @csrf

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name *')" />
                        <x-text-input-2 id="name" name="name" type="text" placeholder="{{ __('name') }}"
                            :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <!-- First names -->
                    <div>
                        <x-input-label for="first_name" :value="__('First names *')" />
                        <x-text-input-2 id="first_name" name="first_name" type="text"
                            placeholder="{{ __('First names') }}" :value="old('first_name')" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Email Address -->
                    <div>
                        <x-input-label for="n_social" :value="__('Social Number (SS) *')" />
                        <x-text-input-2 id="n_social" name="n_social" type="text"
                            placeholder="{{ __('Social Number') }}" :value="old('n_social')" required autofocus />
                        <x-input-error :messages="$errors->get('n_social')" class="mt-2" />
                    </div>
                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone *')" />
                        <x-text-input-2 id="phone" name="phone" type="text" placeholder="{{ __('Phone') }}"
                            :value="old('phone')" required autofocus />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input-2 id="email" name="email" type="text"
                            placeholder="{{ __('alex@gmail.com') }}" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password *')" />
                        <x-text-input-2 id="password" name="password" type="text" placeholder="{{ __('Password') }}"
                            :value="old('password')" required autofocus />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <!-- Password confirmation -->
                    <div>
                        <x-input-label for="confirm_password" :value="__('Password confirmation *')" />
                        <x-text-input-2 id="confirm_password" name="confirm_password" type="text"
                            placeholder="{{ __('Password') }}" :value="old('confirm_password')" required autofocus />
                        <x-input-error :messages="$errors->get('confirm_password')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-10">

                    <x-primary-button class=" flex justify-center shadow-md shadow-green-600/50">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>

        </div>

    </div>
    <!-- end cards -->

</x-app-layout>
