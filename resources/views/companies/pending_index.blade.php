<x-app-layout>

    <!-- cards -->
    <div class="w-full mx-auto">

        <div class="md:w-9/12  2xl:w-8/12 mx-auto">
            <div class="px-6 mt-10">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                    {{ __('Toutes les sociétés en attente') }}
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

                <div class="flex flex-wrap -mx-3">
                    <div class="flex-none w-full max-w-full px-3 mb-20 2xl:overflow-visible overflow-x-auto ">
                        <div class="bg-white ">

                            <table
                                class="min-w-full table-auto divide-y divide-gray-200 border border-separate rounded-lg">
                                <thead class="align-bottom bg-gray-50 rounded-2xl">
                                    <tr>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('#') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Company name') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Company email') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Phone') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Address') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Status') }}
                                        </th>

                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @forelse($companies as $company)
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <input id="remember_me" type="checkbox"
                                                    class="rounded checkbox border-gray-300  text-primary shadow-sm focus:ring-0"
                                                    name="user_id" value="{{ $company->id }}">
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <a href="{{ route('companies.show', $company->id) }}">
                                                    <div class="flex px-2 py-1">
                                                        <div class="h-9 w-9">
                                                            <img src="{{ asset('storage/' . $company->logo) }}"
                                                                class="inline-flex items-center justify-center mr-4 object-cover text-sm text-white transition-all duration-200 ease-soft-in-out h-9 w-9 rounded-full"
                                                                alt="user image">
                                                        </div>
                                                        <div class="flex flex-col justify-center ms-2">
                                                            <h6 class="mb-0 text-sm leading-normal">
                                                                {{ $company->compagny_name ?? '' }}
                                                            </h6>

                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $company->compagny_email ?? '-' }}
                                                </p>
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $company->phone ?? '-' }}
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal w-40 truncate">
                                                    {{ $company->address ?? '-' }}
                                                </p>
                                            </td>


                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <button type="button"
                                                    class="inline-block text-sm py-1 px-6 font-bold text-center pointer-events-none capitalize align-middle border rounded-md
                                                        text-orange-500 bg-orange-100 border-orange-500

                                                     ">
                                                    {{ __($company->status) ?? '-' }}
                                                </button>


                                            </td>

                                            <td>
                                                <form method="POST" id="disabled-form-{{ $company->id }}"
                                                    action="{{ route('companies.disabled', $company->id) }}"
                                                    class=" cursor-pointer">
                                                    @csrf
                                                    @method('PATCH')

                                                    <x-primary-button class="text-sm"
                                                        onclick="event.preventDefault(); confirmDisable({{ $company->id }});">
                                                        {{ __('Valider') }}
                                                    </x-primary-button>

                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                {{ __('No Content') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6 justify-end">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>

        <!-- Modal -->


        <div class="container">

            <button id="disableModal" type="button"
                class="py-3 hidden px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-slide-down-animation-modal"
                data-hs-overlay="#hs-slide-down-animation-modal">
                Confirmer
            </button>

            <div id="hs-slide-down-animation-modal"
                class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none"
                role="dialog" tabindex="-1" aria-labelledby="hs-slide-down-animation-modal-label">
                <div
                    class="hs-overlay-animation-target hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                    <div class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto">
                        <div class="flex justify-between items-center py-3 px-4 border-b">
                            <h3 id="hs-slide-down-animation-modal-label" class="font-bold  text-gray-800">
                                Confirmer la modification du statut
                            </h3>
                            <button type="button"
                                class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none"
                                aria-label="Close" data-hs-overlay="#hs-slide-down-animation-modal">
                                <span class="sr-only">Close</span>
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"></path>
                                    <path d="m6 6 12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4 overflow-y-auto">
                            <p class="text-black">Motif *</p>
                            <textarea id="disableReason" placeholder="Entrez le motif"
                                class="w-full text-black p-2 border rounded-lg focus:outline-none" required></textarea>
                        </div>
                        <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">

                            <x-secondary-button onclick="history.back()" class="flex mr-4 justify-center shadow-md"
                                data-hs-overlay="#hs-slide-down-animation-modal">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button id="confirmDisable"
                                class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                                {{ __('Record') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- end cards -->

</x-app-layout>

<script>
    $(document).ready(function() {

        // Confirmer et soumettre le formulaire
        $('#confirmDisable').on('click', function() {
            var selectedCheckbox = $('input[name="user_id"]:checked').first();

            if (selectedCheckbox.length) {
                var userId = selectedCheckbox.val();
                var disableRoute = "{{ route('companies.disabled', ':id') }}".replace(':id', userId);

                var reason = $('#disableReason').val().trim();

                if (!reason) {
                    alert('Veuillez entrer un motif.');
                    return;
                }

                var form = $('#disabled-form-' + userId);
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

    function confirmDisable(userId) {
        $('input[name="user_id"][value="' + userId + '"]').prop('checked', true);
        $('#disableModal').click();
    }
</script>
