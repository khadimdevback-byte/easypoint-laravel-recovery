<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-qrcode"></i>
                <span>{{ __('Borne de Pointage') }}</span>
            </div>
            <div class="action-date">
                <i class="fas fa-clock"></i>
                <span id="live-clock-header">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- En-tête --}}
            <div class="borne-header">
                <div class="header-content">
                    <h1 class="header-title">Borne de Pointage Automatique</h1>
                    <p class="header-subtitle" id="selected-site-name">
                        @if($selectedSite)
                            Site : {{ $selectedSite->name }}
                        @else
                            Sélectionnez un site ci-dessous
                        @endif
                    </p>
                    <p class="header-desc">Passez votre badge devant le lecteur de QR Code (douchette) pour pointer.</p>
                </div>
                <div class="header-icon">
                    <i class="fas fa-fingerprint"></i>
                </div>
            </div>

            {{-- Sélecteur de site --}}
            @if($sites->count() > 1 || !$selectedSite)
            <div class="site-selector">
                <label class="site-selector-label">
                    <i class="fas fa-map-marker-alt"></i>
                    Sélectionnez votre lieu de travail :
                </label>
                <select id="site-select" class="site-select">
                    <option value="">-- Choisir un site --</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ $selectedSite && $selectedSite->id == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                    @endforeach
                </select>
                <button id="confirm-site-btn" class="site-confirm-btn">
                    <i class="fas fa-check"></i> Valider
                </button>
            </div>
            @else
            <input type="hidden" id="site-select" value="{{ $sites->first()->id }}">
            @endif

            {{-- Carte de statut --}}
            <div id="status-card" class="status-card status-card-waiting">
                <div id="status-icon" class="status-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h2 id="status-title" class="status-title">En attente de scan...</h2>
                <p id="status-message" class="status-message">Le système est prêt à lire un badge.</p>
            </div>

            {{-- Horloge --}}
            <div class="borne-clock">
                <i class="fas fa-clock"></i>
                <span id="live-clock">--:--:--</span>
            </div>

            {{-- Input invisible pour le scanner --}}
            <input type="text" id="scanner-input"
                   class="scanner-input"
                   autocomplete="off"
                   autofocus>

            {{-- Mode dépannage --}}
            <div class="manual-mode">
                <div class="manual-header">
                    <i class="fas fa-tools text-yellow-600"></i>
                    <span class="manual-title">Mode dépannage</span>
                </div>
                <p class="manual-desc">Si la douchette ne fonctionne pas, saisissez votre matricule :</p>
                <div class="manual-input-group">
                    <input type="text" id="manual-matricule"
                           class="manual-input"
                           placeholder="Ex: MPT02 96 5114 81 893"
                           autocomplete="off">
                    <button id="manual-btn" class="manual-btn">
                        <i class="fas fa-check"></i> Pointer manuellement
                    </button>
                </div>
                <p class="manual-hint">Le matricule est indiqué sur votre badge</p>
            </div>
        </div>
    </div>

    <style>
        /* Vos styles existants... */
        :root {
            --vp: #2e7d64;
            --vc: #e8f5f1;
            --vf: #1a5a48;
            --va: #40a088;
            --gf: #2d3a3e;
            --gm: #5a6b6f;
            --gl: #d4dfe2;
            --gtc: #eef3f5;
            --t: all 0.32s cubic-bezier(.4,0,.2,1);
        }

        .action-bar {
            position: fixed;
            top: var(--navbar-height, 60px);
            left: var(--sidebar-width, 260px);
            right: 0;
            z-index: 900;
            background: #2e7d64;
            border-bottom: 2px solid rgba(0,0,0,0.1);
            transition: left 0.32s cubic-bezier(.4,0,.2,1);
        }

        .action-bar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 46px;
        }

        .action-info { display: inline-flex; align-items: center; gap: 10px; color: white; font-size: 0.85rem; font-weight: 500; }
        .action-date { background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; color: white; }

        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 32px;
            max-width: 700px;
            margin: 0 auto;
        }

        .borne-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
            border-radius: 20px;
            padding: 24px 28px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .header-content { position: relative; z-index: 2; }
        .header-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            margin: 0 0 6px 0;
        }
        .header-subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
            margin: 0;
        }
        .header-desc {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.6);
            margin-top: 8px;
        }
        .header-icon {
            position: relative;
            z-index: 1;
            font-size: 3.5rem;
            opacity: 0.15;
            color: white;
        }
        .header-icon i { font-size: 3.5rem; }

        /* Sélecteur de site */
        .site-selector {
            background: var(--gtc);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .site-selector-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gf);
        }
        .site-selector-label i {
            color: var(--vp);
        }
        .site-select {
            padding: 10px 16px;
            border: 1.5px solid var(--gl);
            border-radius: 12px;
            font-size: 0.85rem;
            background: white;
            min-width: 250px;
            cursor: pointer;
            transition: var(--t);
        }
        .site-select:focus {
            outline: none;
            border-color: var(--vp);
        }
        .site-confirm-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--vp);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
        }
        .site-confirm-btn:hover {
            background: var(--vf);
            transform: translateY(-2px);
        }

        .status-card {
            margin: 24px auto;
            padding: 32px;
            border-radius: 24px;
            text-align: center;
            transition: var(--t);
        }
        .status-card-waiting {
            background: var(--gtc);
            border: 2px dashed var(--gl);
        }
        .status-card-success {
            background: #dcfce7;
            border: 2px solid #10b981;
        }
        .status-card-warning {
            background: #fef3c7;
            border: 2px solid #f59e0b;
        }
        .status-card-error {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }
        .status-card-processing {
            background: #e0f2fe;
            border: 2px solid #3b82f6;
        }

        .status-icon {
            margin-bottom: 16px;
        }
        .status-icon i {
            font-size: 3rem;
        }
        .status-card-waiting .status-icon i { color: #9ca3af; animation: pulse 2s infinite; }
        .status-card-success .status-icon i { color: #10b981; }
        .status-card-warning .status-icon i { color: #f59e0b; }
        .status-card-error .status-icon i { color: #ef4444; }
        .status-card-processing .status-icon i { color: #3b82f6; animation: spin 1s linear infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.1); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .status-card-waiting .status-title { color: var(--gf); }
        .status-card-success .status-title { color: #166534; }
        .status-card-warning .status-title { color: #92400e; }
        .status-card-error .status-title { color: #991b1b; }
        .status-card-processing .status-title { color: #1e40af; }

        .status-message {
            font-size: 0.85rem;
            color: var(--gm);
        }

        .borne-clock {
            text-align: center;
            padding: 12px;
            background: var(--gtc);
            border-radius: 40px;
            display: inline-block;
            width: auto;
            margin: 0 auto 24px;
            font-size: 1.2rem;
            font-weight: 700;
            font-family: monospace;
            color: var(--vp);
        }
        .borne-clock i {
            margin-right: 8px;
            color: var(--vp);
        }

        .scanner-input {
            position: fixed;
            top: 0;
            left: 0;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .manual-mode {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--gl);
            text-align: center;
        }
        .manual-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .manual-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #d97706;
        }
        .manual-desc {
            font-size: 0.75rem;
            color: var(--gm);
            margin-bottom: 16px;
        }
        .manual-input-group {
            display: flex;
            flex-direction: row;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .manual-input {
            padding: 10px 16px;
            border: 1.5px solid var(--gl);
            border-radius: 40px;
            font-size: 0.85rem;
            width: 280px;
            font-family: monospace;
            transition: var(--t);
        }
        .manual-input:focus {
            outline: none;
            border-color: var(--vp);
        }
        .manual-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d97706;
            color: white;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
        }
        .manual-btn:hover {
            background: #b45309;
            transform: translateY(-2px);
        }
        .manual-hint {
            font-size: 0.65rem;
            color: var(--gm);
            margin-top: 12px;
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 20px; }
            .borne-header { padding: 20px; }
            .header-icon { display: none; }
            .site-selector { flex-direction: column; align-items: stretch; }
            .site-select { width: 100%; }
            .site-confirm-btn { justify-content: center; }
            .manual-input-group { flex-direction: column; align-items: center; }
            .manual-input { width: 100%; }
            .manual-btn { width: 100%; justify-content: center; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input           = document.getElementById('scanner-input');
            const card            = document.getElementById('status-card');
            const iconContainer   = document.getElementById('status-icon');
            const title           = document.getElementById('status-title');
            const msg             = document.getElementById('status-message');
            const clock           = document.getElementById('live-clock');
            const clockHeader     = document.getElementById('live-clock-header');
            const manualInput     = document.getElementById('manual-matricule');
            const siteSelect      = document.getElementById('site-select');
            const selectedSiteName= document.getElementById('selected-site-name');
            const confirmSiteBtn  = document.getElementById('confirm-site-btn');

            let currentSiteId = {{ $selectedSite ? $selectedSite->id : 'null' }};
            let resetTimeout  = null;
            let scanBuffer    = '';
            let scanTimeout   = null;

            // ── Horloge ────────────────────────────────────────────────
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('fr-FR');
                if (clock) clock.textContent = timeString;
                if (clockHeader) clockHeader.textContent = timeString;
            }
            updateClock();
            setInterval(updateClock, 1000);

            // ── Focus sur l'input scanner ──────────────────────────────
            function setFocus() {
                if (!input || !manualInput) return;
                if (document.activeElement === manualInput) return;
                if (siteSelect && document.activeElement === siteSelect) return;
                input.focus();
            }
            setFocus();
            document.addEventListener('click', setFocus);
            document.addEventListener('keydown', setFocus);

            // ── Affichage ──────────────────────────────────────────────
            function showSuccess(message, type = 'arrivee') {
                if (resetTimeout) clearTimeout(resetTimeout);
                card.className = "status-card status-card-success";
                iconContainer.innerHTML = '<i class="fas fa-check-circle"></i>';
                title.textContent = type === 'arrivee' ? "✅ ARRIVÉE VALIDÉE !" : "✅ DÉPART VALIDÉ !";
                msg.textContent = message;
                resetTimeout = setTimeout(resetBorne, 4000);
            }

            function showWarning(message, type = 'arrivee') {
                if (resetTimeout) clearTimeout(resetTimeout);
                card.className = "status-card status-card-warning";
                iconContainer.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                title.textContent = type === 'arrivee' ? "⚠️ ARRIVÉE EN RETARD" : "⚠️ DÉPART TARDIF";
                msg.textContent = message;
                resetTimeout = setTimeout(resetBorne, 5000);
            }

            function showError(message) {
                if (resetTimeout) clearTimeout(resetTimeout);
                card.className = "status-card status-card-error";
                iconContainer.innerHTML = '<i class="fas fa-times-circle"></i>';
                title.textContent = "❌ POINTAGE REFUSÉ";
                msg.textContent = message;
                resetTimeout = setTimeout(resetBorne, 4000);
            }

            function showProcessing() {
                if (resetTimeout) clearTimeout(resetTimeout);
                card.className = "status-card status-card-processing";
                iconContainer.innerHTML = '<i class="fas fa-spinner"></i>';
                title.textContent = "Traitement en cours...";
                msg.textContent = "Veuillez patienter...";
            }

            function resetBorne() {
                card.className = "status-card status-card-waiting";
                iconContainer.innerHTML = '<i class="fas fa-qrcode"></i>';
                title.textContent = "En attente de scan...";
                msg.textContent = "Le système est prêt à lire un badge.";
                setFocus();
                resetTimeout = null;
            }

            // ── Changement de site (uniquement sur clic bouton) ────────
            function updateSelectedSite(siteId, siteName) {
                currentSiteId = siteId;
                if (selectedSiteName) {
                    selectedSiteName.textContent = 'Site : ' + siteName;
                }

                showProcessing();

                fetch("{{ route('pointage.set-site') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ site_id: siteId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const targetUrl = "{{ url('/borne-pointage') }}" + '/' + siteId;
                        // Rediriger seulement si on n'est pas déjà sur la bonne URL
                        if (window.location.href !== targetUrl) {
                            window.location.href = targetUrl;
                        } else {
                            resetBorne();
                        }
                    } else {
                        showError(data.message || "Erreur lors du changement de site.");
                    }
                })
                .catch(error => {
                    console.error('Erreur sauvegarde site:', error);
                    showError("Erreur de communication avec le serveur.");
                });
            }

            // ── Bouton "Valider" site ──────────────────────────────────
            // NE PAS appeler updateSelectedSite au chargement
            // Uniquement sur clic utilisateur
            if (confirmSiteBtn) {
                confirmSiteBtn.addEventListener('click', function () {
                    if (!siteSelect || !siteSelect.options) {
                        showError('Sélecteur de site introuvable.');
                        return;
                    }
                    const selectedOption = siteSelect.options[siteSelect.selectedIndex];
                    if (siteSelect.value && selectedOption) {
                        updateSelectedSite(parseInt(siteSelect.value), selectedOption.text.trim());
                    } else {
                        showError('Veuillez sélectionner un site valide.');
                    }
                });
            }

            // ── Extraction / formatage du matricule ────────────────────
            function extractMatriculeFromNoise(value) {
                if (!value) return '';
                const pattern = /MPT\d{2}\s*\d{2}\s*\d{4}\s*\d{2}\s*\d{3}/i;
                const match = value.match(pattern);
                if (match) return match[0].replace(/\s+/g, ' ').toUpperCase();
                const numbers = value.replace(/[^0-9]/g, '');
                if (numbers.length >= 13) {
                    return `MPT${numbers.substring(0,2)} ${numbers.substring(2,4)} ${numbers.substring(4,8)} ${numbers.substring(8,10)} ${numbers.substring(10,13)}`;
                }
                return '';
            }

            function formatMatricule(value) {
                if (!value) return '';
                let matricule = extractMatriculeFromNoise(value);
                if (!matricule) {
                    matricule = value.trim()
                        .replace(/[\x00-\x1F\x7F-\x9F]/g, '')
                        .replace(/[\r\n\t]/g, '')
                        .toUpperCase()
                        .replace(/\s+/g, ' ')
                        .trim();
                }
                return matricule;
            }

            // ── Traitement du buffer scanner ───────────────────────────
            function processScanBuffer() {
                const rawData = scanBuffer.trim();
                scanBuffer = '';
                if (input) input.value = '';
                if (!rawData) return;

                if (!currentSiteId) {
                    showError("Veuillez sélectionner un site avant de pointer.");
                    return;
                }

                const matricule = formatMatricule(rawData);
                if (matricule && matricule.length > 10) {
                    sendAttendance(matricule);
                } else if (rawData.length > 0) {
                    showError("Badge non reconnu. Format invalide.");
                }
            }

            // ── Capture clavier (douchette) ────────────────────────────
            window.addEventListener('keydown', function (e) {
                if (document.activeElement === manualInput) return;
                if (siteSelect && document.activeElement === siteSelect) return;
                if (e.ctrlKey || e.altKey || e.metaKey) {
                    e.preventDefault();
                    return;
                }
                if (e.key.length > 1 && e.key !== 'Enter') {
                    if (e.key !== 'Tab' && e.key !== 'Escape') e.preventDefault();
                    return;
                }
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (scanTimeout) clearTimeout(scanTimeout);
                    processScanBuffer();
                    return;
                }
                scanBuffer += e.key;
                if (input) input.value = scanBuffer;
                if (scanTimeout) clearTimeout(scanTimeout);
                scanTimeout = setTimeout(() => processScanBuffer(), 40);
                e.preventDefault();
            }, { passive: false });

            // ── Envoi du pointage ──────────────────────────────────────
            function sendAttendance(matricule) {
                if (!currentSiteId) {
                    showError("Impossible de pointer : Aucun site valide identifié.");
                    return;
                }
                showProcessing();
                fetch("{{ route('pointages.badge.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ matricule: matricule, site_id: currentSiteId }),
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.status === 'late') showWarning(data.message, 'arrivee');
                        else if (data.is_late_departure === true) showWarning(data.message, 'depart');
                        else showSuccess(data.message, data.type);
                    } else {
                        showError(data.message || "Erreur inconnue");
                    }
                })
                .catch(error => {
                    console.error("Erreur:", error);
                    showError("Erreur de communication avec le serveur.");
                });
            }

            // ── Mode dépannage (saisie manuelle) ───────────────────────
            document.getElementById('manual-btn')?.addEventListener('click', function () {
                if (!manualInput) return;
                if (!currentSiteId) {
                    showError("Veuillez sélectionner un site avant de pointer.");
                    return;
                }
                const manualValue = manualInput.value.trim().toUpperCase();
                if (manualValue && manualValue.length > 5) {
                    sendAttendance(manualValue);
                    manualInput.value = '';
                } else {
                    showError("Veuillez entrer un matricule valide (ex: MPT02 96 5114 81 893)");
                }
            });

            manualInput?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('manual-btn')?.click();
                }
            });
        });
    </script>
</x-app-layout>
