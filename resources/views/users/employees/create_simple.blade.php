<x-app-layout>
    @php
        $menusAutorises =
            optional(auth()->user()->company)
                ->menus?->pluck('name')
                ->toArray() ?? [];
    @endphp
    @php
        $userTypesTitle = [
            'manager' => __('Add Manager'),
            'controller' => __('Add Controller'),
            'user' => __('Add Employee'),
            'customer' => __('Add Customer'),
        ];
    @endphp

    <div class="md:w-9/12 2xl:w-8/12 mx-auto">
        <div class="px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                {{ $userTypesTitle[$user_type] ?? '' }}
            </h1>
        </div>

        <!-- Messages d'alerte... -->

        <div class="my-10 px-6 py-6">
            <form action="{{ route('users.store.simple') }}" method="POST" enctype="multipart/form-data" id="userForm">
                @csrf
                <input type="hidden" name="user_type" value="{{ $user_type }}" required>
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>
                <input type="hidden" name="biometric_data" id="biometric_data">
                <input type="hidden" name="type_horaire" value="rotation">

                <!-- Section Informations de base -->
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="name" :value="__('Name *')"/>
                        <x-text-input-2 id="name" name="name" type="text" placeholder="{{ __('Name') }}"
                            :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="first_name" :value="__('First names *')" />
                        <x-text-input-2 id="first_name" name="first_name" type="text" placeholder="{{ __('First names') }}"
                            :value="old('first_name')" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="n_social" :value="__('Social Number (SS) *')" />
                        <x-text-input-2 id="n_social" name="n_social" type="text"
                            placeholder="{{ __('Social Number') }}" :value="old('n_social')" required />
                        <x-input-error :messages="$errors->get('n_social')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('Phone *')" />
                        <x-text-input-2 id="phone" name="phone" type="text"
                            placeholder="{{ __('Phone') }}" :value="old('phone')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="email" :value="__('Email *')" />
                        <x-text-input-2 id="email" name="email" type="email"
                            placeholder="{{ __('alex@gmail.com') }}" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="username" :value="__('Nom d’utilisateur *')" />
                        <x-text-input-2 id="username" name="username" type="text"
                            placeholder="{{ __('Nom d’utilisateur') }}" :value="old('username')" required />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="password" :value="__('Password *')" />
                        <x-text-input-2 id="password" name="password" type="password"
                            placeholder="{{ __('Password') }}" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmation de mot de Passe *')" />
                        <x-text-input-2 id="password_confirmation" name="password_confirmation" type="password"
                            placeholder="{{ __('Password') }}" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="address" :value="__('Adresse *')" />
                        <x-text-input-2 id="address" name="address" type="text"
                            placeholder="{{ __('Adresse') }}" :value="old('address')" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="salary_rate" :value="__('Taux salarial')" />
                        <x-text-input-2 id="salary_rate" name="salary_rate" type="number"
                            placeholder="{{ __('Taux salarial') }}" :value="old('salary_rate')" />
                        <x-input-error :messages="$errors->get('salary_rate')" class="mt-2" />
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="status" :value="__('Status *')" />
                        <x-select-input name="status" :options="['active' => 'Active', 'inactive' => 'Inactive']"
                            :selected="old('status', $user->status ?? 'active')" required />
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="panier" :value="__('Panier')" />
                        <x-text-input-2 id="panier" name="panier" type="number"
                            placeholder="{{ __('Panier') }}" :value="old('panier')" />
                        @error('panier')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="avatar" :value="__('Photo *')" />
                        <x-file-input name="avatar" :value="old('avatar')" accept="image/*" required />
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>
                </div>

                <hr class="border my-10 border-primary-color">

                <x-input-label class="font-bold my-5" :value="__('Caractéristiques')" />
                <div class="md:grid grid-cols-2 gap-16 my-7">
                    <div>
                        <x-input-label for="employee_function" :value="__('Fonction Employé *')" />
                        <x-select-input name="employee_function" :options="$fonction->pluck('nom_fonction', 'id')"
                            :selected="old('employee_function')" required />
                        @error('employee_function')
                            <div class="text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="contract_start_date" :value="__('Date Debut de Contrat')" />
                        <x-text-input-2 id="contract_start_date" name="contract_start_date" type="date"
                            :value="old('contract_start_date')" />
                        @error('contract_start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:grid grid-cols-2 gap-16 my-7">
                    <div>
                        <x-input-label for="qualification" :value="__('Qualification')" />
                        <x-text-input-2 id="qualification" name="qualification" type="text"
                            :value="old('qualification')" />
                        <x-input-error :messages="$errors->get('qualification')" class="mt-2" />
                    </div>
                </div>

                <!-- Section Biométrie -->
                <hr class="border my-10 border-primary-color">
                <h4 class="text-lg font-bold mb-4">Système biométrique</h4>

                <!-- État de la machine -->
                <div id="biometricStatus" class="text-sm font-medium mb-4 flex items-center space-x-2 text-gray-600">
                    <svg class="animate-spin h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span id="biometricStatusText">Vérification de l’appareil biométrique...</span>
                </div>

                <!-- Méthode -->
                {{-- @php
                    $options = ['' => '-- Sélectionnez --'];

                    if (in_array('Pointage via empreinte', $menusAutorises)) {
                        $options['fingerprint'] = 'Empreinte digitale';
                    }

                    if (in_array('Pointage via reconnaissance Faciale', $menusAutorises)) {
                        $options['facial_recognition'] = 'Reconnaissance faciale';
                    }
                @endphp --}}

                {{-- <div class="mb-4">
                    <label for="biometric_system" class="block font-medium mb-2">Méthode d'identification *</label>
                    <x-select-input name="biometric_system" id="biometric_system" :options="$options" required />
                </div> --}}


                {{-- <!-- Scan Empreinte -->
                @if(in_array('Pointage via empreinte', $menusAutorises))
                    <div id="fingerprint_scan" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                        <button type="button" id="scanFingerprintBtn" disabled class="bg-blue-600 text-white px-4 py-2 rounded opacity-50 cursor-not-allowed">
                            Scanner l'empreinte
                        </button>
                        <p class="text-sm text-gray-500 mt-2">Placez votre doigt sur le scanner</p>
                    </div>
                @endif --}}
            <!-- Scan Visage -->
                {{-- @if(in_array('Pointage via reconnaissance Faciale', $menusAutorises))
                    <div id="facial_scan" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex flex-col items-center">
                            <canvas id="faceCanvas" class="hidden"></canvas>
                            <button type="button" id="startCameraBtn" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Activer la caméra
                            </button>
                            <button type="button" id="captureFaceBtn" class="bg-green-600 text-white px-4 py-2 rounded mt-2 hidden">
                                Capturer
                            </button>
                        </div>
                    </div>
                @endif --}}


                    {{-- <div id="preview_section" class="hidden mt-6 p-4 border rounded-lg">
                        <h5 class="font-bold mb-2">Aperçu</h5>
                        <div id="biometric_preview" class="flex justify-center items-center min-h-40 bg-gray-100 rounded">
                            <p class="text-gray-500">Aucun aperçu disponible</p>
                        </div>
                        <div class="mt-4 flex justify-between">
                            <button type="button" id="retryBtn" class="bg-yellow-500 text-white px-4 py-2 rounded">
                                Réessayer
                            </button>
                            <button type="button" id="confirmBtn" class="bg-green-600 text-white px-4 py-2 rounded">
                                Confirmer l'enregistrement
                            </button>
                        </div>
                    </div> --}}

                    {{-- @if(in_array('Pointage via empreinte', $menusAutorises))
                        <div class="mt-4">
                            <button type="button" id="checkResultBtn" class="bg-purple-600 text-white px-4 py-2 rounded">
                                Récupérer Résultat du Scan
                            </button>
                        </div>
                     @endif --}}

                    <input type="hidden" id="biometric_data" name="biometric_data">

                {{-- <div class="md:grid grid-cols-2 gap-16 my-7">
                    <div>
                        <label for="exception_fiche_fixe" class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="exception_fiche_fixe" id="exception_fiche_fixe" value="1"
                                {{ old('exception_fiche_fixe', $user->exception_fiche_fixe ?? false) ? 'checked' : '' }}
                                class="form-checkbox h-5 w-5 text-blue-600">
                            <span class="ml-2 text-gray-700">Autoriser fiche de paie fixe exceptionnelle</span>
                        </label>
                    </div>
                </div> --}}

                <!-- Boutons de soumission -->
                <div class="my-24 flex">
                    <x-secondary-button onclick="history.back()" class="flex mr-4 justify-center shadow-md">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button id="submitBtn" class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Record') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

{{-- <script>
document.addEventListener("DOMContentLoaded", function () {
    const biometricSystemSelect = document.getElementById("biometric_system");
    const fingerprintScan = document.getElementById("fingerprint_scan");
    const facialScan = document.getElementById("facial_scan");
    const badgeScan = document.getElementById("badge_scan");
    const biometricDataInput = document.getElementById("biometric_data");
    const previewSection = document.getElementById("preview_section");
    const biometricPreview = document.getElementById("biometric_preview");
    const retryBtn = document.getElementById("retryBtn");
    const confirmBtn = document.getElementById("confirmBtn");

    const video = document.getElementById("faceCamera");
    const canvas = document.getElementById("faceCanvas");
    const startCameraBtn = document.getElementById("startCameraBtn");
    const captureFaceBtn = document.getElementById("captureFaceBtn");

    let currentStream = null;

    function hideAllScanSections() {
        fingerprintScan.classList.add("hidden");
        facialScan.classList.add("hidden");
        badgeScan.classList.add("hidden");
        previewSection.classList.add("hidden");
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
    }

    biometricSystemSelect.addEventListener("change", function () {
        hideAllScanSections();

        const selected = this.value;

        if (selected === "fingerprint") {
            fingerprintScan.classList.remove("hidden");

        } else if (selected === "facial_recognition") {
            facialScan.classList.remove("hidden");

        } else if (selected === "badge") {
            badgeScan.classList.remove("hidden");

            // Générer un badge automatiquement
            const badgeCode = "BADGE-" + Math.random().toString(36).substring(2, 10).toUpperCase();
            biometricDataInput.value = badgeCode;

            biometricPreview.innerHTML = `<p class="text-green-700 font-semibold">Code : ${badgeCode}</p>`;
            previewSection.classList.remove("hidden");
        }
    });

    // Scan empreinte
    document.getElementById("scanFingerprintBtn").addEventListener("click", async function () {
        try {
            const response = await fetch("http://127.0.0.1:5000/scan/fingerprint");
            const result = await response.json();
            biometricDataInput.value = result.data;

            biometricPreview.innerHTML = `<p class="text-green-700 font-semibold">Empreinte capturée avec succès</p>`;
            previewSection.classList.remove("hidden");
        } catch (error) {
            alert("Erreur lors du scan de l'empreinte : " + error.message);
        }
    });

    // Démarrer la caméra
    startCameraBtn.addEventListener("click", async function () {
        try {
            currentStream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = currentStream;
            video.classList.remove("hidden");
            captureFaceBtn.classList.remove("hidden");
        } catch (error) {
            alert("Erreur d'accès à la caméra : " + error.message);
        }
    });

    // Capturer le visage
    captureFaceBtn.addEventListener("click", function () {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);
        const imageData = canvas.toDataURL("image/png");

        biometricDataInput.value = imageData;
        biometricPreview.innerHTML = `<img src="${imageData}" class="max-w-xs rounded shadow"/>`;
        previewSection.classList.remove("hidden");

        // Arrêter le flux caméra
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            video.classList.add("hidden");
            captureFaceBtn.classList.add("hidden");
        }
    });

    // Réessayer
    retryBtn.addEventListener("click", function () {
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
        previewSection.classList.add("hidden");
    });

    // Confirmer
    confirmBtn.addEventListener("click", function () {
        if (!biometricDataInput.value) {
            alert("Aucune donnée biométrique détectée.");
            return;
        }
        alert("Biométrie enregistrée ! Vous pouvez soumettre le formulaire.");
    });
});
</script> --}}

{{-- <script>
document.addEventListener("DOMContentLoaded", function () {
    const biometricSystemSelect = document.getElementById("biometric_system");
    const fingerprintScan = document.getElementById("fingerprint_scan");
    const facialScan = document.getElementById("facial_scan");
    const badgeScan = document.getElementById("badge_scan");
    const biometricDataInput = document.getElementById("biometric_data");
    const previewSection = document.getElementById("preview_section");
    const biometricPreview = document.getElementById("biometric_preview");
    const retryBtn = document.getElementById("retryBtn");
    const confirmBtn = document.getElementById("confirmBtn");

    const video = document.getElementById("faceCamera");
    const canvas = document.getElementById("faceCanvas");
    const startCameraBtn = document.getElementById("startCameraBtn");
    const captureFaceBtn = document.getElementById("captureFaceBtn");

    let currentStream = null;

    function hideAllScanSections() {
        fingerprintScan.classList.add("hidden");
        facialScan.classList.add("hidden");
        badgeScan.classList.add("hidden");
        previewSection.classList.add("hidden");
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
    }

    biometricSystemSelect.addEventListener("change", function () {
        hideAllScanSections();

        const selected = this.value;

        if (selected === "fingerprint") {
            fingerprintScan.classList.remove("hidden");
        } else if (selected === "facial_recognition") {
            facialScan.classList.remove("hidden");
        } else if (selected === "badge") {
            badgeScan.classList.remove("hidden");

            const badgeCode = "BADGE-" + Math.random().toString(36).substring(2, 10).toUpperCase();
            biometricDataInput.value = badgeCode;

            biometricPreview.innerHTML = `<p class="text-green-700 font-semibold">Code : ${badgeCode}</p>`;
            previewSection.classList.remove("hidden");
        }
    });

    // ✅ Scan empreinte digitale via Flask
    document.getElementById("scanFingerprintBtn").addEventListener("click", async function () {
        const userId = document.getElementById("username").value;

        if (!userId) {
            alert("Veuillez saisir un nom d'utilisateur.");
            return;
        }

        try {
            const response = await fetch("http://127.0.0.1:5000/api/scan", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    type: "fingerprint",  // ← bien adapté à ce que l'API attend
                    user_id: userId
                })
            });

            const result = await response.json();

            if (result.status === "success") {
                alert("Commande envoyée avec succès. Cliquez sur 'Récupérer Résultat du Scan'.");
            } else {
                alert("Erreur serveur : " + result.message);
            }
        } catch (error) {
            alert("Erreur de connexion à Flask : " + error.message);
        }
    });

    // ✅ Récupérer résultat du scan
    document.getElementById("checkResultBtn").addEventListener("click", async function () {
        const userId = document.getElementById("username").value;

        if (!userId) {
            alert("Saisissez un nom d'utilisateur.");
            return;
        }

        try {
            const response = await fetch(`http://127.0.0.1:5000/api/scan-result/${userId}`);
            const result = await response.json();

            if (result.status === "success") {
                biometricDataInput.value = result.data;
                biometricPreview.innerHTML = `<p class="text-green-700 font-semibold">Résultat reçu : ${result.data}</p>`;
                previewSection.classList.remove("hidden");
            } else if (result.status === "pending") {
                alert("Scan en attente. Réessayez bientôt.");
            } else {
                alert("Erreur : " + result.message);
            }

        } catch (error) {
            alert("Erreur récupération : " + error.message);
        }
    });

    // 🎥 Caméra (facial scan local)
    startCameraBtn.addEventListener("click", async function () {
        try {
            currentStream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = currentStream;
            video.classList.remove("hidden");
            captureFaceBtn.classList.remove("hidden");
        } catch (error) {
            alert("Erreur d'accès à la caméra : " + error.message);
        }
    });

    captureFaceBtn.addEventListener("click", function () {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);
        const imageData = canvas.toDataURL("image/png");

        biometricDataInput.value = imageData;
        biometricPreview.innerHTML = `<img src="${imageData}" class="max-w-xs rounded shadow"/>`;
        previewSection.classList.remove("hidden");

        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            video.classList.add("hidden");
            captureFaceBtn.classList.add("hidden");
        }
    });

    retryBtn.addEventListener("click", function () {
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
        previewSection.classList.add("hidden");
    });

    confirmBtn.addEventListener("click", function () {
        if (!biometricDataInput.value) {
            alert("Aucune donnée biométrique détectée.");
            return;
        }
        alert("Biométrie enregistrée. Vous pouvez enregistrer le formulaire.");
    });
});
</script> --}}

{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
    const biometricSystemSelect = document.getElementById("biometric_system");
    const fingerprintScan = document.getElementById("fingerprint_scan");
    const facialScan = document.getElementById("facial_scan");
    const biometricDataInput = document.getElementById("biometric_data");
    const previewSection = document.getElementById("preview_section");
    const biometricPreview = document.getElementById("biometric_preview");
    const retryBtn = document.getElementById("retryBtn");
    const confirmBtn = document.getElementById("confirmBtn");

    const video = document.getElementById("faceCamera");
    const canvas = document.getElementById("faceCanvas");
    const startCameraBtn = document.getElementById("startCameraBtn");
    const captureFaceBtn = document.getElementById("captureFaceBtn");

    let currentStream = null;

    function hideAllScanSections() {
        fingerprintScan.classList.add("hidden");
        facialScan.classList.add("hidden");
        previewSection.classList.add("hidden");
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
    }

    biometricSystemSelect.addEventListener("change", function () {
        hideAllScanSections();
        const selected = this.value;

        if (selected === "fingerprint") {
            fingerprintScan.classList.remove("hidden");
        } else if (selected === "facial_recognition") {
            facialScan.classList.remove("hidden");
        }
    });

    document.getElementById("scanFingerprintBtn").addEventListener("click", async function () {
        const userId = document.getElementById("username").value;
        const biometricType = biometricSystemSelect.value === "fingerprint" ? "fingerprint" : "face";

        if (!userId) return alert("Veuillez saisir un identifiant.");

        const res = await fetch("http://127.0.0.1:5000/api/scan", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type: biometricType, user_id: userId })
        });

        const result = await res.json();
        if (result.status === "success") {
            alert("Commande envoyée. Cliquez sur 'Récupérer Résultat du Scan'.");
        } else {
            alert("Erreur : " + result.message);
        }
    });

    document.getElementById("checkResultBtn").addEventListener("click", async function () {
        const userId = document.getElementById("username").value;
        const res = await fetch(`http://127.0.0.1:5000/api/scan-result/${userId}`);
        const result = await res.json();

        if (result.status === "success") {
            biometricDataInput.value = result.data;
            biometricPreview.innerHTML = `<p>Résultat : ${result.data}</p>`;
            previewSection.classList.remove("hidden");
        } else if (result.status === "pending") {
            alert("Scan en cours. Réessayez dans quelques secondes.");
        } else {
            alert("Erreur : " + result.message);
        }
    });

    startCameraBtn.addEventListener("click", async function () {
        try {
            currentStream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = currentStream;
            video.classList.remove("hidden");
            captureFaceBtn.classList.remove("hidden");
        } catch (error) {
            alert("Erreur d'accès à la caméra : " + error.message);
        }
    });

    captureFaceBtn.addEventListener("click", function () {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);
        const imageData = canvas.toDataURL("image/png");

        biometricDataInput.value = imageData;
        biometricPreview.innerHTML = `<img src="${imageData}" class="max-w-xs rounded shadow"/>`;
        previewSection.classList.remove("hidden");

        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            video.classList.add("hidden");
            captureFaceBtn.classList.add("hidden");
        }
    });

    retryBtn.addEventListener("click", function () {
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
        previewSection.classList.add("hidden");
    });

    confirmBtn.addEventListener("click", function () {
        if (!biometricDataInput.value) {
            alert("Aucune donnée biométrique détectée.");
            return;
        }
        alert("Biométrie enregistrée. Vous pouvez enregistrer le formulaire.");
    });
});
</script> --}}

<script>
document.addEventListener("DOMContentLoaded", function () {
    const biometricSystemSelect = document.getElementById("biometric_system");
    const fingerprintScan = document.getElementById("fingerprint_scan");
    const facialScan = document.getElementById("facial_scan");
    const biometricDataInput = document.getElementById("biometric_data");
    const previewSection = document.getElementById("preview_section");
    const biometricPreview = document.getElementById("biometric_preview");
    const retryBtn = document.getElementById("retryBtn");
    const confirmBtn = document.getElementById("confirmBtn");
    const biometricStatusText = document.getElementById("biometricStatusText");
    const scanBtn = document.getElementById("scanFingerprintBtn");

    const video = document.getElementById("faceCamera");
    const canvas = document.getElementById("faceCanvas");
    const startCameraBtn = document.getElementById("startCameraBtn");
    const captureFaceBtn = document.getElementById("captureFaceBtn");
    let currentStream = null;

    function hideAllScanSections() {
        fingerprintScan.classList.add("hidden");
        facialScan.classList.add("hidden");
        previewSection.classList.add("hidden");
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
    }

    biometricSystemSelect.addEventListener("change", function () {
        hideAllScanSections();
        const selected = this.value;
        if (selected === "fingerprint") fingerprintScan.classList.remove("hidden");
        else if (selected === "facial_recognition") facialScan.classList.remove("hidden");
    });

    scanBtn.addEventListener("click", async function () {
        const userId = document.getElementById("username").value;
        const biometricType = biometricSystemSelect.value === "fingerprint" ? "fingerprint" : "face";
        if (!userId) return alert("Veuillez saisir un identifiant.");

        const res = await fetch("http://127.0.0.1:5000/api/scan", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ type: biometricType, user_id: userId })
        });

        const result = await res.json();
        if (result.status === "success") {
            alert("✅ Commande envoyée. Cliquez sur 'Récupérer Résultat du Scan'.");
        } else {
            alert("❌ Erreur : " + result.message);
        }
    });

    document.getElementById("checkResultBtn").addEventListener("click", async function () {
        const userId = document.getElementById("username").value;
        const res = await fetch(`http://127.0.0.1:5000/api/scan-result/${userId}`);
        const result = await res.json();

        if (result.status === "success") {
            biometricDataInput.value = result.data;
            biometricPreview.innerHTML = `<p class="text-green-700 font-semibold">Résultat reçu : ${result.data}</p>`;
            previewSection.classList.remove("hidden");
        } else if (result.status === "pending") {
            alert("Scan en cours. Réessayez dans quelques secondes.");
        } else {
            alert("Erreur : " + result.message);
        }
    });

    startCameraBtn.addEventListener("click", async function () {
        try {
            currentStream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = currentStream;
            video.classList.remove("hidden");
            captureFaceBtn.classList.remove("hidden");
        } catch (error) {
            alert("Erreur d'accès à la caméra : " + error.message);
        }
    });

    captureFaceBtn.addEventListener("click", function () {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0);
        const imageData = canvas.toDataURL("image/png");

        biometricDataInput.value = imageData;
        biometricPreview.innerHTML = `<img src="${imageData}" class="max-w-xs rounded shadow"/>`;
        previewSection.classList.remove("hidden");

        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            video.classList.add("hidden");
            captureFaceBtn.classList.add("hidden");
        }
    });

    retryBtn.addEventListener("click", function () {
        biometricDataInput.value = "";
        biometricPreview.innerHTML = '<p class="text-gray-500">Aucun aperçu disponible</p>';
        previewSection.classList.add("hidden");
    });

    confirmBtn.addEventListener("click", function () {
        if (!biometricDataInput.value) {
            alert("Aucune donnée biométrique détectée.");
            return;
        }
        alert("✅ Donnée biométrique enregistrée. Vous pouvez sauvegarder.");
    });

    // 🔁 Vérifie la connexion de la machine toutes les 3 secondes
    setInterval(async () => {
        try {
            const res = await fetch("http://127.0.0.1:5000/api/status");
            const data = await res.json();
            if (Array.isArray(data) && data.length > 0) {
                biometricStatusText.innerHTML = `✅ Appareil connecté (${data[0]})`;
                scanBtn.disabled = false;
                scanBtn.classList.remove("opacity-50", "cursor-not-allowed");
            } else {
                biometricStatusText.innerHTML = `❌ Aucun appareil biométrique connecté`;
                scanBtn.disabled = true;
                scanBtn.classList.add("opacity-50", "cursor-not-allowed");
            }
        } catch (err) {
            biometricStatusText.innerHTML = `❌ Erreur de connexion à Flask`;
            scanBtn.disabled = true;
            scanBtn.classList.add("opacity-50", "cursor-not-allowed");
        }
    }, 3000);
});
</script>










