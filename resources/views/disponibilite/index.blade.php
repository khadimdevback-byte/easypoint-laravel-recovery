<x-app-layout>
    <div class="w-full mx-auto">
        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <br>
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Disponibilités Employés') }}
                </h1>
            </div>

            <div class="flex p-3 flex-wrap -mx-2">
                <div class="pt-2 w-full flex flex-col bg-white border shadow-sm rounded-xl ">
                    <form action="{{ route('disponibilite.index') }}" method="get">

                        <div class="flex flex-wrap">
                            <div class="m-4 xl:min-w-52 md:min-w-32">
                                <!-- Choix entre Employé et Site -->
                                <label for="filter-type" class="block mb-2 font-semibold text-gray-700">Sélectionner la
                                    Fonction</label>
                                <select id="filter-type" name="fonction"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                                    <option value="" aria-readonly="on">Selectionnez Une Fonction</option>
                                    @foreach ($fonction as $fonctions)
                                        <option value="{{ $fonctions->id }}">{{ $fonctions->nom_fonction }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Sélection du jour -->
                            <div class="m-4 xl:min-w-52 md:min-w-32" id="day-select-container">
                                <label class="block mb-2 font-semibold text-gray-700">Selectionner La date</label>
                                <input type="date" id="disponibilite" name="date" value="{{ request('date') }}"
                                    class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                            </div>
                            <div class="my-auto">
                                <x-primary-button type="submit"
                                    class="flex ml-4 justify-center shadow-md text-sm h-12 items-center text-center mt-6">
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>

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

            <div class="mb-6 py-3">
                <!-- card 1 -->

                <div class="export-buttons flex justify-end space-x-4 my-5">
                    <button id="export-excel"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center space-x-2">
                        <i class="fas fa-file-excel"></i>
                        <span class="text-xs">Export Excel</span>
                    </button>
                    <button id="export-csv"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center space-x-2">
                        <i class="fas fa-file-alt"></i>
                        <span class="text-xs">Export CSV</span>
                    </button>
                    <button id="export-pdf"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow flex items-center space-x-2">
                        <i class="fas fa-file-pdf"></i>
                        <span class="text-xs">Export PDF</span>
                    </button>
                </div>

                <div class="flex flex-wrap -mx-60">
                    <div class="flex-none w-full max-w-full px-3 pb-40 2xl:overflow-visible overflow-x-auto">
                        <div class="bg-white ">

                            <table
                                class="min-w-full table-auto divide-y divide-gray-200 border border-separate rounded-lg"
                                id="export-table">
                                <thead class="align-bottom bg-gray-50 rounded-2xl">
                                    <tr>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('#') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Nom') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Email') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Télephone') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Adresse') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Fonction') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Disponibilité') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Date de Disponibilité') }}
                                        </th>

                                        {{-- <th
                                            class="text-xs action-column font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th> --}}
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @forelse($disponibilites as $dispo)
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <input id="remember_me" type="checkbox"
                                                    class="rounded checkbox border-gray-300 text-primary shadow-sm focus:ring-0"
                                                    name="disponibilite_id" value="{{ $dispo->id }}">
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->name ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->email ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->phone ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->address ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal w-40 truncate">
                                                    {{ $dispo->nom_fonction ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->disponibilite ?? '-' }}</p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $dispo->date_disponibilite ?? '-' }}</p>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent"
                                                colspan="8">{{ __('Aucun contenu') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

                {{-- <div class="mt-6 justify-end">
                    {{ $companies->links() }}
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
