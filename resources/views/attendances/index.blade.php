@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    {{-- Barre d'actions --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <div class="action-info">
                <i class="fas fa-calendar-check"></i>
                <span>{{ __('Tableau des Pointages') }}</span>
            </div>
            <div class="action-date">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ Carbon::create($year, $month)->translatedFormat('F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="content-card">
            {{-- Formulaire de filtrage --}}
            <div class="filters-section">
                <form method="GET" action="{{ route('attendances.index') }}" class="filters-form">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Site</label>
                            <select name="site" class="filter-select">
                                <option value="">Sélectionnez un site</option>
                                @foreach ($sites as $siteItem)
                                    <option value="{{ $siteItem->id }}" {{ request('site') == $siteItem->id ? 'selected' : '' }}>
                                        {{ $siteItem->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Mois</label>
                            <select name="month" class="filter-select">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>
                                        {{ Carbon::create()->month($i)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Année</label>
                            <select name="year" class="filter-select">
                                @foreach (range(now()->year - 5, now()->year + 5) as $yearOption)
                                    <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                                        {{ $yearOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group filter-actions">
                            <button type="submit" class="filter-btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('attendances.index') }}" class="filter-btn-secondary">
                                <i class="fas fa-sync-alt"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Légende des statuts --}}
                <div class="legend-section">
                    <div class="legend-item">
                        <i class="fa-solid fa-check text-green-500"></i>
                        <i class="fa-solid fa-arrow-right-long text-gray-400 mx-2"></i>
                        <span class="font-semibold">Présent</span>
                    </div>

                    <div class="legend-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-exclamation-circle text-yellow-500" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                        </svg>
                        <i class="fa-solid fa-arrow-right-long text-gray-400 mx-2"></i>
                        <span class="font-semibold">En retard</span>
                    </div>

                    <div class="legend-item">
                        <i class="fa-solid fa-xmark text-red-500"></i>
                        <i class="fa-solid fa-arrow-right-long text-gray-400 mx-2"></i>
                        <span class="font-semibold">Absent</span>
                    </div>

                    <div class="legend-item">
                        <i class="fa-solid fa-umbrella-beach text-blue-500"></i>
                        <i class="fa-solid fa-arrow-right-long text-gray-400 mx-2"></i>
                        <span class="font-semibold">Congé/Permission</span>
                    </div>
                </div>
            </div>

            {{-- Export buttons --}}
            <div class="export-section">
                <div class="export-buttons">
                    <button id="export-excel" class="export-btn export-btn-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button id="export-csv" class="export-btn export-btn-csv">
                        <i class="fas fa-file-alt"></i> Export CSV
                    </button>
                    <button id="export-pdf" class="export-btn export-btn-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            {{-- Tableau des pointages --}}
            <div class="table-container">
                @if ($users->count() > 0)
                    <div class="table-wrapper">
                        <table class="attendance-table" id="export-table">
                            <thead>
                                <tr>
                                    <th class="sticky-col" style="min-width: 140px;">Employés</th>
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $currentDay = Carbon::create($year, $month, $i);
                                            $isSunday   = $currentDay->dayOfWeek === Carbon::SUNDAY;
                                            $dayName    = $currentDay->translatedFormat('D');
                                            $isToday    = $currentDay->isSameDay(Carbon::today());
                                        @endphp
                                        <th class="day-cell {{ $isToday ? 'day-today' : '' }} {{ $isSunday ? 'day-sunday' : '' }}">
                                            <div class="day-number">{{ $i }}</div>
                                            <div class="day-name">{{ strtolower(substr($dayName, 0, 1)) }}</div>
                                        </th>
                                    @endfor
                                    <th class="total-cell">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $workedDays        = 0;
                                        $userCreatedAt     = Carbon::parse($user->created_at);
                                        $userContractStart = $user->contract_start_date ? Carbon::parse($user->contract_start_date) : null;
                                        $userContractEnd   = $user->contract_end_date   ? Carbon::parse($user->contract_end_date)   : null;
                                        $userIsActive      = $user->status === 'active';
                                    @endphp
                                    <tr class="attendance-row">
                                        <td class="employee-cell sticky-col">
                                            <div class="employee-info">
                                                @if ($user->avatar && Storage::disk('public')->exists($user->avatar))
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="employee-avatar" alt="">
                                                @else
                                                    <div class="employee-avatar-placeholder">
                                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="employee-name">{{ $user->first_name }} {{ $user->name }}</span>
                                            </div>
                                        </td>

                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                            @php
                                                $currentDate = Carbon::create($year, $month, $i);
                                                $attendance  = $attendances[$user->id][$i] ?? null;

                                                $wasUserCreated = $currentDate->greaterThanOrEqualTo($userCreatedAt->copy()->startOfDay());
                                                $contractStarted = !$userContractStart || $currentDate->greaterThanOrEqualTo($userContractStart->copy()->startOfDay());
                                                $contractNotExpired = !$userContractEnd || $currentDate->lessThanOrEqualTo($userContractEnd->copy()->endOfDay());

                                                $isOnLeave     = false;
                                                $leaveCategorie = null;
                                                if (isset($userLeaves[$user->id])) {
                                                    foreach ($userLeaves[$user->id] as $leave) {
                                                        $leaveStart = Carbon::parse($leave->date_debut);
                                                        $leaveEnd   = Carbon::parse($leave->date_fin);
                                                        if ($currentDate->between($leaveStart, $leaveEnd)) {
                                                            $isOnLeave      = true;
                                                            $leaveCategorie = $leave->categorie;
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <td class="attendance-cell">
                                                @if ($currentDate->isFuture())
                                                    <span class="status-future">—</span>
                                                @elseif (!$wasUserCreated || !$contractStarted)
                                                    <span class="status-na" title="Avant création du compte ou début contrat">—</span>
                                                @elseif (!$contractNotExpired)
                                                    <span class="status-na" title="Contrat expiré">—</span>
                                                @elseif (!$userIsActive)
                                                    <span class="status-na" title="Compte désactivé">—</span>
                                                @elseif ($isOnLeave)
                                                    <span class="status-leave" title="{{ $leaveCategorie === 'conge' ? 'Congé' : 'Permission' }}">
                                                        <i class="fa-solid fa-umbrella-beach text-blue-500"></i>
                                                    </span>
                                                @elseif ($attendance)
                                                    @if ($attendance['status'] === 'present')
                                                        @php $workedDays++; @endphp
                                                        <a href="{{ route('attendances.show', ['user' => $user->id, 'day' => $i, 'month' => $month, 'year' => $year]) }}" class="status-present" title="Présent">
                                                            <i class="fa-solid fa-check text-green-500"></i>
                                                        </a>
                                                    @elseif ($attendance['status'] === 'late')
                                                        @php $workedDays++; @endphp
                                                        <a href="{{ route('attendances.show', ['user' => $user->id, 'day' => $i, 'month' => $month, 'year' => $year]) }}" class="status-late" title="En retard">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                class="bi bi-exclamation-circle text-yellow-500" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                                            </svg>
                                                        </a>
                                                    @else
                                                        <span class="status-absent" title="Absent">
                                                            <i class="fa-solid fa-xmark text-red-500"></i>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="status-absent" title="Absent">
                                                        <i class="fa-solid fa-xmark text-red-500"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        @endfor

                                        {{-- Colonne Total --}}
                                        <td class="total-cell">
                                            @php
                                                $totalWorkableDays = 0;
                                                for ($j = 1; $j <= $daysInMonth; $j++) {
                                                    $currentDate = Carbon::create($year, $month, $j);
                                                    $wasUserCreated = $currentDate->greaterThanOrEqualTo($userCreatedAt->copy()->startOfDay());
                                                    $contractStarted = !$userContractStart || $currentDate->greaterThanOrEqualTo($userContractStart->copy()->startOfDay());
                                                    $contractNotExpired = !$userContractEnd || $currentDate->lessThanOrEqualTo($userContractEnd->copy()->endOfDay());

                                                    $isOnLeave = false;
                                                    if (isset($userLeaves[$user->id])) {
                                                        foreach ($userLeaves[$user->id] as $leave) {
                                                            $leaveStart = Carbon::parse($leave->date_debut);
                                                            $leaveEnd   = Carbon::parse($leave->date_fin);
                                                            if ($currentDate->between($leaveStart, $leaveEnd)) {
                                                                $isOnLeave = true;
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    if ($wasUserCreated && $contractStarted && $contractNotExpired && $userIsActive && !$isOnLeave && !$currentDate->isFuture()) {
                                                        $totalWorkableDays++;
                                                    }
                                                }
                                            @endphp
                                            <span class="total-present">{{ $workedDays }}</span>
                                            <span class="total-sep">/</span>
                                            <span class="total-workable">{{ $totalWorkableDays }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                        <p>Aucun utilisateur ou pointage disponible</p>
                        <span class="empty-sub">Veuillez sélectionner une autre période</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES - AFFICHAGE COMPACT         */
        /* ============================================ */

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
        .content-card { background: #fff; border-radius: 20px; border: 1px solid var(--gl); padding: 24px; margin: 0 24px; }

        .filters-section { margin-bottom: 24px; }
        .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 20px; align-items: end; }
        .filter-group { display: flex; flex-direction: column; gap: 6px; }
        .filter-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--gm); letter-spacing: 0.5px; }
        .filter-select { padding: 10px 14px; border: 1.5px solid var(--gl); border-radius: 12px; font-size: 0.85rem; background: white; transition: var(--t); }
        .filter-select:focus { outline: none; border-color: var(--vp); box-shadow: 0 0 0 3px rgba(46,125,100,0.1); }
        .filter-actions { display: flex; gap: 10px; }
        .filter-btn-primary, .filter-btn-secondary { padding: 10px 20px; border-radius: 40px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: var(--t); border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .filter-btn-primary { background: var(--vp); color: white; }
        .filter-btn-primary:hover { background: var(--vf); transform: translateY(-2px); }
        .filter-btn-secondary { background: var(--gtc); color: var(--gf); border: 1px solid var(--gl); }
        .filter-btn-secondary:hover { background: var(--gl); transform: translateY(-2px); }

        .legend-section {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            padding: 12px 20px;
            background: var(--gtc);
            border-radius: 16px;
            margin-bottom: 20px;
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--gf);
        }

        .export-section { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        .export-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .export-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 40px; font-size: 0.65rem; font-weight: 600; border: none; cursor: pointer; transition: var(--t); color: white; }
        .export-btn-excel { background: #10b981; }
        .export-btn-csv { background: #3b82f6; }
        .export-btn-pdf { background: #ef4444; }
        .export-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        .table-container { overflow-x: auto; border-radius: 16px; border: 1px solid var(--gl); background: white; }
        .table-wrapper { overflow-x: auto; }
        .attendance-table { width: 100%; border-collapse: collapse; font-size: 0.7rem; min-width: auto; }
        .attendance-table th { padding: 10px 4px; background: var(--gtc); font-weight: 600; color: var(--gf); border-bottom: 1px solid var(--gl); text-align: center; }
        .attendance-table td { padding: 8px 4px; border-bottom: 1px solid #f3f4f6; text-align: center; vertical-align: middle; }
        .attendance-table tr:hover td { background: #f9fafb; }

        .sticky-col { position: sticky; left: 0; background: white; z-index: 10; text-align: left !important; min-width: 140px; }
        .day-cell { min-width: 35px; width: 35px; }
        .day-today { background: #2e7d64; color: white; border-radius: 6px; }
        .day-sunday { background: #fee2e2; color: #dc2626; border-radius: 6px; }
        .day-number { font-weight: 700; font-size: 0.7rem; }
        .day-name { font-size: 0.55rem; opacity: 0.7; }

        .employee-cell { text-align: left; background: white; }
        .employee-info { display: flex; align-items: center; gap: 8px; }
        .employee-avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }
        .employee-avatar-placeholder { width: 28px; height: 28px; border-radius: 50%; background: var(--vp); color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; }
        .employee-name { font-weight: 600; font-size: 0.7rem; }

        .attendance-cell a, .attendance-cell span { font-size: 0.85rem; display: inline-block; text-decoration: none; }
        .status-present { color: #10b981; }
        .status-late { color: #f59e0b; }
        .status-absent { color: #ef4444; }
        .status-leave { color: #3b82f6; }
        .status-na, .status-future { color: #9ca3af; }

        .total-cell { font-weight: 700; background: #f9fafb; }
        .total-present { color: #2e7d64; }
        .total-workable { color: #6b7280; font-weight: 400; }
        .total-sep { color: #9ca3af; margin: 0 2px; }

        .pagination-wrapper { padding: 16px; border-top: 1px solid var(--gl); display: flex; justify-content: flex-end; }

        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-sub { font-size: 0.75rem; display: block; margin-top: 8px; }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 1200px) {
            .day-cell { min-width: 30px; width: 30px; }
            .day-number { font-size: 0.65rem; }
            .employee-name { font-size: 0.65rem; }
            .sticky-col { min-width: 120px; }
        }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .filters-grid { grid-template-columns: 1fr; }
            .filter-actions { flex-direction: column; }
            .filter-btn-primary, .filter-btn-secondary { width: 100%; justify-content: center; }
            .legend-section { flex-direction: column; gap: 10px; }
            .export-buttons { flex-direction: column; width: 100%; }
            .export-btn { justify-content: center; }
            .employee-info { min-width: 120px; }
            .day-cell { min-width: 28px; width: 28px; }
        }
    </style>

<script>
// Configuration AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
});

// 🔥 VÉRIFIER L'AUTHENTIFICATION AU CHARGEMENT
var isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
if (!isAuthenticated) {
    Swal.fire({
        title: 'Session expirée',
        text: 'Veuillez vous reconnecter pour continuer.',
        icon: 'warning',
        confirmButtonText: 'Se reconnecter',
        allowOutsideClick: false
    }).then(() => {
        window.location.href = '{{ route("login") }}';
    });
}

// 🔥 FONCTION DE VÉRIFICATION D'AUTHENTIFICATION
function checkAuth() {
    var isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
    if (!isAuthenticated) {
        Swal.fire({
            title: 'Session expirée',
            text: 'Veuillez vous reconnecter pour continuer.',
            icon: 'warning',
            confirmButtonText: 'Se reconnecter',
            allowOutsideClick: false
        }).then(() => {
            window.location.href = '{{ route("login") }}';
        });
        return false;
    }
    return true;
}

// 🔥 FONCTION DE GESTION DES ERREURS AJAX
function handleAjaxError(xhr) {
    if (xhr.status === 401 || xhr.status === 419) {
        Swal.fire({
            title: 'Session expirée',
            text: 'Veuillez vous reconnecter pour continuer.',
            icon: 'warning',
            confirmButtonText: 'Se reconnecter',
            allowOutsideClick: false
        }).then(() => {
            window.location.href = '{{ route("login") }}';
        });
        return true;
    }
    if (xhr.status === 403) {
        Swal.fire({
            title: 'Accès refusé',
            text: 'Vous n\'avez pas les droits nécessaires.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return true;
    }
    return false;
}

// Gestion du dropdown
function toggleDropdown(btn, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
        if (menu !== btn.nextElementSibling) {
            menu.classList.remove('show');
        }
    });

    var menu = btn.nextElementSibling;
    if (!menu) return;

    if (menu.classList.contains('show')) {
        menu.classList.remove('show');
    } else {
        var rect = btn.getBoundingClientRect();
        menu.style.position = 'fixed';
        menu.style.top = (rect.bottom + 5) + 'px';
        menu.style.left = (rect.right - 170) + 'px';
        menu.classList.add('show');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-container')) {
        document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Select All
document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        if(cb !== this) cb.checked = this.checked;
    });
    updateButtons();
});

function updateButtons() {
    var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
    var selectedCount = selectedCheckboxes.length;
    var addBtn = document.getElementById('add_button');
    var deleteBtn = document.getElementById('delete_button');
    var editBtn = document.getElementById('edit_button');
    var disabledBtn = document.getElementById('disabled_button');

    if (addBtn) {
        if (selectedCount > 0) addBtn.classList.add('disabled');
        else addBtn.classList.remove('disabled');
    }
    if (selectedCount === 1) {
        if (deleteBtn) deleteBtn.classList.remove('disabled');
        if (editBtn) editBtn.classList.remove('disabled');
        if (disabledBtn) disabledBtn.classList.remove('disabled');
    } else if (selectedCount > 1) {
        if (editBtn) editBtn.classList.add('disabled');
        if (disabledBtn) disabledBtn.classList.add('disabled');
        if (deleteBtn) deleteBtn.classList.remove('disabled');
    } else {
        if (deleteBtn) deleteBtn.classList.add('disabled');
        if (editBtn) editBtn.classList.add('disabled');
        if (disabledBtn) disabledBtn.classList.add('disabled');
    }
}

document.querySelectorAll('.row-checkbox').forEach(cb => {
    cb.addEventListener('change', updateButtons);
});
updateButtons();

// 🔥 BOUTON EDIT - AVEC VÉRIFICATION
document.getElementById('edit_button')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!checkAuth()) return;

    var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
    if (selectedCheckbox) {
        var siteId = selectedCheckbox.value;
        window.location.href = "{{ route('sites.edit', ':id') }}".replace(':id', siteId);
    } else {
        Swal.fire('Information', 'Veuillez sélectionner un site.', 'info');
    }
});

// 🔥 BOUTON DELETE (suppression multiple) - AVEC VÉRIFICATION
document.getElementById('delete_button')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!checkAuth()) return;

    var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
    if (selectedCheckboxes.length === 0) {
        Swal.fire('Information', 'Veuillez sélectionner au moins un site.', 'info');
        return;
    }

    Swal.fire({
        title: 'Confirmation',
        text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' site(s) ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            var deleted = 0;
            var total = selectedCheckboxes.length;
            var hasError = false;

            selectedCheckboxes.forEach(function(cb) {
                var siteId = cb.value;
                var deleteUrl = "{{ route('sites.destroy', ':id') }}".replace(':id', siteId);

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    success: function() {
                        deleted++;
                        if (deleted === total && !hasError) {
                            Swal.fire({
                                title: 'Succès',
                                text: 'Tous les sites ont été supprimés.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        if (handleAjaxError(xhr)) {
                            hasError = true;
                            deleted++;
                            return;
                        }
                        hasError = true;
                        deleted++;
                        if (deleted === total) {
                            Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                        }
                    }
                });
            });
        }
    });
});

// 🔥 BOUTON DISABLE (désactivation) - AVEC VÉRIFICATION
let currentDisableId = null;
document.getElementById('disabled_button')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!checkAuth()) return;

    var selectedCheckbox = document.querySelector('.row-checkbox:checked:not(#selectAllCheckbox)');
    if (!selectedCheckbox) {
        Swal.fire('Information', 'Veuillez sélectionner un site.', 'info');
        return;
    }
    if (document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)').length > 1) {
        Swal.fire('Information', 'Veuillez sélectionner un seul site.', 'info');
        return;
    }
    currentDisableId = selectedCheckbox.value;
    document.getElementById('disableModal').classList.remove('hidden');
});

function closeDisableModal() {
    document.getElementById('disableModal').classList.add('hidden');
    document.getElementById('disableReason').value = '';
    currentDisableId = null;
}

// 🔥 CONFIRMATION DÉSACTIVATION - AVEC VÉRIFICATION
document.getElementById('confirmDisable')?.addEventListener('click', function() {
    if (!checkAuth()) return;

    var reason = document.getElementById('disableReason').value.trim();
    if (!reason) {
        Swal.fire('Erreur', 'Veuillez entrer un motif.', 'error');
        return;
    }

    var disableUrl = "{{ route('sites.disabled', ':id') }}".replace(':id', currentDisableId);

    $.ajax({
        url: disableUrl,
        type: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            reason: reason
        },
        success: function(response) {
            Swal.fire({
                title: 'Succès',
                text: 'Statut du site modifié avec succès.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            if (handleAjaxError(xhr)) return;

            var errorMsg = 'Une erreur est survenue lors de la désactivation.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Erreur', errorMsg, 'error');
        }
    });
    closeDisableModal();
});

// 🔥 FONCTION confirmDelete (suppression individuelle) - AVEC VÉRIFICATION
function confirmDelete(siteId) {
    if (!checkAuth()) return;

    Swal.fire({
        title: 'Confirmation',
        text: 'Êtes-vous sûr de vouloir supprimer ce site ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            var deleteUrl = "{{ route('sites.destroy', ':id') }}".replace(':id', siteId);

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                success: function(response) {
                    Swal.fire({
                        title: 'Succès',
                        text: 'Site supprimé avec succès.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    if (handleAjaxError(xhr)) return;

                    var errorMsg = 'Une erreur est survenue lors de la suppression.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Erreur', errorMsg, 'error');
                }
            });
        }
    });
}

// 🔥 FONCTION confirmDisable (désactivation individuelle)
function confirmDisable(siteId) {
    if (!checkAuth()) return;
    currentDisableId = siteId;
    document.getElementById('disableModal').classList.remove('hidden');
}

// Impression QR Code
function printQRCode(siteId, siteName, qrCodeHtml) {
    var printWindow = window.open('', '_blank', 'width=550,height=650');
    printWindow.document.write(`
        <html>
        <head>
            <title>QR Code - ${escapeHtml(siteName)}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin: 0;
                    padding: 30px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    background: white;
                }
                .qr-container {
                    margin: 0 auto;
                    text-align: center;
                    padding: 20px;
                    border: 2px solid #e5e7eb;
                    border-radius: 10px;
                    background: white;
                }
                .qr-title {
                    font-size: 20px;
                    margin-bottom: 25px;
                    color: #1f2937;
                    font-weight: 600;
                }
                .qr-code-wrapper {
                    margin: 20px 0;
                    display: flex;
                    justify-content: center;
                }
                .qr-code-wrapper svg,
                .qr-code-wrapper img {
                    max-width: 250px;
                    height: auto;
                }
                .site-info {
                    margin-top: 20px;
                    padding-top: 10px;
                    border-top: 1px solid #e5e7eb;
                    font-size: 12px;
                    color: #6b7280;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .qr-container {
                        border: none;
                        padding: 10px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="qr-container">
                <h2 class="qr-title">QR Code : ${escapeHtml(siteName)}</h2>
                <div class="qr-code-wrapper">
                    ${qrCodeHtml}
                </div>
                <div class="site-info">
                    Site ID: ${siteId} | Généré le ${new Date().toLocaleDateString()}
                </div>
            </div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>
</x-app-layout>
