<section>
    <header>
        <h2 class="font-medium text-gray-900">
            {{ __('Informations de l\'entreprise') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Mettez à jour les informations de votre entreprise.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update.company') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- SECTION 1 : Informations générales -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <h3 class="text-md font-semibold text-primary-color mb-4">{{ __('Informations générales') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de l'entreprise -->
                <div>
                    <x-input-label for="compagny_name" :value="__('Nom de l\'entreprise *')" />
                    <x-text-input-2 id="compagny_name" name="compagny_name" type="text"
                        placeholder="{{ __('Nom de l\'entreprise') }}" :value="old('compagny_name', $company->compagny_name)" required autofocus />
                    <x-input-error :messages="$errors->get('compagny_name')" class="mt-2" />
                </div>

                <!-- Email de l'entreprise -->
                <div>
                    <x-input-label for="compagny_email" :value="__('Email de l\'entreprise *')" />
                    <x-text-input-2 id="compagny_email" name="compagny_email" type="email"
                        placeholder="{{ __('contact@entreprise.com') }}" :value="old('compagny_email', $company->compagny_email)" required />
                    <x-input-error :messages="$errors->get('compagny_email')" class="mt-2" />
                </div>

                <!-- Téléphone -->
                <div>
                    <x-input-label for="phone" :value="__('Téléphone')" />
                    <x-text-input-2 id="phone" name="phone" type="tel"
                        placeholder="{{ __('+228 XX XXX XXX') }}" :value="old('phone', $company->phone)" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <!-- Site web -->
                <div>
                    <x-input-label for="website" :value="__('Site web')" />
                    <x-text-input-2 id="website" name="website" type="url"
                        placeholder="{{ __('https://www.entreprise.com') }}" :value="old('website', $company->website)" />
                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Adresse *')" />
                    <x-text-input-2 id="address" name="address" type="text"
                        placeholder="{{ __('Adresse complète de l\'entreprise') }}" :value="old('address', $company->address)" required />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- SECTION 2 : Configuration -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <h3 class="text-md font-semibold text-primary-color mb-4">{{ __('Configuration') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fuseau horaire -->
                <div>
                    <x-input-label for="timezone" :value="__('Fuseau horaire *')" />
                    <x-select-input name="timezone" :options="$timezones" :selected="old('timezone', $company->timezone)" required />
                    <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                </div>

                <!-- Couleur de l'en-tête du badge -->
                <div>
                    <x-input-label for="badge_header_color" :value="__('Couleur de l\'en-tête du badge')" />
                    <div class="flex items-center gap-3">
                        <x-text-input-2 id="badge_header_color" name="badge_header_color" type="color"
                            :value="old('badge_header_color', $company->badge_header_color ?? '#1D4ED8')" class="w-20 h-10 p-1" />
                        <span class="text-sm text-gray-500">Cliquez pour choisir une couleur</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Cette couleur sera utilisée pour l'en-tête des badges des employés</p>
                    <x-input-error :messages="$errors->get('badge_header_color')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- SECTION 3 : Logo -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <h3 class="text-md font-semibold text-primary-color mb-4">{{ __('Logo de l\'entreprise') }}</h3>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <x-input-label for="logo" :value="__('Logo')" />
                    <x-file-input name="logo" id="logo" :value="old('logo')" accept="image/*" />
                    <p class="text-sm text-gray-500 mt-1">Formats acceptés : JPG, PNG. Max 2MB</p>
                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />

                    <!-- Aperçu du logo actuel -->
                    @if($company->logo)
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 mb-2">Logo actuel :</p>
                            <img src="{{ asset('storage/' . $company->logo) }}" id="company-logo-preview"
                                class="w-32 h-32 object-cover border border-gray-300 rounded-md">
                        </div>
                    @else
                        <div class="mt-3">
                            <div id="company-logo-preview" class="w-32 h-32 bg-gray-100 border border-gray-300 rounded-md flex items-center justify-center hidden">
                                <span class="text-gray-400 text-xs">Nouveau logo</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aperçu du badge -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <h3 class="text-md font-semibold text-primary-color mb-4">{{ __('Aperçu du badge') }}</h3>
            <p class="text-sm text-gray-500 mb-3">Voici à quoi ressemblera l'en-tête des badges de vos employés :</p>

            <div class="flex justify-center">
                <div class="relative rounded-[15px] overflow-hidden shadow-lg border text-[10px] font-sans bg-white"
                     style="width: 300px; height: 180px;">

                    <!-- Header avec couleur dynamique -->
                    <div class="flex justify-between items-center px-2" id="badge-header-preview"
                         style="background-color: {{ $company->badge_header_color ?? '#1D4ED8' }}; height: 35px;">
                        <div class="text-white font-bold uppercase text-xs leading-[1]">{{ Str::limit($company->compagny_name ?? 'Entreprise', 20) }}</div>
                        <div class="text-white font-semibold text-xs leading-[1]">Carte d'employé</div>
                    </div>

                    <div class="flex items-center px-3 py-2" style="height: calc(100% - 35px - 25px);">
                        <div class="rounded border mr-3 bg-gray-100 flex items-center justify-center" style="width: 60px; height: 60px;">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 text-[9px]">
                            <p class="font-bold uppercase">NOM PRÉNOM</p>
                            <p class="italic text-gray-600">Fonction</p>
                            <p>Matricule : <strong>XXXXXX</strong></p>
                        </div>
                        <div class="ml-1 bg-gray-200" style="width: 55px; height: 55px;"></div>
                    </div>

                    <div class="flex justify-between items-center px-2" style="height: 25px; font-size: 7px; color: #6B7280; border-top: 1px solid #ddd;">
                        <span>{{ now()->format('d/m/Y') }}</span>
                        <span>{{ Str::limit($company->compagny_name ?? 'Entreprise', 15) }}</span>
                        <span>Easy-point</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>

            @if (session('status') === 'company-updated')
                <p class="text-sm text-green-600">{{ __('Enregistré.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prévisualisation du logo
        const fileInput = document.getElementById('logo');
        const logoPreview = document.getElementById('company-logo-preview');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        if (!logoPreview) {
                            // Créer l'aperçu s'il n'existe pas
                            const newPreview = document.createElement('img');
                            newPreview.id = 'company-logo-preview';
                            newPreview.className = 'w-32 h-32 object-cover border border-gray-300 rounded-md mt-3';
                            newPreview.src = e.target.result;
                            fileInput.parentElement.appendChild(newPreview);
                        } else {
                            logoPreview.src = e.target.result;
                            logoPreview.classList.remove('hidden');
                        }
                    }

                    reader.readAsDataURL(fileInput.files[0]);
                }
            });
        }

        // Prévisualisation de la couleur du badge en temps réel
        const colorInput = document.getElementById('badge_header_color');
        const badgeHeader = document.getElementById('badge-header-preview');

        if (colorInput && badgeHeader) {
            colorInput.addEventListener('input', function() {
                badgeHeader.style.backgroundColor = this.value;
            });

            colorInput.addEventListener('change', function() {
                badgeHeader.style.backgroundColor = this.value;
            });
        }
    });
</script>

<style>
    /* Animation pour l'aperçu */
    #badge-header-preview {
        transition: background-color 0.3s ease;
    }
</style>
