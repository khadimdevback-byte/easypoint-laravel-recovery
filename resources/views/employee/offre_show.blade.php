<x-app-layout>
    <div class="container md:w-9/12  2xl:w-8/12 mx-auto my-10 pb-10">
        <div class="w-full lg:px-6 pt-5 mx-auto">

            <div class="mb-10">
                <h2 class=" font-bold mb-4 text-2xl text-black">Détail du Site</h2>


                <div class="my-10">
                    <div class="flex justify-between">
                        <div class="flex items-center gap-2 text-lg text-green-700">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
                                    <path
                                        d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z" />
                                    <path
                                        d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-lg"> {{ $offre['name'] }} </p>
                        </div>

                    </div>
                    <p class="my-5 text-green-700 text-lg">Démarage: <span id="modalDemarrage">
                            {{ $offre['demarrage'] === 'immediat' ? 'Immediat' : 'A partir de ' + $offre['start_date'] }}
                        </span></p>
                    <div class="flex items-center gap-2 my-2">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar3" viewBox="0 0 16 16">
                                <path
                                    d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z" />
                                <path
                                    d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                            </svg>
                        </div>
                        <p id="modalHoraire"> {{ $offre['horaire'] }} </p>
                    </div>

                    <p class="text-gray-500 my-4">
                        <span class="font-bold text-lg">Lieux:</span> <span id="modalAddress"> {{ $offre['address'] }}
                        </span>
                    </p>
                    <p class="text-gray-500 my-4">
                        <span class="font-bold text-lg">Taux horaire:</span> <span id="modalTauxHoraire">
                            {{ $offre['taux_horaire'] }} </span>
                    </p>
                    <p class="text-gray-500 my-4">
                        <span class="font-bold text-lg">Description:</span> <span id="modalDescription">
                            {{ $offre['description_offre'] }} </span>
                    </p>
                </div>

                <div class="mt-20 flex gap-x-10">
                    <button type="button" onclick="Accepter(this)" data-id="{{ $offre['id'] }}"
                        class="py-2 px-32 inline-flex items-center gap-x-2  font-bold rounded-lg border border-transparent bg-green-700 text-white hover:bg-green-700 focus:outline-hidden focus:bg-green-700 disabled:opacity-50 disabled:pointer-events-none">
                        Accepter
                    </button>
                    <button type="button" onclick="Decliner(this)" data-id="{{ $offre['id'] }}"
                        class="py-2 px-32 inline-flex items-center gap-x-2  font-bold rounded-lg border border-red-200 bg-white text-red-800 shadow-2xs hover:bg-red-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-red-50">
                        Décliner
                    </button>
                </div>


            </div>



        </div>
    </div>


    <script>
        function Accepter(button) {
            const offreId = button.getAttribute("data-id");

            Swal.fire({
                title: "Voudrez-vous accepter l'offre ?",
                text: "Confirmer l'acceptation de l'offre",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Oui, j'accepte",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `/offre/employee/${offreId}/postuler`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Succès!",
                                text: response.message ||
                                    "Votre candidature a été envoyée avec succès.",
                                icon: "success"
                            });
                        },
                        error: function(xhr) {
                            let message = "Une erreur est survenue.";

                            // Traitement spécifique selon le code HTTP

                            message = xhr.responseJSON?.message || "Erreur inconnue.";


                            Swal.fire("Erreur", message, "error");
                        }
                    });
                }
            });
        }



        function Decliner(button) {
            const offreId = button.getAttribute("data-id");

            Swal.fire({
                title: 'Décliner l\'offre',
                input: 'text',
                inputLabel: 'Motif du rejet',
                inputPlaceholder: 'Entrez la raison du refus',
                inputAttributes: {
                    maxlength: 255,
                    required: true
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Le motif est requis.';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Envoyer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                icon: 'warning'
            }).then((result) => {
                if (result.isConfirmed) {
                    const motif = result.value;

                    $.ajax({
                        type: "POST",
                        url: `/candidature/employee/${offreId}/decliner`,
                        data: {
                            _token: '{{ csrf_token() }}',
                            motif_rejet: motif
                        },
                        success: function(response) {
                            Swal.fire('Succès', response.message || 'Candidature déclinée.', 'success');
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || "Erreur inattendue.";
                            Swal.fire('Erreur', message, 'error');
                        }
                    });
                }
            });
        }
    </script>



</x-app-layout>
