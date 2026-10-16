<x-app-layout>
    @php
        $menusAutorises = optional(auth()->user()->company)->menus?->pluck('name')->toArray() ?? [];
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Liste des fiches de paie') }}
        </h2>
    </x-slot>

    <div class="md:w-9/12 2xl:w-7/12 mx-auto mt-8">
        <div class="px-6 mb-4">
            <h1 class="font-bold text-black text-2xl">{{ __('Listes Des Fiches de Paie') }}</h1>
        </div>

        {{-- ALERTES --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-white bg-green-600 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-white bg-red-600 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORMULAIRE FILTRAGE --}}
        <form method="GET" action="{{ url()->current() }}" class="mb-6 flex flex-wrap gap-4 items-center">
            <select name="site_id" class="form-select border px-3 py-2 rounded">
                <option value="">-- Tous les sites --</option>
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                @endforeach
            </select>

            <select name="mois" class="form-select border px-3 py-2 rounded">
                <option value="">-- Tous les mois --</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('mois') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('m', $i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="annee" class="form-select border px-3 py-2 rounded">
                <option value="">-- Toutes les années --</option>
                @foreach ($anneesDisponibles as $an)
                    <option value="{{ $an }}" {{ request('annee') == $an ? 'selected' : '' }}>{{ $an }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filtrer
            </button>
        </form>

        {{-- BOUTONS DE GÉNÉRATION --}}
        <div class="mb-6 flex flex-wrap gap-4">
            @if(in_array('fiche de paie à horaires fixes', $menusAutorises))
                <form method="GET" action="{{ route('fichepaie.genererFixe') }}">
                    <input type="hidden" name="site_id" value="{{ request('site_id') }}">
                    <input type="hidden" name="mois" value="{{ request('mois') }}">
                    <input type="hidden" name="annee" value="{{ request('annee') }}">
                    <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                        Générer fiches fixes
                    </button>
                </form>
            @endif

            @if(in_array('fiches de paie à rotation', $menusAutorises))
                <form method="GET" action="{{ route('fichepaie.genererRotatif') }}">
                    <input type="hidden" name="site_id" value="{{ request('site_id') }}">
                    <input type="hidden" name="mois" value="{{ request('mois') }}">
                    <input type="hidden" name="annee" value="{{ request('annee') }}">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                        Générer fiches rotatives
                    </button>
                </form>
            @endif
        </div>

        {{-- TABLEAU DES FICHES --}}
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto bg-white rounded-lg border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Nom de l'employé</th>
                        <th class="px-4 py-2 text-left">Mois</th>
                        <th class="px-4 py-2 text-left">Année</th>
                        {{-- <th class="px-4 py-2 text-left">Heures travaillées</th>
                        <th class="px-4 py-2 text-left">Salaire</th> --}}
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fichePaies as $fiche)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $fiche->id }}</td>
                            <td class="px-4 py-2">{{ $fiche->user->name }}</td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::createFromDate(null, $fiche->mois)->translatedFormat('F') }}
                            </td>
                            <td class="px-4 py-2">{{ $fiche->annee }}</td>
                            {{-- <td class="px-4 py-2">{{ number_format($fiche->heures_travaillees, 2) }}</td> --}}
                            {{-- <td class="px-4 py-2">
                                @php
                                    $typeHoraire = $fiche->user->type_horaire;
                                    $tauxHoraire = $fiche->user->salary_rate ?? 0;
                                    $heuresTravaillees = $fiche->heures_travaillees ?? 0;
                                    $salaireFixe = $fiche->user->salaire_fixe ?? 0;
                                    $salaireCalcule = null;
                                @endphp

                                @if ($typeHoraire === 'fixe')
                                    @if ($salaireFixe > 0)
                                        {{ number_format($salaireFixe, 2, ',', ' ') }} €
                                    @elseif ($tauxHoraire > 0)
                                        {{ number_format($tauxHoraire * $heuresTravaillees, 2, ',', ' ') }} €
                                    @else
                                        <span class="text-red-500 font-medium">Non défini</span>
                                    @endif
                                @elseif ($typeHoraire === 'rotation')
                                    @if ($tauxHoraire > 0)
                                        {{ number_format($tauxHoraire * $heuresTravaillees, 2, ',', ' ') }} €
                                    @else
                                        <span class="text-red-500 font-medium">Non défini</span>
                                    @endif
                                @else
                                    <span class="text-red-500 font-medium">Type inconnu</span>
                                @endif
                            </td> --}}
                            <td class="px-4 py-2">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link href="{{ route('fichepaie.show', $fiche->id) }}">
                                            Afficher
                                        </x-dropdown-link>
                                        <x-dropdown-link href="{{ route('fichepaie.edit', $fiche->id) }}">
                                            Modifier
                                        </x-dropdown-link>
                                        <form method="POST" action="{{ route('fichepaie.delete', $fiche->id) }}"
                                              onsubmit="return confirm('Confirmer la suppression ?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-dropdown-link as="button" type="submit" class="text-red-600">
                                                Supprimer
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">
                                Aucune fiche de paie trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
