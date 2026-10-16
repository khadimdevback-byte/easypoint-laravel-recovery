<x-app-layout>
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
        <div class=" my-10 px-6 py-6 ">
            <!-- card 1 -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Modifier Une Offre') }}
                </h1>
            </div>

            <form method="POST" action="{{ route('offres.update',$offre->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="site_id" :value="__('Site *')"/>
                        <x-select-input id="site-select"
                            name="site_id"
                            :options="$site->pluck('name', 'id')"
                            :selected="old('site_id',$offre->site_id)"/>
                            @error('site_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        {{-- <x-input-error :messages="$errors->get('first_name')" class="mt-2" /> --}}
                    </div>
                    <!-- Poste -->
                    <div>
                        <x-input-label for="poste_id" :value="__('Poste *')" />
                            <x-select-input id="fonction-select"
                                name="poste_id"
                                :options="$fonction->pluck('nom_fonction', 'id')"
                                :selected="old('poste_id',$offre->poste_id)"/>
                                @error('poste_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                        {{-- <x-input-error :messages="$errors->get('name')" class="mt-2" /> --}}
                    </div>
                </div>
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="taux_horaire" :value="__('Taux horaire (€) *')" />
                        <x-text-input-2 id="taux_horaire" name="taux_horaire" type="number"
                            placeholder="{{ __('taux_horaire ') }}" :value="old('taux_horaire',$offre->taux_horaire)"  autofocus />
                            @error('taux_horaire')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                    </div>
                    <div>
                        <x-input-label for="nombre_personne_recherche" :value="__('Nombre de personnes recherchées *')" />
                        <x-text-input-2 id="nombre_personne_recherche" name="nombre_personne_recherche" type="number" placeholder="{{ __('nombre_personne_recherche') }}"
                            :value="old('nombre_personne_recherche',$offre->nombre_personne_recherche)"  autofocus />
                            @error('nombre_personne_recherche')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        {{-- <label class="flex items-center space-x-2 border border-gray-200 rounded-md p-2 cursor-pointer hover:bg-gray-100"> --}}
                        <x-input-label for="demarrage" :value="__('Démarrage*')" class="font-semibold text-lg" />
                        <div class="border border-gray-300 rounded-lg p-4 space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="demarrage" value="immediat"  id="demarrage" class="mr-2" onclick="toggleStartDate()" {{ old('demarrage',$offre->demarrage) == 'immediat' ? 'checked' : '' }}>
                                Immédiat
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="demarrage" value="A_partir_de"  id="demarrage_non" class="mr-2" onclick="toggleStartDate()" {{ old('demarrage',  $offre->demarrage) == 'A_partir_de' ? 'checked' : '' }}> Non immédiat
                            </label>
                            @error('demarrage')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <x-input-label for="start_date" :value="__('A partir De ')"/>
                        <x-text-input-2 id="start_date" :value="old('start_date')" name="start_date" type="date" placeholder="{{ __('A partir De ') }}"
                          autofocus />
                        @error('start_date')
                          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="description_offre" :value="__('Description Offre *')" />
                        <x-textarea id="description_offre" name="description_offre" class="block mt-1  w-full" aria-placeholder="description_offre"> {{ old('description_offre', $offre->description_offre) }}</x-textarea>
                        @error('description_offre')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <br>
                <div class="my-19 flex">
                    <x-secondary-button  class="flex mr-4 justify-center shadow-md"><a href="{{ route('offres.Liste') }}">
                        {{ __('Annuler') }}
                    </a></x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Modifier & Publier') }}
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

<script>
    function toggleStartDate() {
        var startDateInput = document.getElementById('start_date');
        var radioImmediat = document.getElementById('demarrage');
        var radioNonImmediat = document.getElementById('demarrage_non');

        // Si le bouton radio 'Immadiat' est coché, désactive start_date
        if (radioImmediat.checked) {
            startDateInput.disabled = true;
        } else {
            startDateInput.disabled = false; // Réactive start_date si 'Non immédiat' est coché
        }
    }

    // Vérifier au chargement de la page si une case est déjà cochée
    document.addEventListener('DOMContentLoaded', function() {
        var radioImmediat = document.getElementById('demarrage');
        var startDateInput = document.getElementById('start_date');
        if (radioImmediat.checked) {
            startDateInput.disabled = true; // Désactive start_date si 'Immadiat' est coché
        } else {
            startDateInput.disabled = false; // Réactive start_date si 'Non immédiat' est coché
        }
    });
</script>
