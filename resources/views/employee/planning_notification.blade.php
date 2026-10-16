<x-app-layout>
    <div class="container md:w-9/12  2xl:w-8/12 mx-auto pb-10">
        <div class="w-full lg:px-6 pt-5 mx-auto">

            <div class="mb-10">
                <h2 class=" font-bold mb-4 text-2xl text-black">Planning & Notification</h2>

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


                <div class="p-10 my-10 rounded-lg border shadow">
                    <h3 class=" mb-4 text-xl text-black"> Sélectionner votre disponibilité</h3>

                    <form action="{{ route('employee.planning_notification.store') }}" method="post">
                        @csrf
                        <div class="grid lg:grid-cols-3 items-center gap-4 my-10">

                            <div class="mt-4">

                                <x-select-input name="disponibilite" id="disponibilite" :options="['immediat' => 'Immediat', 'A_partir_de' => 'A partir de']"
                                    :selected="old('disponibilite', $disponibilite->disponibilite ?? '')" :disabled="false" required />
                                <x-input-error :messages="$errors->get('disponibilite')" class="mt-2" />
                            </div>

                            <div id="date_disponibilite_div" class="hidden">

                                <x-text-input-2 id="date_disponibilite" name="date_disponibilite" :value="$disponibilite->date_disponibilite ?? ''"
                                    type="date" min="{{ date('Y-m-d') }}" />
                                <x-input-error :messages="$errors->get('date_disponibilite')" class="mt-2" />
                            </div>

                            <x-primary-button class="flex ml-4 py-3 justify-center shadow-md shadow-green-600/50">
                                {{ __('Record') }}
                            </x-primary-button>


                        </div>
                    </form>
                </div>
                <div class="p-10 my-10">
                    <h3 class="text-2xl text-black font-bold mb-5"> Offres </h3>

                    @if ($disponibilite)
                        @if ($offres->count() > 0)

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 item-center">
                                @foreach ($offres as $offre)
                                    <a href="{{ route('employee.planning_notification.show', $offre->id) }}">
                                        <div class="p-4 shadow border rounded-lg" aria-haspopup="dialog"
                                            aria-expanded="false" aria-controls="hs-sign-out-alert"
                                            data-hs-overlay="#hs-sign-out-alert" data-offre-id="{{ $offre->id }}">
                                            <div class="flex justify-between">
                                                <div class="flex items-center gap-2 text-lg text-green-700">
                                                    <div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-buildings"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z" />
                                                            <path
                                                                d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z" />
                                                        </svg>
                                                    </div>
                                                    <p>{{ $offre->name }}</p>
                                                </div>
                                                <div class="bg-yellow-500 px-2 rounded-3xl">
                                                    <p class="text-white">{{ $offre->Status ?? 'Offre' }}</p>
                                                </div>
                                            </div>
                                            <p class="my-2"> <span class="font-bold">Lieux: </span>
                                                {{ $offre->address }}
                                            </p>
                                            <p class="my-2 text-green-700">Démarage:
                                                @if ($offre->demarrage === 'immediat')
                                                    Immediat
                                                @else
                                                    A partir de {{ $offre->start_date }}
                                                @endif
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="16" fill="currentColor" class="bi bi-calendar3"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z" />
                                                        <path
                                                            d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                                                    </svg>
                                                </div>
                                                <p>{{ $offre->horaire_travail }}</p>
                                            </div>
                                        </div>

                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-black text-2xl ">Aucune offre disponible</p>
                        @endif
                    @else
                        <p class="text-black text-2xl text-center"> Veuillez définir votre disponibilité </p>
                    @endif



                </div>

            </div>

        </div>
    </div>






    <script>
        $(document).ready(function() {
            // Gérer la logique de date
            function toggleDateDisponibilite() {
                if ($('#disponibilite').val() === 'A_partir_de') {
                    $('#date_disponibilite_div').show();
                } else {
                    $('#date_disponibilite_div').hide();
                }
            }

            toggleDateDisponibilite();
            $('#disponibilite').on('change', toggleDateDisponibilite);


        });
    </script>


</x-app-layout>
