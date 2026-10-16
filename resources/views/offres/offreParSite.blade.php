<x-app-layout>
    <div class="w-full mx-auto">
        <!-- row 1 -->
        <div class="">
            <!-- card1 -->

        </div>
        <br><br><br><br>
        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <br>
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Offre Par Site') }}
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


                <div class="flex flex-wrap -mx-60">
                    <div class="flex-none w-full max-w-full px-3 pb-40 2xl:overflow-visible overflow-x-auto">
                        <div class="bg-white ">

                            <table
                                class="min-w-full table-auto divide-y divide-gray-200 border border-separate rounded-lg"
                                id="export-table">
                                <thead class="align-bottom bg-gray-50 rounded-2xl">
                                    <tr>
                                        {{-- <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('#') }}
                                        </th> --}}

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Nom du Site') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Poste') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Taux Horaire') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Nombre de Personne Recherché') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Démarage') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Date Demarage') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Description') }}
                                        </th>

                                        {{-- <th
                                            class="text-xs action-column font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th> --}}
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @foreach ($offres as $siteId => $offresDuSite)
                                        <tr>

                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent"
                                                rowspan="{{ $offresDuSite->count() }}"
                                                style="background-color: #f5f5f5; font-weight: bold;">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->name ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()?->fonction)->nom_fonction ?? 'Aucune fonction' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->taux_horaire ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->nombre_personne_recherche ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->demarrage ?? '-' }}
                                                    {{-- {{ $offresDuSite->first()->demarrage ?? '-' }} --}}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->start_date ?? '-' }}
                                                    {{-- {{$offresDuSite->first()->start_date ?? '-' }} --}}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ optional($offresDuSite->first()->site)->description_offre ?? '-' }}
                                                    {{-- {{ $offresDuSite->first()->description_offre ?? '-' }} --}}
                                                </p>
                                                </p>
                                            </td>
                                        </tr>
                                        @foreach ($offresDuSite->slice(1) as $offre)
                                            <tr>

                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ optional($offre->fonction)->nom_fonction ?? 'Aucune fonction' }}

                                                    </p>
                                                    </p>
                                                </td>
                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ $offre->taux_horaire ?? '-' }}
                                                    </p>
                                                    </p>
                                                </td>
                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ $offre->nombre_personne_recherche ?? '-' }}
                                                    </p>
                                                    </p>
                                                </td>
                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ $offre->demarrage ?? '-' }}
                                                    </p>
                                                    </p>
                                                </td>
                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ $offre->start_date ?? '-' }}
                                                    </p>
                                                    </p>
                                                </td>
                                                <td
                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                    <p class="mb-0 font-semibold text-sm leading-normal">
                                                        {{ $offre->description_offre ?? '-' }}
                                                    </p>
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
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

{{-- <script>
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
            var selectedCheckbox = $('input[name="offre_id"]:checked').first();

            if (selectedCheckbox.length) {
                var OffreId = selectedCheckbox.val();
                var editRoute = "{{ route('offres.edit', ':id') }}".replace(':id', OffreId);
                window.location.href = editRoute;
            }
        });

        $('#delete_button').on('click', function() {
            var selectedCheckbox = $('input[name="offre_id"]:checked').first();

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
</script> --}}
