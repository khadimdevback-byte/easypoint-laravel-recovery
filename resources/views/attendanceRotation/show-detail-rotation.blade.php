@php
    use Carbon\Carbon;
@endphp
<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Lien de retour --}}
                <div class="mb-6">
                    <a href="{{ route('pointage.attendance.rotation', ['equipe_id' => $equipe->id, 'annee' => $year, 'mois' => $month]) }}"
                       class="text-blue-600 hover:text-blue-800 flex items-center font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                  clip-rule="evenodd" />
                        </svg>
                        Retour au planning
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détail du Pointage pour {{ $user->name }} - {{ $currentDate->locale('fr')->translatedFormat('d F Y') }}
                </h2>
                <br>
                {{-- Total heures travaillées ce mois --}}
                <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Total des heures travaillées ce mois</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Période du mois</p>
                            <p class="font-medium">
                                {{ \Carbon\Carbon::create($year, $month)->startOfMonth()->translatedFormat('d M Y') }}
                                -
                                {{ \Carbon\Carbon::create($year, $month)->endOfMonth()->translatedFormat('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">Jours travaillés: {{ $totalHeuresMois['days_worked'] ?? 0 }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total heures</p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ $totalHeuresMois['formatted'] ?? '00h 00m' }}
                            </p>
                        </div>
                    </div>

                </div>

                <h3 class="text-lg font-bold mb-4">Résumé de la journée</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                    {{-- Jour de travail prévu --}}
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-600 mb-1">Jour de travail prévu ?</p>
                        <p class="font-semibold text-lg">
                            @if($detailsPointage['is_working_day'])
                                ✅ Oui (Période: <span class="text-blue-700">{{ $detailsPointage['periode_nom'] ?? 'N/A' }}</span>)
                            @else
                                ❌ Non ({{ $detailsPointage['reason'] ?? 'Repos' }})
                            @endif
                        </p>
                    </div>

                    {{-- Statut global --}}
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-600 mb-1">Statut global</p>
                        <p class="font-semibold text-lg flex items-center gap-2
                            @switch($detailsPointage['etat'])
                                @case('Présence complète') text-green-600 @break
                                @case('Pointage partiel') text-yellow-600 @break
                                @case('Absent') text-red-600 @break
                                @default text-gray-800
                            @endswitch">
                            @switch($detailsPointage['etat'])
                                @case('Présence complète') ✅ @break
                                @case('Pointage partiel') ⚠️ @break
                                @case('Absent') ❌ @break
                                @default ❓
                            @endswitch
                            {{ $detailsPointage['etat'] ?? 'Inconnu' }}
                        </p>
                    </div>

                    @if($detailsPointage['is_working_day'])
                        {{-- Heures prévues --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600 mb-1">Heures Prévues</p>
                            <p class="font-semibold text-lg">
                                {{ $detailsPointage['heureEntreePrevue'] ?? 'N/A' }} - {{ $detailsPointage['heureSortiePrevue'] ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- Heures réelles --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600 mb-1">Heures Réelles</p>
                            <p class="font-semibold text-lg">
                                @if($detailsPointage['heureEntreeReelle'] && $detailsPointage['heureSortieReelle'])
                                    {{ $detailsPointage['heureEntreeReelle'] }} - {{ $detailsPointage['heureSortieReelle'] }}
                                @elseif($detailsPointage['heureEntreeReelle'])
                                    {{ $detailsPointage['heureEntreeReelle'] }} - <span class="text-gray-500 italic">Départ non pointé</span>
                                @elseif($detailsPointage['heureSortieReelle'])
                                    <span class="text-gray-500 italic">Arrivée non pointée</span> - {{ $detailsPointage['heureSortieReelle'] }}
                                @else
                                    <span class="text-gray-500 italic">Non pointé</span>
                                @endif
                            </p>
                        </div>

                        {{-- Durée travaillée --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600 mb-1">Durée Travaillée</p>
                            <p class="font-semibold text-lg text-blue-700">
                                {{ $detailsPointage['formattedWorkDuration'] ?? '00h 00m' }}
                            </p>
                        </div>

                        {{-- Retard --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600 mb-1">Retard à l'arrivée</p>
                            <p class="font-semibold text-lg {{ $detailsPointage['retardMinutes'] > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $detailsPointage['formattedRetard'] ?? '00h 00m' }}
                                {{-- @if($detailsPointage['retardMinutes'] > 0)
                                    <span class="text-sm text-red-500">(+{{ $detailsPointage['retardMinutes'] }} min)</span>
                                @endif --}}
                            </p>
                        </div>

                        {{-- Heures sup --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600 mb-1">Heures Supplémentaires</p>
                            <p class="font-semibold text-lg {{ ($detailsPointage['heuresSupMinutes'] ?? 0) > 0 ? 'text-green-600' : 'text-gray-800' }}">
                                {{ $detailsPointage['formattedHeuresSup'] ?? '00h 00m' }}
                                {{-- @if($detailsPointage['heuresSupMinutes'] > 0)
                                    <span class="text-sm text-green-500">(+{{ $detailsPointage['heuresSupMinutes'] }} min)</span>
                                @endif --}}
                            </p>
                        </div>
                    @else
                        <div class="col-span-full bg-gray-50 p-4 rounded-lg text-center text-gray-700">
                            Aucune période de travail prévue pour ce jour.
                        </div>
                    @endif
                </div>

                {{-- Historique des pointages --}}
                <h3 class="text-lg font-bold mb-4">Historique des Pointages du Jour</h3>
                @if($attendances->isNotEmpty())
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($attendances as $pointage)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pointage['type'] === 'arrivee' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($pointage['type']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pointage['timestamp']->format('H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $pointage['site_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($pointage['statut']) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">Aucun pointage enregistré pour cette journée.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
