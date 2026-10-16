@php
    $color = $user->company->badge_header_color ?? '#1D4ED8';
    $companyName = $user->company->compagny_name ?? 'Nom de la Compagnie';
    $qrData = $user->matricule;
@endphp

<div class="badge-card">
    <!-- Header avec dégradé -->
    <div class="badge-header" style="background: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}cc 100%);">
        <div class="company-logo">
            @if($user->company->logo && Storage::disk('public')->exists($user->company->logo))
                <img src="{{ asset('storage/' . $user->company->logo) }}" alt="Logo" class="logo-img">
            @else
                <span class="company-initial">{{ substr($companyName, 0, 1) }}</span>
            @endif
        </div>
        <div class="company-name">{{ Str::limit($companyName, 25) }}</div>
        <div class="badge-type">BADGE</div>
    </div>

    <!-- Corps principal -->
    <div class="badge-body">
        <!-- Photo -->
        <div class="photo-section">
            @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                <img src="{{ asset('storage/' . $user->avatar) }}" class="employee-photo" />
            @else
                <div class="photo-placeholder">
                    <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Informations employé -->
        <div class="info-section">
            <div class="employee-name">{{ strtoupper($user->name) }} {{ $user->first_name }}</div>
            <div class="employee-function">{{ $user->fonction->nom_fonction ?? 'Fonction non précisée' }}</div>
            <div class="employee-details">
                <div class="detail-row">
                    <span class="detail-label" style="font-size: 10px">Matricule</span>
                    <span class="detail-value">{{ $user->matricule }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label" style="font-size: 10px">Site</span>
                    <span class="detail-value">
                        @if($user->sites && $user->sites->count())
                            {{ $user->sites->first()->name }}
                            @if($user->sites->count() > 1)
                                +{{ $user->sites->count() - 1 }}
                            @endif
                        @else
                            Non précisé
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label" style="font-size: 10px">Depuis le</span>
                    <span class="detail-value">{{ $user->contract_start_date ? \Carbon\Carbon::parse($user->contract_start_date)->format('d/m/Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qrcode-section">
            {!! QrCode::size(70)->errorCorrection('M')->generate($qrData) !!}
        </div>
    </div>

    <!-- Pied de page -->
    <div class="badge-footer">
        <div class="footer-text" style="font-size: 10px ; color:black">Valable jusqu'au : {{ $user->contract_end_date ? \Carbon\Carbon::parse($user->contract_end_date)->format('d/m/Y') : 'Indéterminé' }}</div>
        <div class="footer-logo" style="font-size: 10px ; color:black">Easy-Point</div>
    </div>
</div>

<style>
    .badge-card {
        width: 100%;
        height: 130%;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        flex-direction: column;
    }
        /* Header */
    .badge-header {
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
        height: 45px;
        flex-shrink: 0;
    }
    .company-logo {
        width: 30px;
        height: 30px;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .company-initial {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .company-name {
        flex: 1;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-type {
        font-size: 10px;
        font-weight: bold;
        background: rgba(255,255,255,0.2);
        padding: 4px 8px;
        border-radius: 20px;
        letter-spacing: 1px;
    }

    /* Body */
    .badge-body {
        flex: 1;
        padding: 12px;
        display: flex;
        gap: 12px;
        background: white;
        min-height: 0;
    }

    /* Photo */
    .photo-section {
        width: 65px;
        flex-shrink: 0;
    }

    .employee-photo {
        width: 65px;
        height: 65px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .photo-placeholder {
        width: 65px;
        height: 65px;
        border-radius: 10px;
        background: #f3f4f6;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .placeholder-icon {
        width: 30px;
        height: 30px;
        color: #9ca3af;
    }

    /* Info section */
    .info-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .employee-name {
        font-size: 10px;
        font-weight: 800;
        color: #1f2937;
        text-transform: uppercase;
        line-height: 1.3;
    }

    .employee-function {
        font-size: 8px;
        color: #6b7280;
        font-style: italic;
        border-left: 2px solid #e5e7eb;
        padding-left: 6px;
    }

    .employee-details {
        background: #f9fafb;
        padding: 6px;
        border-radius: 6px;
        margin-top: 2px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        font-size: 7px;
        margin-bottom: 3px;
        line-height: 1.3;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }

    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }

    .detail-value {
        color: #1f2937;
        font-weight: 700;
    }

    /* QR Code */
    .qrcode-section {
        width: 70px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qrcode-section svg {
        width: 100%;
        height: auto;
    }

    /* Footer */
    .badge-footer {
        background: #f9fafb;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #e5e7eb;
        flex-shrink: 0;
        min-height: 40px;
    }

    .footer-text {
        color: #6b7280;
        font-size: 9px;
    }

    .footer-logo {
        color: #9ca3af;
        font-weight: 600;
        letter-spacing: 1px;
        font-size: 9px;
    }
</style>
