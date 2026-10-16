@php
        use Carbon\Carbon;
        $debutMois = Carbon::parse($fiche->date_debut_periode)->startOfMonth();
        $finMois = Carbon::parse($fiche->date_debut_periode)->endOfMonth();

        $heures = floor($total_heures);
        $minutes = round(($total_heures - $heures) * 60);
        $affichageHeureMinute = sprintf('%02dh%02d', $heures, $minutes);

        $totalBrut = $brut;
        $totalSalariale = 0;
        $totalEmployeurCotisations = 0;
        foreach ($cotisations as $cotisation) {
            $totalSalariale += $cotisation['part_salariale'] ?? 0;
            $totalEmployeurCotisations += $cotisation['part_employeur'] ?? 0;
        }

        $retenues = $fiche->retenues ?? 0;
        $avancements = $fiche->avancements ?? 0;
        $impotSource = $fiche->impot_source ?? 0;

        $netAvantImpot = $brut - $totalSalariale - $retenues - $avancements;
        $netSocial = $netAvantImpot;
        $netAPayer = $netAvantImpot - $impotSource;
        $totalCoutEmployeur = $brut + ($fiche->part_employeur_salaire ?? 0) + $totalEmployeurCotisations;
    @endphp

<x-app-layout>

    <style>
        /* Styles personnalisés pour l'affichage à l'écran */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5; /* Fond gris clair pour la page */
        }

        .payslip-container {
            width: 210mm; /* Largeur A4 pour l'affichage écran */
            min-height: 297mm; /* Hauteur A4 pour l'affichage écran (pour donner une idée de la taille) */
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15mm;
            box-sizing: border-box; /* S'assure que le padding est inclus dans la largeur/hauteur */
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .header-left p, .header-right p {
            margin: 0;
            line-height: 1.4;
            font-size: 10pt;
        }

        .header-title {
            background-color: #3b82f6; /* Bleu */
            color: white;
            padding: 8px 15px;
            font-size: 14pt;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .payslip-period {
            font-size: 10pt;
            font-weight: bold;
            text-align: right;
            margin-top: 5px;
            color: #4b5563; /* Couleur de texte légèrement plus foncée */
        }

        .company-info, .employee-info {
            border: 1px solid #d1d5db; /* Bordure gris clair */
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 9pt;
            line-height: 1.6;
            flex: 1; /* Permet aux blocs de prendre l'espace disponible */
        }
        .company-info { margin-right: 10px; } /* Espacement entre les deux blocs */
        .employee-info { margin-left: 10px; } /* Espacement entre les deux blocs */


        .company-info p, .employee-info p {
            margin: 0;
        }

        .company-info strong, .employee-info strong {
            display: inline-block;
            width: 70px; /* Alignement des labels */
        }

        .table-section {
            margin-bottom: 20px;
        }

        .table-section table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .table-section th, .table-section td {
            border: 1px solid #e5e7eb; /* Bordure gris plus clair */
            padding: 8px 15px; /* Augmentation du padding horizontal pour un aspect plus aéré */
            text-align: left;
        }

        .table-section thead th {
            background-color: #eff6ff; /* Bleu clair pour l'entête */
            color: #1f2937; /* Texte plus foncé */
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            white-space: nowrap; /* Empêche le retour à la ligne dans les en-têtes */
        }

        .table-section tbody tr:nth-child(even) {
            background-color: #f9fafb; /* Fond légèrement différent pour les lignes paires */
        }

        .table-section .total-row td {
            background-color: #dbeafe; /* Bleu clair pour les lignes totales */
            font-weight: bold;
        }

        .table-section .subtotal-header td {
            background-color: #bfdbfe; /* Bleu un peu plus foncé pour les totaux de catégorie */
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        .footer-summary {
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            margin-top: 20px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .summary-table td {
            padding: 8px 15px; /* Ajusté pour cohérence */
            border: 1px solid #e5e7eb;
        }

        .summary-table .highlighted-row td {
            background-color: #dbeafe; /* Bleu clair pour les totaux du résumé */
            font-weight: bold;
        }

        .net-pay-box {
            background-color: #3b82f6; /* Fond bleu pour le salaire net */
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .net-pay-label {
            font-size: 12pt;
        }

        .net-pay-amount {
            font-size: 18pt;
            white-space: nowrap;
        }

        .info-text {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 15px;
            text-align: center;
        }

        .cost-employer {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 10px;
            text-align: center;
            color: #374151; /* Couleur de texte plus foncée */
        }

        .no-print-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #cccccc;
        }

        .no-print-buttons a, .no-print-buttons button {
            display: inline-flex;
            align-items: center;
            padding: 8px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 10pt;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s, border-color 0.2s; /* Ajout border-color pour hover */
            border: 1px solid transparent; /* Bordure transparente par défaut */
        }

        .no-print-buttons a {
            background-color: #e0e0e0;
            color: #333;
        }

        .no-print-buttons a:hover {
            background-color: #d0d0d0;
            border-color: #c0c0c0;
        }

        .no-print-buttons button {
            background-color: #3b82f6;
            color: white;
            border: none;
        }

        .no-print-buttons button:hover {
            background-color: #2563eb;
            border-color: #1d4ed8;
        }

        /* Styles spécifiques pour l'impression */
        @media print {
            html, body {
                font-family: 'Arial', sans-serif !important;
                font-size: 9pt !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important; /* Ajout pour meilleure compatibilité */
            }

            .payslip-container {
                width: 100% !important;
                margin: 0 auto !important;
                padding: 5mm !important; /* Réduit le padding interne pour donner plus d'espace au contenu */
                box-shadow: none !important;
                border: none !important;
                min-height: auto !important;
            }

            table {
                font-size: 8pt !important;
                width: 100% !important;
                border-collapse: collapse;
                table-layout: fixed !important; /* TRÈS IMPORTANT : Aide à la distribution des colonnes */
            }
            th, td {
                padding: 5px 8px !important; /* Espacement dans les cellules */
                word-wrap: break-word !important; /* Permet aux mots longs de se couper */
                white-space: normal !important; /* Permet au texte des en-têtes et cellules de s'enrouler */
            }
            /* --- Ajustement des largeurs de colonnes pour la TABLE PRINCIPALE en mode impression --- */
            .table-section table thead th:nth-child(1), .table-section table tbody td:nth-child(1) { width: 30% !important; text-align: left !important; } /* Rubriques */
            .table-section table thead th:nth-child(2), .table-section table tbody td:nth-child(2) { width: 8% !important; text-align: center !important; } /* Nombre */
            .table-section table thead th:nth-child(3), .table-section table tbody td:nth-child(3) { width: 12% !important; text-align: right !important; } /* Base */
            .table-section table thead th:nth-child(4), .table-section table tbody td:nth-child(4) { width: 8% !important; text-align: right !important; } /* Taux */
            .table-section table thead th:nth-child(5), .table-section table tbody td:nth-child(5) { width: 14% !important; text-align: right !important; } /* Part salariale (+) */
            .table-section table thead th:nth-child(6), .table-section table tbody td:nth-child(6) { width: 14% !important; text-align: right !important; } /* Retenue (-) */
            .table-section table thead th:nth-child(7), .table-section table tbody td:nth-child(7) { width: 14% !important; text-align: right !important; } /* Part Employeur */
            /* Total: 30+8+12+8+14+14+14 = 100% */
            /* --- Ajustement des largeurs de colonnes pour la TABLE DU BAS (ADDITIONAL INFO) en mode impression --- */
            /* 10 colonnes: Label, Value, Label, Value, Label, Value, Label, Value, Label, Value */
            .table-section:last-of-type table th:nth-child(1), .table-section:last-of-type table td:nth-child(1) { width: 12% !important; text-align: left !important; }
            .table-section:last-of-type table th:nth-child(2), .table-section:last-of-type table td:nth-child(2) { width: 8% !important; text-align: right !important; }
            .table-section:last-of-type table th:nth-child(3), .table-section:last-of-type table td:nth-child(3) { width: 12% !important; text-align: left !important; }
            .table-section:last-of-type table th:nth-child(4), .table-section:last-of-type table td:nth-child(4) { width: 8% !important; text-align: right !important; }
            .table-section:last-of-type table th:nth-child(5), .table-section:last-of-type table td:nth-child(5) { width: 12% !important; text-align: left !important; }
            .table-section:last-of-type table th:nth-child(6), .table-section:last-of-type table td:nth-child(6) { width: 8% !important; text-align: right !important; }
            .table-section:last-of-type table th:nth-child(7), .table-section:last-of-type table td:nth-child(7) { width: 12% !important; text-align: left !important; }
            .table-section:last-of-type table th:nth-child(8), .table-section:last-of-type table td:nth-child(8) { width: 8% !important; text-align: right !important; }
            .table-section:last-of-type table th:nth-child(9), .table-section:last-of-type table td:nth-child(9) { width: 12% !important; text-align: left !important; }
            .table-section:last-of-type table th:nth-child(10), .table-section:last-of-type table td:nth-child(10) { width: 8% !important; text-align: right !important; }
            /* Total: 12*5 + 8*5 = 60 + 40 = 100% */
            .section-title, table {
                page-break-inside: avoid !important;
            }
            @page {
                margin: 5mm !important;
            }
            .no-print, nav.fixed {
                display: none !important;
            }
            /* Réapplique explicitement les couleurs et le gras */
            .header-title {
                background-color: #3b82f6 !important;
                color: white !important;
            }
            .net-pay-box {
                background-color: #3b82f6 !important;
                color: white !important;
            }
            .table-section thead th {
                background-color: #eff6ff !important;
                color: #1f2937 !important;
            }
            .table-section tbody tr:nth-child(even) {
                background-color: #f9fafb !important;
            }
            .table-section .total-row td {
                background-color: #dbeafe !important;
                font-weight: bold !important;
            }
            .table-section .subtotal-header td {
                background-color: #bfdbfe !important;
                font-weight: bold !important;
            }
            .summary-table .highlighted-row td {
                background-color: #dbeafe !important;
                font-weight: bold !important;
            }
            .net-pay-amount {
                color: white !important;
            }
        }
    </style>

    <div class="payslip-container" id="fiche-paie">
        {{-- EN-TÊTE --}}
        <div class="header-section">
            <div class="header-left">
                <div class="header-title">BULLETIN DE PAIE</div>
            </div>
            <div class="header-right">
                <p class="payslip-period">PÉRIODE DU {{ $debutMois->format('d/m/Y') }} AU {{ $finMois->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- INFOS EMPLOYEUR / EMPLOYÉ --}}
        <div class="header-section">
            <div class="company-info">
                <p><strong>Société :</strong> {{ $fiche->user->company->compagny_name }}</p>
                <p><strong>Adresse :</strong> {{ $fiche->user->company->address }}</p>
                <p><strong>IBAN :</strong> {{ $fiche->user->company->iban }}</p>
                <p><strong>SIRET :</strong> {{ $fiche->user->company->n_siret }}</p>
                <p><strong>Siren :</strong> {{ $fiche->user->company->n_siren }}</p>
                <p><strong>Téléphone :</strong> {{ $fiche->user->company->phone }}</p>
            </div>
            <div class="employee-info">
                <p><strong>Employé(e) :</strong> {{ $fiche->user->name }} {{ $fiche->user->first_name }}</p>
                <p><strong>Adresse :</strong> {{ $fiche->user->address }}</p>
                <p><strong>Ville :</strong> {{ $fiche->user->zip_code}} {{ $fiche->user->city}}</p>
                <p><strong>EMPLOI :</strong> {{ $fiche->user->fonction->nom_fonction }}</p>
                <p><strong>Coef :</strong> {{ $fiche->user->qualification }}</p>
                <p><strong>Matricule :</strong> {{ $fiche->user->matricule }}</p>
            </div>
        </div>

        {{-- TABLE PRINCIPALE --}}
        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Rubriques</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-right">Base</th>
                        <th class="text-right">Taux</th>
                        <th class="text-right">Part salariale (+)</th>
                        <th class="text-right">Retenue (-)</th>
                        <th class="text-right">Part Employeur</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- SALAIRE PAR HEURE --}}
                    @if($fiche->user->salary_rate > 0)
                        <tr>
                            <td>Heures travaillées</td>
                            <td class="text-center">{{ number_format($total_heures, 2) }} ({{ $affichageHeureMinute }})</td>
                            <td class="text-right">{{ number_format($fiche->user->salary_rate, 2) }} {{ $fiche->company->currency }}/h</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{ number_format($montantHoraire, 2) }}</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{ number_format($fiche->part_employeur_salaire ?? 0, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-danger">Taux horaire non défini pour cet employé.</td>
                        </tr>
                    @endif

                    {{-- PRIMES --}}
                    @if ($fiche->primes > 0)
                        <tr>
                            <td>Primes</td>
                            <td class="text-center"></td>
                            <td class="text-right">{{ number_format($fiche->primes, 2) }} {{ $fiche->company->currency }}</td>
                            <td class="text-right"></td>
                            <td class="text-right">{{ number_format($fiche->primes, 2) }}</td>
                            <td class="text-right"></td>
                            <td class="text-right">0.00</td>
                        </tr>
                    @endif

                    {{-- TOTAL BRUT --}}
                    <tr class="total-row">
                        <td colspan="4">Total brut</td>
                        <td class="text-right">{{ number_format($brut, 2) }}</td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($fiche->part_employeur_salaire + $totalEmployeurCotisations, 2) }}</td>
                    </tr>

                    {{-- AVANCEMENTS --}}
                    @if ($avancements > 0)
                        <tr>
                            <td>Avancement sur salaire</td>
                            <td colspan="4"></td>
                            <td class="text-right">{{ number_format($avancements, 2) }}</td>
                            <td></td>
                        </tr>
                    @endif

                    {{-- RETENUES --}}
                    @if ($retenues > 0)
                        <tr>
                            <td>Autres retenues</td>
                            <td colspan="4"></td>
                            <td class="text-right">{{ number_format($retenues, 2) }}</td>
                            <td></td>
                        </tr>
                    @endif

                    {{-- COTISATIONS --}}
                    @forelse($cotisations as $cotisation)
                        <tr>
                            <td>{{ $cotisation['libelle'] }}</td>
                            <td></td>
                            <td class="text-right">{{ number_format($cotisation['base'], 2) }}</td>
                            <td class="text-right">{{ number_format($cotisation['taux'], 2) }}%</td>
                            <td></td>
                            <td class="text-right">{{ number_format($cotisation['part_salariale'], 2) }}</td>
                            <td class="text-right">{{ number_format($cotisation['part_employeur'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-gray-500">Aucune cotisation définie</td></tr>
                    @endforelse

                    {{-- TOTAL COTISATIONS --}}
                    <tr class="total-row subtotal-header">
                        <td colspan="4">Total des cotisations</td>
                        <td></td>
                        <td class="text-right">{{ number_format($totalSalariale, 2) }}</td>
                        <td class="text-right">{{ number_format($totalEmployeurCotisations, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- RÉSUMÉ --}}
        <div class="summary-table-section">
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td colspan="2">Montant net social</td>
                        <td class="text-right">{{ number_format($netSocial, 2) }} {{ $fiche->company->currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Frais de déplacement</td>
                        <td class="text-right">0.00 {{ $fiche->company->currency }}</td>
                    </tr>
                    <tr class="highlighted-row">
                        <td colspan="2">NET À PAYER AVANT IMPÔT</td>
                        <td class="text-right">{{ number_format($netAvantImpot, 2) }} {{ $fiche->company->currency }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Net imposable</td>
                        <td class="text-right">{{ number_format($netAvantImpot, 2) }} {{ $fiche->company->currency }}</td>
                    </tr>
                    <tr>
                        <td>Impôt à la source</td>
                        <td>Taux : {{ $netAvantImpot != 0 ? number_format(($impotSource / $netAvantImpot) * 100, 2) : '0.00' }}%</td>
                        <td class="text-right">{{ number_format($impotSource, 2) }} {{ $fiche->company->currency }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- NET À PAYER --}}
        <div class="net-pay-box">
            <span class="net-pay-label">NET À PAYER APRÈS IMPÔT</span>
            <span class="net-pay-amount">{{ number_format($netAPayer, 2) }} {{ $fiche->company->currency }}</span>
        </div>

        {{-- TOTAL COÛT EMPLOYEUR --}}
        <p class="cost-employer"><strong>TOTAL COÛT EMPLOYEUR :</strong> {{ number_format($totalCoutEmployeur, 2) }} {{ $fiche->company->currency }}</p>

        {{-- ACTIONS --}}
        <div class="no-print no-print-buttons">
            <a href="{{ route($fiche->horaire_type === 'fixe' ? 'fichepaie.fixe' : 'fichepaie.rotatif') }}">← Retour</a>
            <a href="{{ route('fichepaie.edit', ['fichePaie' => $fiche->id]) }}" class="button-modifier">✏️ Modifier</a>
            <button onclick="window.print()">🖨️ Imprimer</button>
        </div>
    </div>
</x-app-layout>
