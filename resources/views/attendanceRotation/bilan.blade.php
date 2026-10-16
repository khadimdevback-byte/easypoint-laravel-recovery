@php use Carbon\Carbon; @endphp

<x-app-layout>
    <div class="md:w-10/12 2xl:w-9/12 mx-auto">
        <div class="px-6 mt-5">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                Bilan Global d’Assiduité
            </h1>
        </div>

        <div class="w-full px-6 py-4">
            {{-- Filtres --}}
            <div class="w-full px-6 py-6 mx-auto">
            {{-- Section des filtres --}}
            <div class="flex p-3 flex-wrap -mx-3">
                <div class="pt-2 w-full flex flex-col bg-white border shadow-sm rounded-xl ">
                    <form action="{{ route('pointage.bilan') }}" method="get">
                        <div class="flex flex-wrap">
                            <div class="m-4 xl:min-w-52 md:min-w-32">
                                <label for="filter-type" class="block mb-2 font-semibold text-gray-700">Sélectionner le site</label>
                                <select id="filter-type" name="type" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                                    <option value="" {{ request('type') == '' ? 'selected' : '' }}>Tous les sites</option>
                                    @foreach ($sites as $site)
                                        <option value="{{ $site->name }}" {{ request('type') == $site->name ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="m-4 xl:min-w-52 md:min-w-32">
                                <label for="filter-period" class="block mb-2 font-semibold text-gray-700">Période</label>
                                <select id="filter-period" name="period" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                                    <option value="day" {{ $periodType == 'day' ? 'selected' : '' }}>Jour</option>
                                    <option value="week" {{ $periodType == 'week' ? 'selected' : '' }}>Semaine</option>
                                    <option value="month" {{ $periodType == 'month' ? 'selected' : '' }}>Mois</option>
                                    <option value="year" {{ $periodType == 'year' ? 'selected' : '' }}>Année</option>
                                </select>
                            </div>

                            {{-- Champs de sélection de date dynamiques --}}
                            <div class="m-4 xl:min-w-52 md:min-w-32" id="day-select-container" style="{{ $periodType == 'day' ? 'display: block;' : 'display: none;' }}">
                                <label class="block mb-2 font-semibold text-gray-700">Sélectionner le jour</label>
                                <input type="date" id="day" name="day" value="{{ request('day') ?? Carbon::now()->format('Y-m-d') }}" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                            </div>

                            <div class="m-4 xl:min-w-52 md:min-w-32" id="week-select-container" style="{{ $periodType == 'week' ? 'display: block;' : 'display: none;' }}">
                                <label class="block mb-2 font-semibold text-gray-700">Sélectionner la semaine</label>
                                <input type="week" id="week" name="week" value="{{ request('week') ?? Carbon::now()->format('Y-\WW') }}" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                            </div>

                            <div class="m-4 xl:min-w-52 md:min-w-32" id="month-select-container" style="{{ $periodType == 'month' ? 'display: block;' : 'display: none;' }}">
                                <label class="block mb-2 font-semibold text-gray-700">Sélectionner le mois</label>
                                <input type="month" id="month" name="month" value="{{ request('month') ?? Carbon::now()->format('Y-m') }}" class="py-3 px-4 block w-full border-gray-200 rounded-lg border capitalize">
                            </div>

                            <div class="m-4 xl:min-w-52 md:min-w-32" id="year-select-container" style="{{ $periodType == 'year' ? 'display: block;' : 'display: none;' }}">
                                <label class="block mb-2 font-semibold text-gray-700">Sélectionner l'année</label>
                                <select id="year" name="year" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                                    @foreach (range(Carbon::now()->year - 10, Carbon::now()->year + 1) as $yearOption)
                                        <option value="{{ $yearOption }}" {{ (request('year') ?? Carbon::now()->year) == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="my-auto">
                                <x-primary-button type="submit" class="flex ml-4 justify-center shadow-md text-sm h-12 items-center text-center mt-6">
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

            {{-- Export --}}
            <div class="flex justify-end gap-2 mb-4">
                <button class="bg-green-600 text-white px-4 py-2 rounded shadow text-sm">Excel</button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded shadow text-sm">CSV</button>
                <button class="bg-red-600 text-white px-4 py-2 rounded shadow text-sm">PDF</button>
            </div>

            {{-- Tableau --}}
            <div class="bg-white border shadow-sm rounded-xl overflow-x-auto">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Résultats du {{ $startDate->format('d/m/Y') }} au {{ $endDate->format('d/m/Y') }}
                        @if ($selectedSiteName)
                            — <span class="font-medium">Site : {{ $selectedSiteName }}</span>
                        @endif
                    </h2>
                </div>

                @if($users->count())
                    <table class="min-w-full text-sm divide-y divide-gray-200" id="export-table">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-6 py-3">Nom</th>
                                <th class="px-6 py-3">Fonction</th>
                                <th class="px-6 py-3">Taux salarial</th>
                                <th class="px-6 py-3">Présents</th>
                                <th class="px-6 py-3">Retards</th>
                                <th class="px-6 py-3">Absents</th>
                                <th class="px-6 py-3">Heures travaillées</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                                @php
                                    $metric = $metrics[$user->id] ?? ['total_present' => 0, 'total_late' => 0, 'total_absent' => 0, 'total_worked_hours' => 0];
                                    $heures = floor($metric['total_worked_hours']);
                                    $minutes = round(($metric['total_worked_hours'] - $heures) * 60);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $user->first_name }} {{ $user->name }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $user->fonction ? $user->fonction->nom_fonction : '-' }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $user->salary_rate ?? '-' }}</td>
                                    <td class="px-6 py-3 text-green-700 font-semibold">{{ $metric['total_present'] }}</td>
                                    <td class="px-6 py-3 text-yellow-600 font-semibold">{{ $metric['total_late'] }}</td>
                                    <td class="px-6 py-3 text-red-600 font-semibold">{{ $metric['total_absent'] }}</td>
                                    <td class="px-6 py-3 text-gray-800 font-semibold">{{ $heures }}h {{ $minutes }}m</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-4">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">Aucun utilisateur trouvé pour la période sélectionnée.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- JS dynamique --}}
    <script>
        const toggleFields = () => {
            const period = document.getElementById('period').value;
            ['day', 'week', 'month', 'year'].forEach(p => {
                document.getElementById(`${p}-field`).style.display = (period === p) ? '' : 'none';
            });
        };
        document.getElementById('period').addEventListener('change', toggleFields);
        window.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-app-layout>
