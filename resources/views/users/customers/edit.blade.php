<x-app-layout>

    @php
        $userTypesTitle = [
            'manager' => __('Modifier un Gestionnaire'),
            'controller' => __('Modifier un contrôleur'),
            'user' => __('Modifier un employé'),
            'customer' => __('Modifier le client'),
        ];
    @endphp
    <!-- cards -->
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">

        <div class="px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                {{ $userTypesTitle[$user->user_type] ?? '' }}
                </h6>
        </div>

        @if (session('success'))
            <div id="session-success-alert-div" alert
                class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-primary">
                {{ session('success') }}
                <button type="button" alert-close
                    onclick="document.getElementById('session-success-alert-div').style.display='none'"
                    class="box-content absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4 z-2">
                    <span aria-hidden="true" class="text-center cursor-pointer">&#10005;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div id="session-error-alert-div" alert
                class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-danger">
                {{ session('error') }}
                <button type="button" alert-close
                    onclick="document.getElementById('session-error-alert-div').style.display='none'"
                    class="box-content absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4 z-2">
                    <span aria-hidden="true" class="text-center cursor-pointer">&#10005;</span>
                </button>
            </div>
        @endif

        <div class=" my-10 px-6 py-6">
            <!-- card 1 -->

            <form method="POST" action="{{ route('users.update', ['user_type' => $user->user_type, 'id' => $user->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <input type="hidden" name="user_type" value="{{ $user->user_type }}" required>
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>
                <input type="hidden" name="status" value="active" required>
                <input type="hidden" name="password" value="activecustomer" required>
                <input type="hidden" name="password_confirmation" value="activecustomer" required>

                <div class="md:grid grid-cols-2 gap-16 my-7">
                    <!-- Social Number -->
                    <div>
                        <x-input-label for="customer_type" :value="__('Type de client *')" />
                        <x-select-input name="customer_type" :options="['physical_person' => 'Personne physique', 'legal_person' => 'Personne morale']" :selected="$user->customer_type" :disabled="false"
                            required />

                        <x-input-error :messages="$errors->get('customer_type')" class="mt-2" />
                    </div>

                </div>

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
                        <x-text-input-2 id="first_name" name="first_name" type="text"
                            placeholder="{{ __('First names') }}" :value="$user->first_name" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Social Number -->
                    <div>
                        <x-input-label for="n_social" :value="__('Social Number (SS)')" />
                        <x-text-input-2 id="n_social" name="n_social" type="text"
                            placeholder="{{ __('Social Number') }}" :value="$user->n_social" autofocus />
                        <x-input-error :messages="$errors->get('n_social')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input-2 id="phone" name="phone" type="text" placeholder="{{ __('Phone') }}"
                            :value="$user->phone" autofocus />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">

                    <div>
                        <x-input-label for="email" :value="__('Email *')" />
                        <x-text-input-2 id="email" name="email" type="email"
                            placeholder="{{ __('alex@gmail.com') }}" :value="$user->email" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Social Number -->
                    <div>
                        <x-input-label for="address" :value="__('Adresse')" />
                        <x-text-input-2 id="address" name="address" type="text" placeholder="{{ __('Adresse') }}"
                            :value="$user->address" autofocus />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Email Address -->
                    <div>
                        <x-input-label for="avatar" :value="__('Photo')" />

                        <x-file-input name="avatar" id="logo" :value="old('avatar')" accept="image/*" />

                        <!-- Prévisualisation d'image -->
                        <img src="{{ asset('storage/' . $user->avatar) }}" id="company-logo"
                            class="my-5 w-32 h-32 object-cover border border-gray-300 rounded-md">

                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                </div>

                <hr class="border mt-10  border-primary-color border-solid opacity-100">

                {{-- <x-input-label class="font-bold  my-5" :value="__('Contact d\'urgence')" />

                <div class="md:grid grid-cols-2 gap-16 mt-7">

                    <!-- Password -->
                    <div>
                        <x-input-label for="emergency_contact_name" :value="__('Nom et prenoms')" />
                        <x-text-input-2 id="emergency_contact_name" name="emergency_contact_name" type="text"
                            placeholder="{{ __('Name') }}" :value="$user->emergency_contact_name" />
                        <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="emergency_contact_email" :value="__('Email')" />
                        <x-text-input-2 id="emergency_contact_email" name="emergency_contact_email" type="email"
                            placeholder="{{ __('alex@gmail.com') }}" :value="$user->emergency_contact_email" />
                        <x-input-error :messages="$errors->get('emergency_contact_email')" class="mt-2" />
                    </div>

                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Name -->
                    <div>
                        <x-input-label for="emergency_contact_address" :value="__('Adresse')" />
                        <x-text-input-2 id="emergency_contact_address" name="emergency_contact_address"
                            type="text" placeholder="{{ __('Adresse') }}" :value="$user->emergency_contact_address" />
                        <x-input-error :messages="$errors->get('emergency_contact_address')" class="mt-2" />
                    </div>
                    <!-- First names -->
                    <div>
                        <x-input-label for="emergency_contact_phone" :value="__('Mobile')" />
                        <x-text-input-2 id="emergency_contact_phone" name="emergency_contact_phone" type="text"
                            placeholder="{{ __('Mobile') }}" :value="$user->emergency_contact_phone" />
                        <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
                    </div>
                </div> --}}

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Social Number -->
                    <div>
                        <x-input-label for="emergency_contact_relationship" :value="__('Relation')" />
                        <x-text-input-2 id="emergency_contact_relationship" name="emergency_contact_relationship"
                            type="text" placeholder="{{ __('Relation') }}" :value="$user->emergency_contact_relationship" />
                        <x-input-error :messages="$errors->get('emergency_contact_relationship')" class="mt-2" />
                    </div>

                </div>


                <div class="my-24 flex">
                    <x-secondary-button onclick="history.back()" class="flex mr-4 justify-center shadow-md">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Record') }}
                    </x-primary-button>
                </div>
            </form>

        </div>


    </div>
    <!-- end cards -->

</x-app-layout>

<script>
    // Récupérer l'élément input file et la div
    const fileInput = document.getElementById('logo');
    const companyLogo = document.getElementById('company-logo');

    // Ajouter un écouteur d'événement pour détecter les changements
    fileInput.addEventListener('change', () => {
        // Vérifier si une valeur a été sélectionnée
        if (fileInput.value) {
            companyLogo.classList.add('hidden');
        } else {
            companyLogo.classList.remove('hidden');
        }
    });
</script>
