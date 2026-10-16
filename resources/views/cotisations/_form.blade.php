
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Libellé -->
                    <div>
                        <x-input-label for="libelle" :value="__('Libellé *')" />
                        <x-text-input-2 id="libelle" name="libelle" type="text" 
                            placeholder="Ex: Sécurité sociale" 
                            :value="old('libelle', $cotisationSociale->libelle ?? '')" required autofocus />
                        @error('libelle')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Taux salarié -->
                    <div>
                        <x-input-label for="taux_salarial" :value="__('Taux salarié (%) *')" />
                        <x-text-input-2 id="taux_salarial" name="taux_salarial" type="number" step="0.01"
                            placeholder="Ex: 20.5" 
                            :value="old('taux_salarial', $cotisationSociale->taux_salarial ?? '')" required />
                        @error('taux_salarial')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Taux employeur -->
                    <div>
                        <x-input-label for="taux_employeur" :value="__('Taux employeur (%)')" />
                        <x-text-input-2 id="taux_employeur" name="taux_employeur" type="number" step="0.01"
                            placeholder="Ex: 30.2" 
                            :value="old('taux_employeur', $cotisationSociale->taux_employeur ?? '')" />
                        @error('taux_employeur')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="my-10 flex">
                    <x-secondary-button class="flex mr-4 justify-center shadow-md">
                        <a href="{{ route('cotisations-sociales.index') }}">
                            {{ __('Annuler') }}
                        </a>
                    </x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ isset($cotisationSociale) ? __('Mettre à jour') : __('Créer') }}
                    </x-primary-button>
                </div>