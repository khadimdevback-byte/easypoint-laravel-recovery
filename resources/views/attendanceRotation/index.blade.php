@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    {{-- Le JavaScript pour les minuteurs en temps réel n'est plus nécessaire pour l'affichage du statut de base --}}
    {{-- Vous pouvez le garder si vous avez d'autres fonctionnalités qui en dépendent --}}

    <div class="px-4 lg:px-6 py-6">
        <h1 class="text-2xl font-bold mb-6">Suivi des Pointages par Équipe</h1>

        {{-- Section de sélection de l'équipe (inchangée) --}}
        @if($sites->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @foreach($sites as $site)
                <div class="bg-white shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-2">{{ $site->name }}</h2>
                    @forelse($site->equipes as $equipe)
                        <a href="{{ route('pointage.attendance.rotation', ['equipe_id' => $equipe->id, 'annee' => $year, 'mois' => $month]) }}"
                           class="block mt-2 p-3 rounded {{ $selectedEquipe && $selectedEquipe->id == $equipe->id ? 'bg-blue-100' : 'bg-gray-50 hover:bg-gray-100' }}">
                            <div class="font-medium">{{ $equipe->nom }}</div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 mt-2">Aucune équipe</p>
                    @endforelse
                </div>
            @endforeach
        </div>
        @endif

        @if($selectedEquipe)
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-5 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        Pointages - {{ $selectedEquipe->nom }}
                        <span class="text-sm text-gray-600">
                            ({{ Carbon::create($year, $month)->translatedFormat('F Y') }})
                        </span>
                    </h2>
                    <div class="flex gap-2">
                         <a href="{{ route('pointage.attendance.rotation', ['equipe_id' => $selectedEquipe->id, 'annee' => $currentDate->copy()->subMonth()->year, 'mois' => $currentDate->copy()->subMonth()->month]) }}" class="btn btn-sm btn-outline" aria-label="Mois précédent">&lt;</a>
                         <a href="{{ route('pointage.attendance.rotation', ['equipe_id' => $selectedEquipe->id, 'annee' => $currentDate->copy()->addMonth()->year, 'mois' => $currentDate->copy()->addMonth()->month]) }}" class="btn btn-sm btn-outline" aria-label="Mois suivant">&gt;</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="sticky left-0 bg-gray-100 px-4 py-2 text-left z-20">Employé</th>
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $headerDate = Carbon::create($year, $month, $day);
                                        $periode = $periodesParJour[$day] ?? 'N/A';
                                        $isToday = $headerDate->isToday();
                                    @endphp
                                    <th class="px-2 py-2 text-center border {{ $isToday ? 'bg-blue-100' : '' }}">
                                        <div class="font-semibold text-lg">{{ $day }}</div>
                                        <div class="text-xs">{{ $headerDate->translatedFormat('D') }}</div>
                                        <div class="text-xs mt-1 font-medium text-blue-700">{{ $periode }}</div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($selectedEquipe->users as $user)
                                <tr class="border-t">
                                    <td class="sticky left-0 bg-white px-4 py-2 whitespace-nowrap flex items-center gap-2 z-10 border-r">
                                        <img src="{{ $user->avatar_url ?? 'https://via.placeholder.com/150' }}" class="h-8 w-8 rounded-full" alt="Avatar">
                                        {{ $user->name }}
                                    </td>
                                    @for($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $date = Carbon::create($year, $month, $day);
                                            $attendance = $attendances[$user->id][$day] ?? null;

                                            $periodeTheorique = $periodesParJour[$day] ?? 'Inconnue';
                                            $isWorkingDay = !in_array(strtolower($periodeTheorique), ['repos', 'avant début rotation', 'inconnue', 'n/a']);

                                            $tooltip = "Date: " . $date->format('d/m/Y') . "\nPériode: " . $periodeTheorique;
                                            if ($attendance) {
                                                $tooltip .= "\nStatut: " . ucfirst($attendance['statut']) . "\nHeure: " . $attendance['heure_pointage'];
                                            } elseif (!$isWorkingDay) {
                                                $tooltip .= "\nStatut: Repos";
                                            } else {
                                                $tooltip .= "\nStatut: Non pointé";
                                            }
                                        @endphp
                                        <td class="px-2 py-2 text-center border" title="{{ $tooltip }}">
                                            <div class="flex items-center justify-center h-full">
                                                @if ($attendance)
                                                    {{-- Remplacez toute la structure @switch(...) par le lien ci-dessous --}}
                                                    <a href="{{ route('pointage.attendances.show-detail-rotation', [
                                                        'equipe' => $selectedEquipe->id,
                                                        'user' => $user->id,
                                                        'year' => $year,
                                                        'month' => $month,
                                                        'day' => $day,
                                                    ]) }}" class="block p-1"> {{-- Ajoutez un peu de padding au lien pour faciliter le clic --}}
                                                        @switch(strtolower($attendance['statut']))
                                                            @case('retard')
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle text-yellow-500" viewBox="0 0 16 16">
                                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                                                </svg>
                                                                @break
                                                            @case('present')
                                                                <i class="fa-solid fa-check text-green-500"></i>
                                                                @break
                                                            @default {{-- Pour 'absent' ou autres statuts non 'retard'/'présent' --}}
                                                                <i class="fa-solid fa-xmark text-red-500"></i>
                                                                @break
                                                        @endswitch
                                                    </a>
                                                @else
                                                    @if ($isWorkingDay && $date->isPast())
                                                        {{-- Jour de travail passé sans pointage = Absent --}}
                                                        <a href="{{ route('pointage.attendances.show-detail-rotation', [
                                                            'equipe' => $selectedEquipe->id,
                                                            'user' => $user->id,
                                                            'year' => $year,
                                                            'month' => $month,
                                                            'day' => $day,
                                                        ]) }}" class="block p-1">
                                                            <i class="fa-solid fa-xmark text-red-500"></i>
                                                        </a>
                                                    @else
                                                        {{-- Jour de repos ou jour futur --}}
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $daysInMonth + 1 }}" class="text-center py-4">Aucun employé dans cette équipe.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p>Veuillez sélectionner une équipe pour afficher le suivi des pointages.</p>
            </div>
        @endif
    </div>
</x-app-layout>
