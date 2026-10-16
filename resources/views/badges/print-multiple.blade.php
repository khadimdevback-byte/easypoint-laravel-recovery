<x-app-layout>
    {{-- Zone d'impression (cachée par défaut, visible uniquement lors de l'impression) --}}
    <div id="print-area">
        <div class="badges-grid">
            @foreach($users as $index => $user)
                <div class="badge-item {{ $index % 6 === 5 ? 'last-in-row' : '' }}">
                    @include('components.badge', ['user' => $user])
                </div>
            @endforeach
        </div>
    </div>

    {{-- Contrôles (masqués à l'impression) --}}
    <div class="print-controls no-print">
        <div class="controls-container">
            <button onclick="printBadges()" class="print-btn">
                🖨️ Imprimer les badges
            </button>
            <button onclick="history.back()" class="back-btn">
                ← Retour
            </button>
        </div>
        <div class="info-banner">
            <span>📄 {{ count($users) }} badge(s) à imprimer</span>
            <span>📏 Format A4 - 3x2 badges par page</span>
        </div>
    </div>

    {{-- Aperçu à l'écran --}}
    <div class="badges-preview">
        <div class="badges-grid">
            @foreach($users as $index => $user)
                <div class="badge-item {{ $index % 6 === 5 ? 'last-in-row' : '' }}">
                    @include('components.badge', ['user' => $user])
                </div>
            @endforeach
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES D'IMPRESSION - UNIQUEMENT LES BADGES  */
        /* ============================================ */
        
        @media print {
            /* Masquer tout sauf la zone d'impression */
            body * {
                visibility: hidden !important;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                background: white;
            }

            /* Masquer les contrôles et l'aperçu */
            .print-controls,
            .badges-preview,
            .no-print,
            header,
            nav,
            footer {
                display: none !important;
            }

            /* Style des badges pour l'impression */
            .badges-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 40px 30px; /* gap: [vertical] [horizontal] */
                padding: 15px;
                margin: 0 auto;
                page-break-inside: avoid;
            }

            .badge-item {
                width: 100%;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .badge-card {
                break-inside: avoid;
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Saut de page après chaque ligne de 3 badges */
            .badge-item.last-in-row {
                page-break-after: always;
            }
        }

        /* ============================================ */
        /* STYLES ÉCRAN - VOS STYLES ORIGINAUX          */
        /* ============================================ */

        .print-controls {
            background: white;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .controls-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 10px;
        }

        .print-btn {
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .print-btn:hover {
            background: #059669;
        }

        .back-btn {
            background: #6b7280;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        .info-banner {
            text-align: center;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 13px;
            color: #374151;
        }

        /* Grille écran - Votre design original */
        .badges-preview {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .badges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .badge-item {
            width: 100%;
            height: 180px;
            transition: transform 0.2s;
        }

        .badge-item:hover {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (min-width: 1024px) {
            .badges-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .badges-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 15px;
            }
        }

        /* Zone d'impression cachée à l'écran */
        #print-area {
            display: none;
        }
    </style>

    <script>
        function printBadges() {
            window.print();
        }

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                // Laisser le comportement normal
            }
        });
    </script>
</x-app-layout>