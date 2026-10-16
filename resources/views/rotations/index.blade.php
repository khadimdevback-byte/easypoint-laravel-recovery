<x-app-layout>
    <div class="w-full mx-auto">
        <!-- row 1 -->
        <div class="">
            <!-- card1 -->
            <div class="w-full max-w-full px-3 mb-6">
                <div class="bg-primary text-white">
                    <div class="flex-auto">
                        @if (is_admin())
                            <div class="flex flex-row -mx-3 flex-wrap justify-center">
                                <a href="{{ route('rotation.create') }}" id="add_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Ajouter') }}
                                    <svg width="18" height="18" class="mt-1 ms-2" viewBox="0 0 18 18"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.6187 16.4259C7.6187 16.7809 7.75973 17.1214 8.01077 17.3724C8.26181 17.6234 8.60229 17.7645 8.95731 17.7645C9.31233 17.7645 9.6528 17.6234 9.90384 17.3724C10.1549 17.1214 10.2959 16.7809 10.2959 16.4259V10.6253H16.0965C16.4515 10.6253 16.792 10.4842 17.0431 10.2332C17.2941 9.98215 17.4351 9.64167 17.4351 9.28665C17.4351 8.93163 17.2941 8.59115 17.0431 8.34012C16.792 8.08908 16.4515 7.94805 16.0965 7.94805H10.2959V2.14744C10.2959 1.79242 10.1549 1.45194 9.90384 1.20091C9.6528 0.949869 9.31233 0.808838 8.95731 0.808838C8.60229 0.808838 8.26181 0.949869 8.01077 1.20091C7.75973 1.45194 7.6187 1.79242 7.6187 2.14744V7.94805H1.81809C1.46307 7.94805 1.1226 8.08908 0.87156 8.34012C0.620523 8.59115 0.479492 8.93163 0.479492 9.28665C0.479492 9.64167 0.620523 9.98215 0.87156 10.2332C1.1226 10.4842 1.46307 10.6253 1.81809 10.6253H7.6187V16.4259Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                                <a href="#" id="delete_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Delete') }}
                                    <svg width="14" height="16" class="mt-1 ms-2" viewBox="0 0 14 16"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.4268 5.50681V14.1416H3.16007V5.50681H10.4268ZM9.06426 0.325928H4.52258L3.61424 1.18941H0.435059V2.91637H13.1518V1.18941H9.9726L9.06426 0.325928ZM12.2434 3.77985H1.3434V14.1416C1.3434 15.0915 2.1609 15.8686 3.16007 15.8686H10.4268C11.4259 15.8686 12.2434 15.0915 12.2434 14.1416V3.77985Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                                <a href="#" id="edit_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Edit') }}
                                    <svg width="19" height="19" class="mb-1 ms-2" viewBox="0 0 19 19"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.19281 16.8276H3.64699L13.6222 6.85247L12.168 5.39829L2.19281 15.3735V16.8276ZM0.151855 18.8686V14.5316L13.6222 1.08677C13.8262 0.899687 14.0518 0.755119 14.2987 0.653072C14.5457 0.551024 14.8049 0.5 15.0763 0.5C15.3478 0.5 15.6114 0.551024 15.8672 0.653072C16.123 0.755119 16.3441 0.908191 16.5305 1.11229L17.9337 2.54096C18.1378 2.72804 18.2868 2.94915 18.3806 3.20427C18.4745 3.45938 18.5211 3.7145 18.5204 3.96962C18.5204 4.24175 18.4738 4.50129 18.3806 4.74825C18.2874 4.9952 18.1384 5.22039 17.9337 5.4238L4.48888 18.8686H0.151855ZM12.8823 6.13814L12.168 5.39829L13.6222 6.85247L12.8823 6.13814Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <br>
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Listes des Parametrages Rotation et Repartition') }}
                </h1>
            </div>

            {{-- <div class="flex p-3 flex-wrap -mx-3">
                <div class="pt-2 w-full flex flex-col bg-white border shadow-sm rounded-xl ">
                    <form action="" method="get">

                        <div class="flex flex-wrap">

                            <div class="m-4 xl:min-w-52 md:min-w-32" id="day-select-container" >
                                <label class="block mb-2 font-semibold text-gray-700">Offre</label>
                                <input type="text"  name="filtrerOffre" class="py-3 px-4 block w-full border-gray-200 rounded-lg border">
                            </div>
                            <div class="my-auto">
                                <x-primary-button type="submit"
                                    class="flex ml-4 justify-center shadow-md text-sm h-12 items-center text-center mt-6">
                                    {{ __('Filtrer') }}
                                </x-primary-button>
                            </div>
                            <div class="my-auto">
                                <a href="" class="flex ml-4 justify-center  text-sm h-12 items-center text-center mt-6">
                                    <x-secondary-button type="button">
                                        {{ __('Actualiser') }}
                                    </x-secondary-button>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

            </div> --}}

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
                {{-- <div class="flex justify-end space-x-4 my-5">
                    <a href="{{ route('offres.grouper') }}" class="flex ml-4 justify-center  text-sm h-12 items-center text-center mt-6">
                        <x-secondary-button type="button">
                            {{ __('Liste Des rotations et repartition') }}
                        </x-secondary-button>
                    </a>

                    <a href="{{ route('candidatures.index') }}" class="flex ml-4 justify-center  text-sm h-12 items-center text-center mt-6">
                        <x-secondary-button type="button">
                            {{ __('Liste Des Candidatures') }}
                        </x-secondary-button>
                    </a>
                </div> --}}

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
                                            {{ __('Nom Rotation') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Système') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Durée du Cycle') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Jours de Travail') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Jours de Repos') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Date de Début') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Repartition') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Status') }}
                                        </th>

                                        <th
                                            class="text-xs action-column font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @forelse($rotations as $rotation)
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <input id="remember_me" type="checkbox"
                                                    class="rounded checkbox border-gray-300  text-primary  focus:ring-0"
                                                    name="rotation_id" value="{{ $rotation->id }}">
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->nom ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->systeme ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->duree_cycle ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->nombre_jours_travail ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->nombre_jours_repos ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $rotation->date_debut ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td>
                                                    <ul>
                                                        @foreach ($rotation->repartitions as $repartition)
                                                            <li>
                                                                {{ ucfirst($repartition->periode) }} :
                                                                {{ \Carbon\Carbon::parse($repartition->heure_debut)->format('H:i') }} -
                                                                {{ \Carbon\Carbon::parse($repartition->heure_fin)->format('H:i') }}
                                                                {{-- ({{ $repartition->nombre_jours }} jours) --}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <button type="button"
                                                    class="inline-block text-sm py-1 px-6 font-bold text-center capitalize align-middle border rounded-md
                                                    {{ $rotation->statut == 'active' ? 'text-primary bg-primary-2 border-green-700' : 'text-danger bg-danger-2 border-red-700' }}">
                                                    {{ __($rotation->statut) ?? '-' }}
                                                </button>
                                            </td>
                                            <td
                                                class="p-2 action-column align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <x-dropdown align="right" width="48">
                                                    <x-slot name="trigger">
                                                        <button
                                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-slate-500  focus:outline-none transition ease-in-out duration-150">
                                                            <i class="fa-solid fa-ellipsis"></i>
                                                        </button>
                                                    </x-slot>

                                                    <x-slot name="content">
                                                        <x-dropdown-link class="text-slate-500"
                                                            href="{{ route('rotation.edit', $rotation->id) }}">
                                                            {{ __('Modifier') }}
                                                        </x-dropdown-link>


                                                        <!-- Delete user -->
                                                        <form method="POST" id="delete-form-{{ $rotation->id }}"
                                                            action="{{ route('rotation.delete', $rotation->id) }}"
                                                            class=" cursor-pointer">
                                                            @csrf
                                                            @method('DELETE')

                                                            <x-dropdown-link class="text-slate-500"
                                                                onclick="event.preventDefault(); if(confirm('Êtes-vous sûr de vouloir supprimer cette rotation ?')) document.getElementById('delete-form-{{ $rotation->id }}').submit();">
                                                                {{ __('Supprimer') }}
                                                            </x-dropdown-link>
                                                        </form>
                                                    </x-slot>
                                                </x-dropdown>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                {{ __('Pas de rotation et de repartition') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6 justify-end">
                    {{-- {{ $fonction->links() }} --}}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        // Fonction pour vérifier l'état des checkboxes
        function updateButtons() {
            var selectedCheckboxes = $(".checkbox:checked");

            if (selectedCheckboxes.length > 0) {
                $("#add_button").addClass("disabled").prop("disabled", true);
            } else {
                $("#add_button").removeClass("disabled").prop("disabled", false);

            }

            if (selectedCheckboxes.length === 1) {
                $("#delete_button").removeClass("disabled").prop("disabled", false);
                $("#edit_button").removeClass("disabled").prop("disabled", false);
                $("#disabled_button").removeClass("disabled").prop("disabled", false);
            }
            //  else if (selectedCheckboxes.length > 1) {
            //     $("#edit_button").addClass("disabled").prop("disabled", true);
            //     $("#disabled_button").addClass("disabled").prop("disabled", true);
            // }
            else {
                $("#delete_button").addClass("disabled").prop("disabled", true);
                $("#edit_button").addClass("disabled").prop("disabled", true);
                $("#disabled_button").addClass("disabled").prop("disabled", true);
            }
        }

        // Événement lors du changement de l'état d'un checkbox
        $(".checkbox").on("change", function() {
            updateButtons();
        });

        // Initialiser l'état des boutons
        updateButtons();


        $('#edit_button').on('click', function() {
            var selectedCheckbox = $('input[name="rotation_id"]:checked').first();

            if (selectedCheckbox.length) {
                var OffreId = selectedCheckbox.val();
                var editRoute = "".replace(':id', OffreId);
                window.location.href = editRoute;
            }
        });

        $('#delete_button').on('click', function() {
            var selectedCheckbox = $('input[name="rotation_id"]:checked').first();

            if (selectedCheckbox.length) {
                var OffreId = selectedCheckbox.val();
                confirmDelete(OffreId);
            }
        });



        $('#disabled_button').on('click', function() {
            var selectedCheckbox = $('input[name="site_id"]:checked').first();

            if (selectedCheckbox.length) {
                $('#disableModal').click();
            } else {
                alert('Veuillez sélectionner un utilisateur.');
            }
        });


        // Confirmer et soumettre le formulaire
        $('#confirmDisable').on('click', function() {
            var selectedCheckbox = $('input[name="site_id"]:checked').first();

            if (selectedCheckbox.length) {
                var siteId = selectedCheckbox.val();
                var disableRoute = "{{ route('sites.disabled', ':id') }}".replace(':id', siteId);

                var reason = $('#disableReason').val().trim();

                if (!reason) {
                    alert('Veuillez entrer un motif.');
                    return;
                }

                var form = document.getElementById('disable-form-' + siteId);
                form.attr('action', disableRoute);

                // Ajouter le motif au formulaire
                if (!form.find('input[name="reason"]').length) {
                    form.append('<input type="hidden" name="reason" value="' + reason + '" required >');
                } else {
                    form.find('input[name="reason"]').val(reason);
                }

                // Soumettre le formulaire
                form.submit();
            }

            // Fermer la modale
            $('#disableModal').addClass('hidden');
        });

    });

    function confirmDisable(siteId) {
        $('input[name="site_id"][value="' + siteId + '"]').prop('checked', true);
        $('#disableModal').click();
    }

    function confirmDelete(siteId) {
        // Afficher la boîte de confirmation
        var confirmation = window.confirm("Êtes-vous sûr de vouloir supprimer cela ?");

        if (confirmation) {
            // Si l'utilisateur confirme, soumettre le formulaire
            var form = document.getElementById('delete-form-' + siteId);
            form.submit();
        }
    }
</script>
