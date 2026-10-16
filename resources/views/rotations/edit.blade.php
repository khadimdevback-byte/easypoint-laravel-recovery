<x-app-layout>
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
        <div class=" my-10 px-6 py-6 ">
            <!-- card 1 -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Modifier une Rotation') }}
                </h1>
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
            @if ($errors->any())
                <div class="mb-4 p-4 rounded bg-red-100 text-red-800 border border-red-400">
                    <strong>Oups !</strong> Il y a eu quelques problèmes avec les données saisies :
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('rotation.update', $rotation->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>

                <!-- Informations de la rotation -->
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="nom" :value="__('Nom Rotation')" />
                        <x-text-input-2 type="text" name="nom" required placeholder="{{ __('Nom') }}" :value="old('nom', $rotation->nom)" autofocus/>
                    </div>
                    <div>
                        <x-input-label for="systeme" :value="__('Système Rotation')" />
                        <x-select-input name="systeme" required :options="[
                            'journalier' => 'Journalier',
                            'hebdomadaire' => 'Hebdomadaire',
                            'mensuel' => 'Mensuel'
                        ]" :selected="old('systeme', $rotation->systeme)" />
                        @error('systeme')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="duree_cycle" :value="__('Durée du cycle')" />
                        <x-text-input-2 type="number" name="duree_cycle" required placeholder="{{ __('Durée cycle') }}" :value="old('duree_cycle', $rotation->duree_cycle)"/>
                        @error('duree_cycle')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="date_debut" :value="__('Date de début')" />
                        <x-text-input-2 type="date" name="date_debut" required :value="old('date_debut', \Carbon\Carbon::parse($rotation->date_debut)->format('Y-m-d'))" />                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="nombre_jours_travail" :value="__('Nombre de jours de travail')" />
                        <x-text-input-2 type="number" name="nombre_jours_travail" required placeholder="{{ __('Nombre de jours de travail') }}" :value="old('nombre_jours_travail', $rotation->nombre_jours_travail)" />
                        @error('nombre_jours_travail')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- <div>
                        <x-input-label for="type_cycle" :value="__('Type de Cycle')"/>
                        <x-select-input
                        name="type_cycle"
                        label="Type de cycle"
                        :options="['fixe' => 'Fixe', 'tournant' => 'Tournant']"
                        :value="old('type_cycle',$rotation->type_cycle)"
                        required
                    />
                    </div> --}}

                    <div>
                        <x-input-label for="nombre_repartitions" :value="__('Nombre de répartitions')" />
                        <x-text-input-2 type="number" name="nombre_repartitions" required placeholder="{{ __('Nombre de répartitions') }}" :value="old('nombre_repartitions', $rotation->nombre_repartitions)" id="nombre_repartitions"/>
                        @error('nombre_repartitions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3>Répartitions</h3>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold">Modifier les répartitions</h4>
                        </div>

                        <div id="repartitions-container">
                            @foreach($rotation->repartitions as $index => $repartition)
                                <div class="repartition">
                                    <div class="flex space-x-4 mt-7">
                                        <div class="flex-1">
                                            <x-input-label for="repartitions[{{ $index }}][periode]" :value="'Période'" />
                                            <x-select-input name="repartitions[{{ $index }}][periode]" required :options="[
                                                    'matin' => 'Matin',
                                                    'apres-midi' => 'Après-midi',
                                                    'nuit' => 'Nuit'
                                                ]" :selected="old('repartitions['.$index.'][periode]', $repartition->periode)" />
                                        </div>

                                        <div class="flex-1">
                                            <x-input-label for="repartitions[{{ $index }}][nombre_jours]" :value="'Nombre de jours'" />
                                            <x-text-input-2 type="number" name="repartitions[{{ $index }}][nombre_jours]" required :value="old('repartitions['.$index.'][nombre_jours]', $repartition->nombre_jours)" />
                                        </div>

                                        <div class="flex-1">
                                            <x-input-label for="repartitions[{{ $index }}][heure_debut]" :value="'Heure début'" />
                                            <x-text-input-2 type="time" name="repartitions[{{ $index }}][heure_debut]" required :value="old('repartitions['.$index.'][heure_debut]', $repartition->heure_debut)" />
                                        </div>

                                        <div class="flex-1">
                                            <x-input-label for="repartitions[{{ $index }}][heure_fin]" :value="'Heure fin'" />
                                            <x-text-input-2 type="time" name="repartitions[{{ $index }}][heure_fin]" required :value="old('repartitions['.$index.'][heure_fin]', $repartition->heure_fin)" />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <br>
                <div class="my-19 flex">
                    <x-secondary-button class="flex mr-4 justify-center shadow-md"><a href="{{ route('rotation.index') }}">
                        {{ __('Annuler') }}
                    </a></x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Mettre à jour') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('nombre_repartitions').addEventListener('input', function() {
            let container = document.getElementById('repartitions-container');
            container.innerHTML = ''; // Supprimer les anciennes répartitions

            let numRepartitions = parseInt(this.value) || 0; // Nombre de répartitions

            for (let i = 0; i < numRepartitions; i++) {
                let div = document.createElement('div');
                div.classList.add('repartition');
                div.innerHTML = `
                    <div class="flex space-x-4 mt-7">
                        <!-- Sélection de la période -->
                        <div class="flex-1">
                            <x-input-label for="repartitions[${i}][periode]" :value="'Période'" />
                            <x-select-input name="repartitions[${i}][periode]" required :options="[
                                'matin' => 'Matin',
                                'apres-midi' => 'Après-midi',
                                'nuit' => 'Nuit' ]" :selected="old('repartitions[${i}][periode]')" />
                            @error("repartitions.$index.periode")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombre de jours pour cette période -->
                        <div class="flex-1">
                            <x-input-label for="repartitions[${i}][nombre_jours]" :value="'Nombre de jours'" />
                            <x-text-input-2 type="number" name="repartitions[${i}][nombre_jours]"  placeholder="Ex: 2" />
                            @error("repartitions.$index.nombre_jours")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Heure de début -->
                        <div class="flex-1">
                            <x-input-label for="repartitions[${i}][heure_debut]" :value="'Heure début'" />
                            <x-text-input-2 type="time" name="repartitions[${i}][heure_debut]" required />
                            @error("repartitions.$index.heure_debut")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Heure de fin -->
                        <div class="flex-1">
                            <x-input-label for="repartitions[${i}][heure_fin]" :value="'Heure fin'" />
                            <x-text-input-2 type="time" name="repartitions[${i}][heure_fin]" required />
                            @error("repartitions.$index.heure_fin")
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                `;
                container.appendChild(div);
            }
        });
    </script>

</x-app-layout>
