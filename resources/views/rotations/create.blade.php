<x-app-layout>
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
        <div class=" my-10 px-6 py-6 ">
            <!-- card 1 -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Paramétrage des Rotation et Repartition') }}
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
            <form method="POST" action="{{ route('rotation.store') }}">
                @csrf
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="nom" :value="__('Nom Rotation')" />
                        <x-text-input-2 type="text" name="nom" required placeholder="{{ __('nom ') }}" :value="old('nom')" autofocus/>
                    </div>
                    <div>
                        <x-input-label for="systeme" :value="__('Système Rotation')" />
                        <x-select-input name="systeme" required :options="[
                            'journalier' => 'Journalier',
                            'hebdomadaire' => 'Hebdomadaire',
                            'mensuel' => 'Mensuel'
                        ]" :selected="old('systeme')" />
                        @error('systeme')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="duree_cycle" :value="__('Durée du cycle')" />
                        <x-text-input-2 type="number" name="duree_cycle" required placeholder="{{ __('durée cycle ') }}" :value="old('duree_cycle')"/>
                        @error('duree_cycle')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="date_debut" :value="__('Date de début')" />
                        <x-text-input-2 type="date" name="date_debut" required :value="old('date_debut')" />
                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                   <div>
                        <x-input-label for="nombre_jours_travail" :value="__('Nombre de jours de travail')" />
                        <x-text-input-2 type="number" name="nombre_jours_travail" placeholder="{{ __('nombre_jours_travail') }}" :value="old('nombre_jours_travail')" required />
                        @error('nombre_jours_travail')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                   </div>

                   <div>
                        <x-input-label for="nombre_repartitions" :value="__('Nombre de répartitions')" />
                        <x-text-input-2 type="number" name="nombre_repartitions" placeholder="{{ __('nombre_repartitions') }}" :value="old('nombre_repartitions')" required id="nombre_repartitions"/>
                        @error('nombre_repartitions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                   </div>
                </div>


                <div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3>Répartitions</h3>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold">Ajouter des répartitions</h4>
                        </div>
                        <div id="repartitions-container">
                        </div>
                    </div>
                </div>

                <br>
                <div class="my-19 flex">
                    <x-secondary-button  class="flex mr-4 justify-center shadow-md"><a href="{{ route('rotation.index') }}">
                        {{ __('Annuler') }}
                    </a></x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Ajouter') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const repartitionInput = document.getElementById('nombre_repartitions');
            const systemeSelect = document.querySelector('[name="systeme"]');

            function updateRepartitions() {
                const container = document.getElementById('repartitions-container');
                container.innerHTML = ''; // Clear previous

                const numRepartitions = parseInt(repartitionInput.value) || 0;
                const systeme = systemeSelect.value;

                for (let i = 0; i < numRepartitions; i++) {
                    let div = document.createElement('div');
                    div.classList.add('repartition');

                    div.innerHTML = `
                        <div class="flex space-x-4 mt-7">
                            <!-- Période -->
                            <div class="flex-1">
                                <label for="repartitions[${i}][periode]">Période</label>
                                <input type="text" name="repartitions[${i}][periode]" required placeholder="Ex: Matin, Après-midi, Nuit" class="w-full border rounded px-3 py-2" />
                                @foreach ($errors->get('repartitions.*.periode') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                            <!-- Nombre de jours -->
                            <div class="flex-1">
                                <label for="repartitions[${i}][nombre_jours]">Nombre de jours</label>
                                <input type="number" name="repartitions[${i}][nombre_jours]" placeholder="Ex: 2" class="w-full border rounded px-3 py-2" />
                                @foreach ($errors->get('repartitions.*.nombre_jours') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>

                            <!-- Heure de début -->
                            <div class="flex-1">
                                <label for="repartitions[${i}][heure_debut]">Heure début</label>
                                <input type="time" name="repartitions[${i}][heure_debut]" required class="w-full border rounded px-3 py-2" />
                                @foreach ($errors->get('repartitions.*.heure_debut') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>

                            <!-- Heure de fin -->
                            <div class="flex-1">
                                <label for="repartitions[${i}][heure_fin]">Heure fin</label>
                                <input type="time" name="repartitions[${i}][heure_fin]" required class="w-full border rounded px-3 py-2" />
                                @foreach ($errors->get('repartitions.*.heure_fin') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    `;

                    container.appendChild(div);
                }
            }

            repartitionInput.addEventListener('input', updateRepartitions);
            systemeSelect.addEventListener('change', updateRepartitions);
        });
    </script>


</x-app-layout>
