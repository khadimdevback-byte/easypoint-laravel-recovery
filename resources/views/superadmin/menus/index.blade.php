<x-app-layout>
    <div class="w-full mx-auto">
        <div class="">
            <!-- card1 -->
            <div class="w-full max-w-full px-3 mb-6">
                <div class="bg-primary text-white">
                    <div class="flex-auto">
                        <div class="flex flex-row -mx-3 flex-wrap justify-center">
                            <a href="{{ route('superadmin.menus.create') }}" id="add_button"
                                class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                {{ __('Add') }}
                                <svg width="18" height="18" class="mt-1 ms-2" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.6187 16.4259C7.6187 16.7809 7.75973 17.1214 8.01077 17.3724C8.26181 17.6234 8.60229 17.7645 8.95731 17.7645C9.31233 17.7645 9.6528 17.6234 9.90384 17.3724C10.1549 17.1214 10.2959 16.7809 10.2959 16.4259V10.6253H16.0965C16.4515 10.6253 16.792 10.4842 17.0431 10.2332C17.2941 9.98215 17.4351 9.64167 17.4351 9.28665C17.4351 8.93163 17.2941 8.59115 17.0431 8.34012C16.792 8.08908 16.4515 7.94805 16.0965 7.94805H10.2959V2.14744C10.2959 1.79242 10.1549 1.45194 9.90384 1.20091C9.6528 0.949869 9.31233 0.808838 8.95731 0.808838C8.60229 0.808838 8.26181 0.949869 8.01077 1.20091C7.75973 1.45194 7.6187 1.79242 7.6187 2.14744V7.94805H1.81809C1.46307 7.94805 1.1226 8.08908 0.87156 8.34012C0.620523 8.59115 0.479492 8.93163 0.479492 9.28665C0.479492 9.64167 0.620523 9.98215 0.87156 10.2332C1.1226 10.4842 1.46307 10.6253 1.81809 10.6253H7.6187V16.4259Z"
                                        fill="#FAFFFC" />
                                </svg>

                            </a>
                            <a href="#" id="delete_button"
                                class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                {{ __('Delete') }}
                                <svg width="14" height="16" class="mt-1 ms-2" viewBox="0 0 14 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.4268 5.50681V14.1416H3.16007V5.50681H10.4268ZM9.06426 0.325928H4.52258L3.61424 1.18941H0.435059V2.91637H13.1518V1.18941H9.9726L9.06426 0.325928ZM12.2434 3.77985H1.3434V14.1416C1.3434 15.0915 2.1609 15.8686 3.16007 15.8686H10.4268C11.4259 15.8686 12.2434 15.0915 12.2434 14.1416V3.77985Z"
                                        fill="#FAFFFC" />
                                </svg>

                            </a>
                            <a href="#" id="edit_button"
                                class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                {{ __('Edit') }}
                                <svg width="19" height="19" class="mb-1 ms-2" viewBox="0 0 19 19" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.19281 16.8276H3.64699L13.6222 6.85247L12.168 5.39829L2.19281 15.3735V16.8276ZM0.151855 18.8686V14.5316L13.6222 1.08677C13.8262 0.899687 14.0518 0.755119 14.2987 0.653072C14.5457 0.551024 14.8049 0.5 15.0763 0.5C15.3478 0.5 15.6114 0.551024 15.8672 0.653072C16.123 0.755119 16.3441 0.908191 16.5305 1.11229L17.9337 2.54096C18.1378 2.72804 18.2868 2.94915 18.3806 3.20427C18.4745 3.45938 18.5211 3.7145 18.5204 3.96962C18.5204 4.24175 18.4738 4.50129 18.3806 4.74825C18.2874 4.9952 18.1384 5.22039 17.9337 5.4238L4.48888 18.8686H0.151855ZM12.8823 6.13814L12.168 5.39829L13.6222 6.85247L12.8823 6.13814Z"
                                        fill="#FAFFFC" />
                                </svg>

                            </a>
                            <a href="#" id="disabled_button"
                                class="px-6 py-1 mr-3 flex my-auto text-center align-middle cursor-pointer hover:font-bold">
                                {{ __('Disable') }}
                                <svg width="15" height="20" class="mb-2 ms-2" viewBox="0 0 15 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.27051 19.75C1.78926 19.75 1.37742 19.573 1.03501 19.2189C0.692591 18.8648 0.521091 18.4387 0.520508 17.9405V8.89286C0.520508 8.39524 0.692008 7.9694 1.03501 7.61533C1.37801 7.26127 1.78984 7.08394 2.27051 7.08333H3.14551V5.27381C3.14551 4.02222 3.57222 2.95551 4.42563 2.07367C5.27905 1.19183 6.31067 0.750604 7.52051 0.750001C8.73034 0.749397 9.76226 1.19062 10.6163 2.07367C11.4703 2.95671 11.8967 4.02343 11.8955 5.27381V7.08333H12.7705C13.2518 7.08333 13.6639 7.26067 14.0069 7.61533C14.3499 7.97 14.5211 8.39584 14.5205 8.89286V17.9405C14.5205 18.4381 14.3493 18.8642 14.0069 19.2189C13.6645 19.5736 13.2523 19.7506 12.7705 19.75H2.27051ZM2.27051 17.9405H12.7705V8.89286H2.27051V17.9405ZM7.52051 15.2262C8.00176 15.2262 8.41388 15.0492 8.75688 14.6951C9.09988 14.341 9.27109 13.9149 9.27051 13.4167C9.26992 12.9184 9.09871 12.4926 8.75688 12.1391C8.41505 11.7857 8.00292 11.6083 7.52051 11.6071C7.03809 11.6059 6.62626 11.7833 6.28501 12.1391C5.94376 12.495 5.77226 12.9209 5.77051 13.4167C5.76876 13.9125 5.94026 14.3386 6.28501 14.6951C6.62976 15.0516 7.04159 15.2286 7.52051 15.2262ZM4.89551 7.08333H10.1455V5.27381C10.1455 4.51984 9.8903 3.87897 9.37988 3.35119C8.86946 2.82341 8.24967 2.55952 7.52051 2.55952C6.79134 2.55952 6.17155 2.82341 5.66113 3.35119C5.15072 3.87897 4.89551 4.51984 4.89551 5.27381V7.08333Z"
                                        fill="#FAFFFC" />
                                </svg>

                            </a>

                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                    {{ __('All companies') }}
                    </h6>
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

                <div class="flex flex-wrap -mx-3">
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
                                            {{ __('Nom Menu') }}
                                        </th>
                                        <th
                                            class="text-xs action-column font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @forelse($menus as $menu)
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <input id="remember_me" type="checkbox"
                                                    class="rounded checkbox border-gray-300  text-primary shadow-sm focus:ring-0"
                                                    name="menu_id" value="{{ $menu->id }}">
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $menu->name ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 action-column align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <x-dropdown align="right" width="48">
                                                    <x-slot name="trigger">
                                                        <button
                                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-slate-500 focus:outline-none transition ease-in-out duration-150">
                                                            <i class="fa-solid fa-ellipsis"></i>
                                                        </button>
                                                    </x-slot>

                                                    <x-slot name="content">
                                                        <!-- Lien édition -->
                                                        <x-dropdown-link :href="route('superadmin.menus.edit', $menu->id)" class="text-slate-500">
                                                            {{ __('Edit') }}
                                                        </x-dropdown-link>

                                                        <!-- Supprimer -->
                                                        <form method="POST"
                                                            action="{{ route('superadmin.menus.destroy', $menu) }}"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit">Supprimer</button>
                                                        </form>

                                                        {{-- <x-dropdown-link :href="route('superadmin.menu-management.edit', $menu->id) " class="text-slate-500">
                                                            {{ __('Gerer le Menu') }}
                                                        </x-dropdown-link> --}}
                                                    </x-slot>
                                                </x-dropdown>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                {{ __('Pas de Menu') }}
                                            </td>
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
        {{-- <h2>Menus existants</h2>

    <a href="{{ route('superadmin.menus.create') }}">Créer un nouveau menu</a>

    <ul>
        @foreach ($menus as $menu)
            <li>
                {{ $menu->name }}
                <a href="{{ route('superadmin.menus.edit', $menu) }}">Éditer</a>
                <form method="POST" action="{{ route('superadmin.menus.destroy', $menu) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Supprimer</button>
                </form>
            </li>
        @endforeach
    </ul> --}}
</x-app-layout>
