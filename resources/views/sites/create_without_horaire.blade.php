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

    <!-- cards -->
    <div class="md:w-9/12  2xl:w-8/12 mx-auto">


        <div class="px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                {{ __('Ajouter un lieu de travail') }}
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
            <!-- card 1 -->

            <form method="POST" action="{{ route('sites.store') }}" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Company name -->
                    <div>
                        <x-input-label for="name" :value="__('Nom du site *')" />
                        <x-text-input-2 id="name" name="name" type="text"
                            placeholder="{{ __('Nom du site') }}" :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <!-- Company name -->
                    <div>
                        <x-input-label for="customer_id" :value="__('Client *')" />
                        <x-select-input name="customer_id" :options="$customers" :disabled="false"  />
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                    </div>
                </div>
                <div class="md:grid grid-cols-2 gap-16 mt-7">

                    <!-- Company email -->
                    <div>
                        <x-input-label for="controller_id" :value="__('Contrôleurs ')" />

                        <!-- Select -->
                        <label class="hidden" for="hs-tags-input">Tags label</label>
                        <select multiple="", name="controller_id[]"
                            data-hs-select='{
                                                        "placeholder": "Sélectionnez les controlleurs...",
                                                        "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-secondary dark:border-primary-color",
                                                        "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 dark:bg-secondary dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                                        "mode": "tags",
                                                        "wrapperClasses": "relative ps-0.5 pe-9 min-h-[46px] flex items-center flex-wrap text-nowrap w-full border-t-0 border-r-0 border-l-0 border-b-2 border-primary-color rounded-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-secondary dark:border-primary-color dark:text-neutral-400",
                                                        "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center relative z-10 bg-white border border-gray-200 rounded-full p-1 m-1 dark:bg-secondary dark:border-primary-color \"><div class=\"size-6 me-1\" data-icon></div><div class=\"whitespace-nowrap text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"inline-flex shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm dark:bg-neutral-700/50 dark:hover:bg-neutral-700 dark:text-neutral-400 cursor-pointer\" data-remove><svg class=\"shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
                                                        "tagsInputId": "hs-tags-input",
                                                        "tagsInputClasses": "py-3 px-2 rounded-lg order-1 text-sm outline-none dark:bg-secondary dark:placeholder-neutral-500 text-neutral-400",
                                                        "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"text-xs text-gray-500 dark:text-neutral-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                                                        "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500  \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                                        }'
                            class="hidden">
                            <option value="">Choose</option>

                            @foreach ($controllers as $controller)
                                <option value="{{ $controller->id }}"
                                    data-hs-select-option='{
                                                                "description": "{{ $controller->first_name }}",
                                                                "icon": "<img class=\"inline-block rounded-full\" src=\"{{ asset('storage/' . $controller->avatar) }}\" />"
                                                            }'>
                                    {{ $controller->name }}</option>
                            @endforeach
                        </select>
                        <!-- End Select -->

                        <x-input-error :messages="$errors->get('controller_id')" class="mt-2" />
                    </div>

                    <!-- Website -->
                    <div>
                        <x-input-label for="user_id" :value="__('Employés admis sur le site ')" />

                        <!-- Select -->
                        <label class="hidden" for="hs-tags-input">Tags label</label>
                        <select multiple="", name="user_id[]"
                            data-hs-select='{
                                "placeholder": "Sélectionnez les employés...",
                                "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-secondary dark:border-primary-color",
                                "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 dark:bg-secondary dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                "mode": "tags",
                                "wrapperClasses": "relative ps-0.5 pe-9 min-h-[46px] flex items-center flex-wrap text-nowrap w-full border-t-0 border-r-0 border-l-0 border-b-2 border-primary-color rounded-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-secondary dark:border-primary-color dark:text-neutral-400",
                                "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center relative z-10 bg-white border border-gray-200 rounded-full p-1 m-1 dark:bg-secondary dark:border-primary-color \"><div class=\"size-6 me-1\" data-icon></div><div class=\"whitespace-nowrap text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"inline-flex shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm dark:bg-neutral-700/50 dark:hover:bg-neutral-700 dark:text-neutral-400 cursor-pointer\" data-remove><svg class=\"shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
                                "tagsInputId": "hs-tags-input",
                                "tagsInputClasses": "py-3 px-2 rounded-lg order-1 text-sm outline-none dark:bg-secondary dark:placeholder-neutral-500 text-neutral-400",
                                "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"text-xs text-gray-500 dark:text-neutral-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                                "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500  \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                            class="hidden">
                            <option value="">Choose</option>

                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    data-hs-select-option='{
                                        "description": "{{ $user->first_name }}",
                                        "icon": "<img class=\"inline-block rounded-full\" src=\"{{ asset('storage/' . $user->avatar) }}\" />"
                                    }'>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                         <!-- Affichage des erreurs pour user_id -->
                         {{-- @error('user_id')
                            <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                        @enderror --}}
                    </div>
                </div>
                <div class="md:grid grid-cols-2 gap-16 mt-7">

                    <!-- Website -->
                    <div>
                        <x-input-label for="status" :value="__('Status *')" />
                        <x-select-input name="status" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="old('status', $user->status ?? 'active')" :disabled="false"
                            required />

                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="panier" :value="__('Panier')" />
                        <x-text-input-2 id="panier" name="panier" type="number"
                            placeholder="{{ __('panier') }}" :value="old('panier')" />
                        <x-input-error :messages="$errors->get('panier')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Company name -->
                    <div><x-input-label for="description" :value="__('Description')" />
                        <textarea name="description" id="description" rows="5" placeholder="Écrivez ici..." :value="old('description')"
                            class="block w-full bg-secondary border-t-0 border-r-0 border-l-0 border-b-2 text-black border-primary-color pt-3 mt-3 focus:ring-0 focus:border-primary-color rounded-md pl-4"></textarea>
                    </div>

                    <div>
                        <x-input-label for="timezone" :value="__('Fuseau Horaire ')" />
                        <x-select-input name="timezone" :options="$timezones" :disabled="false"  />
                        <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                    </div>
                </div>

                <hr class="border mt-10  border-primary-color border-solid opacity-100">

                <x-input-label class="font-bold  my-5" :value="__('Horaire de travail')" />

                {{-- <div class="grid grid-cols-1 gap-6 mt-7">
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="md:flex items-center gap-16">
                        <div class="flex items-center">
                            <input type="checkbox" class="shrink-0 mr-5 h-4 w-4 border-gray-200 rounded text-primary focus:ring-green-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-green-500 dark:checked:border-green-500 dark:focus:ring-offset-gray-800"
                                id="checkbox-{{ strtolower($day) }}" name="{{ strtolower($day) }}_checked" value="1">
                            <p class="font-semibold w-24 text-black">{{ __($day) }}</p>
                        </div>
                        <div class="w-full">
                            <x-input-label for="start_time_{{ strtolower($day) }}" :value="__('Heure de début')" />
                            <x-text-input-2 id="start_time_{{ strtolower($day) }}" name="start_time_{{ strtolower($day) }}" type="time"
                                placeholder="{{ __('Heure de début') }}" disabled />
                                <x-input-error :messages="$errors->get('start_time_' . strtolower($day))" class="mt-2" />
                        </div>
                        <div class="w-full">
                            <x-input-label for="end_time_{{ strtolower($day) }}" :value="__('Heure de fin')" />
                            <x-text-input-2 id="end_time_{{ strtolower($day) }}" name="end_time_{{ strtolower($day) }}" type="time"
                                placeholder="{{ __('Heure de fin') }}" disabled />
                                <x-input-error :messages="$errors->get('end_time_' . strtolower($day))" class="mt-2" />
                        </div>
                    </div>
                    @endforeach
                </div> --}}

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Phone -->
                    <div>
                        <x-input-label for="late_mark_duration" :value="__('Marquage tardif après (minutes)')" />
                        <x-text-input-2 id="late_mark_duration" name="late_mark_duration" type="text"
                            placeholder="{{ __('Heure de fin') }}" :value="old('late_mark_duration')" />
                        <x-input-error :messages="$errors->get('late_mark_duration')" class="mt-2" />
                    </div>


                </div>
                <hr class="border mt-10  border-primary-color border-solid opacity-100">


                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <x-input-label class="font-bold  my-5" :value="__('Localisation')" />

                    <button type="button" id="getLocation" class="font-semibold text-primary underline"><i
                            class="fa-solid fa-location-dot mr-2"></i>Cliquer pour utiliser votre position
                        actuelle</button>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Company address -->
                    <div>
                        <x-input-label for="address" :value="__('Adresse *')" />
                        <x-text-input-2 id="address" name="address" type="text"
                            placeholder="{{ __('Adresse') }}" :value="old('address')" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- Default time zone -->

                    <div>
                        <x-input-label for="radius" :value="__('Rayon (en mètre)*')" />
                        <x-text-input-2 id="radius" name="radius" type="number"
                            placeholder="{{ __('Rayon') }}" :value="old('radius')" required />
                        <x-input-error :messages="$errors->get('radius')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <!-- Default time zone -->

                    <div>
                        <x-input-label for="latitude" :value="__('Latitude *')" />
                        <x-text-input-2 id="latitude" name="latitude" type="text"
                            placeholder="{{ __('Latitude') }}" :value="old('latitude')" required />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>
                    <!-- Company address -->
                    <div>
                        <x-input-label for="longitude" :value="__('Longitude *')" />
                        <x-text-input-2 id="longitude" name="longitude" type="text"
                            placeholder="{{ __('Longitude') }}" :value="old('longitude')" required />
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
                        {{ __('Record') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

    </div>
    <!-- end cards -->

    <!-- Inclure Select2 et jQuery -->
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

<script>
    $(document).ready(function() {
        $('input[type="checkbox"]').change(function() {
            var day = $(this).attr('id').split('-')[1];
            if (this.checked) {
                $('#start_time_' + day).prop('disabled', false).val('');
                $('#end_time_' + day).prop('disabled', false).val('');
            } else {
                $('#start_time_' + day).prop('disabled', true).val('');
                $('#end_time_' + day).prop('disabled', true).val('');
            }
        });
    });
    </script>

</x-app-layout>
