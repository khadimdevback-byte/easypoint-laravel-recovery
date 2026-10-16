<x-app-layout>
    <style>
        #map {
            height: 400px;
        }

        #search {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 5px;
        }

        #results {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: relative;
            background: white;
            z-index: 1000;
            display: none;
        }

        #results div {
            padding: 10px;
            cursor: pointer;
        }

        #results div:hover {
            background: #f0f0f0;
        }
    </style>
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
         <div class="px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                {{ __('Modifier un lieu de travail') }}
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
        <div class=" my-10 px-6 py-6">
            <form method="POST" action="{{ route('site_rotation.update', $siteRotation->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="name" :value="__('Nom du site *')" />
                        <x-text-input-2 id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $siteRotation->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="timezone" :value="__('Fuseau Horaire ')" />
                        <x-select-input name="timezone" :options="$timezones" :selected="old('timezone', $siteRotation->timezone)" />
                        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="status" :value="__('Status *')" />
                        <x-select-input name="status" :options="['active' => 'Active', 'inactive' => 'Inactive']"
                            :selected="old('status', $siteRotation->status)" required />
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="panier" :value="__('Panier')" />
                        <x-text-input-2 id="panier" name="panier" type="number"
                            :value="old('panier', $siteRotation->panier)" />
                        <x-input-error :messages="$errors->get('panier')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="5" placeholder="Écrivez ici..."
                            class="block w-full bg-secondary border-t-0 border-r-0 border-l-0 border-b-2 text-black border-primary-color pt-3 mt-3 focus:ring-0 focus:border-primary-color rounded-md pl-4">{{ old('description', $siteRotation->description) }}</textarea>
                    </div>
                </div>

                <hr class="border mt-10  border-primary-color border-solid opacity-100">

                <div class="mt-7">
                    <h3 class="font-semibold text-lg mb-4">Périodes</h3>

                    <!-- Matin -->
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="heure_debut_matin" :value="__('Début matin')" />
                            <x-text-input-2 id="heure_debut_matin" name="heure_debut_matin" type="time"
                                :value="old('heure_debut_matin', $siteRotation->heure_debut_matin)" />
                        </div>
                        <div>
                            <x-input-label for="heure_fin_matin" :value="__('Fin matin')" />
                            <x-text-input-2 id="heure_fin_matin" name="heure_fin_matin" type="time"
                                :value="old('heure_fin_matin', $siteRotation->heure_fin_matin)" />
                        </div>
                    </div>

                    <!-- Après-midi -->
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="heure_debut_apres_midi" :value="__('Début après-midi')" />
                            <x-text-input-2 id="heure_debut_apres_midi" name="heure_debut_apres_midi" type="time"
                                :value="old('heure_debut_apres_midi', $siteRotation->heure_debut_apres_midi)" />
                        </div>
                        <div>
                            <x-input-label for="heure_fin_apres_midi" :value="__('Fin après-midi')" />
                            <x-text-input-2 id="heure_fin_apres_midi" name="heure_fin_apres_midi" type="time"
                                :value="old('heure_fin_apres_midi', $siteRotation->heure_fin_apres_midi)" />
                        </div>
                    </div>

                    <!-- Nuit -->
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="heure_debut_nuit" :value="__('Début nuit')" />
                            <x-text-input-2 id="heure_debut_nuit" name="heure_debut_nuit" type="time"
                                :value="old('heure_debut_nuit', $siteRotation->heure_debut_nuit)" />
                        </div>
                        <div>
                            <x-input-label for="heure_fin_nuit" :value="__('Fin nuit')" />
                            <x-text-input-2 id="heure_fin_nuit" name="heure_fin_nuit" type="time"
                                :value="old('heure_fin_nuit', $siteRotation->heure_fin_nuit)" />
                        </div>
                    </div>

                    <!-- Marquage tardif -->
                    <div class="mt-4">
                        <x-input-label for="late_mark_duration" :value="__('Marquage tardif après (minutes)')" />
                        <x-text-input-2 id="late_mark_duration" name="late_mark_duration" type="number"
                            :value="old('late_mark_duration', $siteRotation->late_mark_duration)" />
                        <x-input-error :messages="$errors->get('late_mark_duration')" class="mt-2" />
                    </div>
                </div>

                <hr class="border mt-10  border-primary-color border-solid opacity-100">
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <x-input-label class="font-bold  my-5" :value="__('Localisation')" />

                    <button type="button" id="getLocation" class="font-semibold text-primary underline"><i
                            class="fa-solid fa-location-dot mr-2"></i>Cliquer pour utiliser votre position actuelle</button>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="address" :value="__('Adresse *')" />
                        <x-text-input-2 id="address" name="address" type="text"
                            :value="old('address', $siteRotation->address)" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="radius" :value="__('Rayon (en mètre)*')" />
                        <x-text-input-2 id="radius" name="radius" type="number"
                            :value="old('radius', $siteRotation->radius)" required />
                        <x-input-error :messages="$errors->get('radius')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="latitude" :value="__('Latitude *')" />
                        <x-text-input-2 id="latitude" name="latitude" type="text"
                            :value="old('latitude', $siteRotation->latitude)" required />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="longitude" :value="__('Longitude *')" />
                        <x-text-input-2 id="longitude" name="longitude" type="text"
                            :value="old('longitude', $siteRotation->longitude)" required />
                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                    </div>
                </div>

                <div class="w-full mt-7">
                    <x-input-label for="search" :value="__('Rechercher un lieu')" />
                    <div class="md:grid grid-cols-2 gap-16 mb-4">
                        <x-text-input-2 type="text" id="search" placeholder="Chercher un lieu..." />
                    </div>
                    <div id="results" class="mb-7"></div>
                    <div id="map"></div>
                </div>

                <div class="my-24 flex">
                    <x-secondary-button onclick="history.back()" class="flex mr-4 justify-center shadow-md">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Mettre à jour') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un site rotatif') }}
        </h2>
    </x-slot> --}}



    <!-- Leaflet -->
       <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
        $(document).ready(function() {
            // Initialisation de Select2
            $('#items').select2({
                placeholder: "Sélectionnez des options",
                allowClear: true,
                width: '100%',
                theme: 'classic',
            });

            // Initialisation de la carte avec Leaflet
            const map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées par défaut : Paris

            // Ajouter les tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Ajouter un marqueur initial
            let marker = L.marker([48.8566, 2.3522], {
                draggable: true
            }).addTo(map);

            // Mettre à jour les champs hidden avec les coordonnées et l'adresse
            function updateHiddenFields(lat, lng, address = '') {
                $('#latitude').val(lat);
                $('#longitude').val(lng);
                $('#address').val(address);
            }

            // Mettre à jour la carte et le marqueur
            function updateMap(lat, lng) {
                const newLatLng = L.latLng(lat, lng);
                marker.setLatLng(newLatLng);
                map.setView(newLatLng, 13);
                fetchAddress(lat, lng);
            }

            // Fonction pour obtenir l'adresse avec Nominatim
            function fetchAddress(lat, lng) {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
                $.getJSON(url, function(data) {
                    const address = data.display_name || 'Adresse introuvable';
                    $('#address').val(address);
                });
            }

            // Recherche d'un lieu avec Nominatim
            function searchLocation(query) {
                const url =
                    `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&addressdetails=1&limit=5`;
                $.getJSON(url, function(data) {
                    showResults(data);
                });
            }

            // Afficher les résultats dans une liste
            function showResults(data) {
                const resultsContainer = $('#results');
                resultsContainer.empty(); // Vider les résultats précédents

                if (data.length > 0) {
                    resultsContainer.show(); // Afficher la liste déroulante
                    data.forEach(location => {
                        const lat = parseFloat(location.lat);
                        const lon = parseFloat(location.lon);
                        const displayName = location.display_name;

                        // Ajouter chaque résultat comme un div
                        const resultItem = $(`<div>${displayName}</div>`);
                        resultItem.on('click', function() {
                            // Lorsqu'un lieu est cliqué, mettre à jour la carte et les champs
                            updateMap(lat, lon);
                            updateHiddenFields(lat, lon, displayName);
                            resultsContainer.hide(); // Masquer les résultats après sélection
                            $('#search').val(
                                displayName); // Mettre le nom dans la barre de recherche
                        });
                        resultsContainer.append(resultItem);
                    });
                } else {
                    resultsContainer.hide(); // Masquer si aucun résultat
                }
            }

            // Écouteurs d'événements pour le champ de recherche
            $('#search').on('input', function() {
                const query = $(this).val();
                if (query.length > 3) {
                    searchLocation(query);
                } else {
                    $('#results').hide(); // Masquer les résultats si la recherche est vide ou trop courte
                }
            });

            // Mise à jour après déplacement du marqueur
            marker.on('dragend', function() {
                const latLng = marker.getLatLng();
                updateHiddenFields(latLng.lat, latLng.lng);
                fetchAddress(latLng.lat, latLng.lng);
            });

            // Nouvelle fonctionnalité : Géolocalisation
            $('#getLocation').on('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            // Succès : mise à jour de la carte et des champs
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            updateMap(lat, lng);
                            updateHiddenFields(lat, lng);
                            //   alert("Votre position actuelle a été récupérée avec succès !");
                        },
                        function(error) {
                            // Gestion des erreurs
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    alert(
                                        "Permission refusée. Activez les autorisations de localisation dans les paramètres du navigateur."
                                    );
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    alert(
                                        "Position indisponible. Vérifiez votre connexion ou réessayez plus tard."
                                    );
                                    break;
                                case error.TIMEOUT:
                                    alert("Le temps pour obtenir votre position a expiré. Réessayez.");
                                    break;
                                default:
                                    alert(
                                        "Une erreur inconnue est survenue. Vérifiez les paramètres de votre appareil."
                                    );
                                    break;
                            }
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                } else {
                    alert("La géolocalisation n'est pas supportée par votre navigateur.");
                }
            });
        });
    </script>
</x-app-layout>
