<x-app-layout>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatTime(totalSeconds) {
            if(totalSeconds <= 0) return "0h00m";
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            return `${hours}h${minutes.toString().padStart(2, '0')}m`;
        }

        function updateTimers() {
            const now = new Date();
            const today = now.toISOString().split('T')[0];

            document.querySelectorAll('tr[data-date="'+today+'"] [data-start]').forEach(cell => {
                const startTime = new Date(cell.dataset.start);
                const endTime = new Date(cell.dataset.end);
                const timerEl = cell.querySelector('.timer');
                const statusEl = cell.querySelector('.status-badge');

                const currentTime = now.getTime();
                const startTimeMs = startTime.getTime();
                const endTimeMs = endTime.getTime();

                // Avant le début
                if(currentTime < startTimeMs) {
                    const diffSeconds = Math.floor((startTimeMs - currentTime) / 1000);
                    timerEl.textContent = `Débute dans ${formatTime(diffSeconds)}`;
                    statusEl.innerHTML = '<span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full animate-pulse">⏳ À venir</span>';
                }
                // Pendant le travail
                else if(currentTime >= startTimeMs && currentTime <= endTimeMs) {
                    const remainingSeconds = Math.floor((endTimeMs - currentTime) / 1000);
                    timerEl.textContent = `${formatTime(remainingSeconds)} restant`;

                    if(remainingSeconds <= 1800) { // 30 minutes
                        statusEl.innerHTML = '<span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full animate-bounce">⚠️ Bientôt fini</span>';
                    } else {
                        statusEl.innerHTML = '<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full animate-bounce">🔄 En cours</span>';
                    }
                }
                // Après la fin
                else {
                    timerEl.textContent = 'Terminé';
                    statusEl.innerHTML = '<span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">✅ Terminé</span>';
                }
            });
        }

        // Mise à jour immédiate + toutes les secondes
        updateTimers();
        setInterval(updateTimers, 1000);
    });
    </script>
    @endpush

    <div class="w-full mx-auto px-4 py-6">
        <div class="md:w-11/12 lg:w-9/12 mx-auto bg-white rounded-lg shadow-md p-6 animate-fade-in">
            <!-- En-tête avec navigation mois -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Planning des équipes - {{ $site->name }}</h1>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('rotations.calendrier', ['site_id' => $site->id, 'mois' => $debutMois->copy()->subMonth()->format('Y-m')]) }}"
                       class="p-2 rounded-full hover:bg-gray-100 transition duration-200 transform hover:-translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <span class="font-medium text-gray-700">{{ $debutMois->translatedFormat('F Y') }}</span>
                    <a href="{{ route('rotations.calendrier', ['site_id' => $site->id, 'mois' => $debutMois->copy()->addMonth()->format('Y-m')]) }}"
                       class="p-2 rounded-full hover:bg-gray-100 transition duration-200 transform hover:translate-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            @if (!$showRotation)
                <div class="text-center py-10">
                    <p class="text-xl text-gray-700 font-semibold">📅 Le mois sélectionné est en dehors de la période de rotation.</p>
                    <p class="text-gray-500 mt-2">Aucune planification n’est disponible pour ce mois.</p>
                </div>
            @else
                <!-- Tableau de planning -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        @if (!empty($planning) && isset($planning[array_key_first($planning)]))
                        <thead>
                            <tr class="bg-gray-50 text-center">
                                <th class="sticky left-0 z-10 border border-gray-200 px-4 py-3 bg-gray-50 min-w-[120px]">Date</th>
                                @foreach($planning[array_key_first($planning)] as $equipe => $data)
                                    <th class="border border-gray-200 px-4 py-3 bg-gray-50 min-w-[180px]">
                                        <div class="flex flex-col items-center">
                                            <span class="font-semibold">{{ $equipe }}</span>
                                            <span class="text-xs text-gray-500 mt-1">
                                                {{ $site->equipes->firstWhere('nom', $equipe)?->users->pluck('name')->join(', ') }}
                                            </span>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @foreach($planning as $date => $equipes)
                                @php
                                    $currentDate = \Carbon\Carbon::parse($date);
                                    $isToday = $currentDate->isToday();
                                    $isWeekend = $currentDate->isWeekend();
                                    $rowClass = $isToday ? 'bg-blue-50 font-semibold' : ($isWeekend ? 'bg-gray-50' : '');
                                @endphp
                                <tr class="{{ $rowClass }} hover:bg-indigo-50 transition duration-200 ease-in-out" data-date="{{ $date }}">
                                    <td class="sticky left-0 z-10 border border-gray-200 px-4 py-3 bg-inherit min-w-[120px]">
                                        <div class="flex flex-col items-center">
                                            <span @class([
                                                'font-bold' => $isToday,
                                                'text-gray-500' => $isWeekend,
                                                'text-blue-600' => $isToday
                                            ])>
                                                {{ $currentDate->translatedFormat('D d') }}
                                            </span>
                                            @if($isToday)
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full mt-1 animate-pulse">Aujourd'hui</span>
                                            @endif
                                        </div>
                                    </td>

                                    @foreach($site->equipes as $equipe)
                                        @php
                                            $data = $equipes[$equipe->nom] ?? ['periode' => 'Repos', 'status' => 'Repos'];
                                            $isWorking = $data['status'] !== 'Repos';
                                            $cellClasses = [
                                                'border border-gray-200 px-4 py-3',
                                                'text-center align-top',
                                                'transition-colors duration-300',
                                                $isWorking ? 'bg-green-50' : 'bg-red-50'
                                            ];
                                        @endphp

                                        <td @class($cellClasses)
                                            @if($isWorking && isset($data['raw_data']))
                                                data-start="{{ $data['raw_data']['start'] ?? '' }}"
                                                data-end="{{ $data['raw_data']['end'] ?? '' }}"
                                            @endif
                                        >
                                            <div class="flex flex-col gap-1.5 items-center">
                                                <span @class([
                                                    'font-semibold',
                                                    'text-green-700' => $isWorking,
                                                    'text-red-700' => !$isWorking
                                                ])>
                                                    {{ $isWorking ? $data['periode'] : 'Repos' }}
                                                </span>

                                                @if($isWorking)
                                                    <div class="text-sm text-gray-600">
                                                        {{ $data['heure_debut'] ?? 'N/A' }} - {{ $data['heure_fin'] ?? 'N/A' }}
                                                    </div>

                                                    <div class="timer text-xs font-mono font-semibold">
                                                        @if($isToday && isset($data['raw_data']))
                                                            @php
                                                                $start = \Carbon\Carbon::parse($data['raw_data']['start']);
                                                                $end = \Carbon\Carbon::parse($data['raw_data']['end']);
                                                                $now = \Carbon\Carbon::now();

                                                                if($now < $start) {
                                                                    $diff = $start->diff($now);
                                                                    echo "Débute dans ".$diff->h."h".str_pad($diff->i, 2, '0', STR_PAD_LEFT)."m";
                                                                } elseif($now >= $start && $now <= $end) {
                                                                    $diff = $end->diff($now);
                                                                    echo $diff->h."h".str_pad($diff->i, 2, '0', STR_PAD_LEFT)."m restant";
                                                                } else {
                                                                    echo "Terminé";
                                                                }
                                                            @endphp
                                                        @else
                                                            {{ $currentDate->isPast() ? 'Terminé' : 'À venir' }}
                                                        @endif
                                                    </div>

                                                    <div class="status-badge">
                                                        @if($isToday)
                                                            @if($data['status'] === 'À venir')
                                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">⏳ À venir</span>
                                                            @elseif($data['status'] === 'En cours')
                                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full animate-pulse">🔄 En cours</span>
                                                            @else
                                                                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">✅ Terminé</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
