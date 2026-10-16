<section>
    <div class="px-6">
        <h2 class="mb-2 ml-2 font-bold text-black text-2xl">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 ml-2">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <input type="hidden" name="user_type" value="{{ $user->user_type }}" required>
        <input type="hidden" name="company_id" value="{{ $user->company_id }}" required>

        <div class="md:grid grid-cols-2 gap-16 mt-7">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name *')" />
                <x-text-input-2 id="name" name="name" type="text" placeholder="{{ __('name') }}"
                    :value="$user->name" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <!-- First names -->
            <div>
                <x-input-label for="first_name" :value="__('First names *')" />
                <x-text-input-2 id="first_name" name="first_name" type="text" placeholder="{{ __('First names') }}"
                    :value="$user->first_name" required autofocus />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
        </div>

        <div class="md:grid grid-cols-2 gap-16 mt-7">
            <!-- Social Number -->
            <div>
                <x-input-label for="n_social" :value="__('Social Number (SS) *')" />
                <x-text-input-2 id="n_social" name="n_social" type="text" placeholder="{{ __('Social Number') }}"
                    :value="$user->n_social" required autofocus />
                <x-input-error :messages="$errors->get('n_social')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email *')" />
                <x-text-input-2 id="email" name="email" type="email" placeholder="{{ __('alex@gmail.com') }}"
                    :value="$user->email" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="md:grid grid-cols-2 gap-16 mt-7">
            <!-- Avatar -->
            <div>
                <x-input-label for="avatar" :value="__('Photo *')" />
                <x-file-input name="avatar" id="avatar" :value="old('avatar')" accept="image/*" />
                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />

                <!-- Prévisualisation d'image -->
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" id="user-avatar"
                    class="my-5 w-32 h-32 object-cover border border-gray-300 rounded-md">
            </div>
        </div>

        <div class="my-19 flex">
            <x-secondary-button class="flex mr-4 justify-center shadow-md">
                <a href="{{ route('profile.edit') }}">
                    {{ __('Annuler') }}
                </a>
            </x-secondary-button>

            <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                {{ __('Enregistrer') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Récupérer l'élément input file et la div
        const fileInput = document.getElementById('avatar');
        const userAvatar = document.getElementById('user-avatar');

        // Ajouter un écouteur d'événement pour détecter les changements
        fileInput.addEventListener('change', () => {
            // Vérifier si une valeur a été sélectionnée
            if (fileInput.value) {
                userAvatar.classList.add('hidden');
            } else {
                userAvatar.classList.remove('hidden');
            }
        });
    </script>
</section>