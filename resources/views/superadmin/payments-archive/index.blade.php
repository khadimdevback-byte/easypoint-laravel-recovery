{{-- resources/views/superadmin/payments-archive/index.blade.php --}}

<x-app-layout>
    {{-- ===================================================== --}}
    {{-- BARRE D'ACTIONS — collée sous la navbar               --}}
    {{-- ===================================================== --}}
    <div class="action-bar">
        <div class="action-bar-inner">
            <a href="#" id="refresh_button" class="action-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i>
                <span>{{ __('Refresh') }}</span>
            </a>
            {{-- <a href="#" id="export_excel_button" class="action-btn">
                <i class="fas fa-file-excel"></i>
                <span>{{ __('Export Excel') }}</span>
            </a> --}}
            {{-- <a href="#" id="export_csv_button" class="action-btn">
                <i class="fas fa-file-alt"></i>
                <span>{{ __('Export CSV') }}</span>
            </a> --}}
            <a href="#" id="delete_button" class="action-btn disabled">
                <i class="fas fa-trash"></i>
                <span>{{ __('Delete') }}</span>
            </a>
            <a href="#" id="filter_button" class="action-btn">
                <i class="fas fa-filter"></i>
                <span>{{ __('Filter') }}</span>
            </a>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CONTENU PRINCIPAL                                     --}}
    {{-- ===================================================== --}}
    <div class="page-content">
        <div class="content-card">
            <div class="content-header">
                <h1 class="page-title">{{ __('Payment Archives') }}</h1>
                <div class="export-group">
                    <button id="export-excel" class="export-btn export-btn--excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button id="export-csv" class="export-btn export-btn--csv">
                        <i class="fas fa-file-alt"></i> Export CSV
                    </button>
                    <button id="export-pdf" class="export-btn export-btn--pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div id="session-success-alert-div" class="alert alert--success">
                    {{ session('success') }}
                    <button onclick="document.getElementById('session-success-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            @if (session('error'))
                <div id="session-error-alert-div" class="alert alert--error">
                    {{ session('error') }}
                    <button onclick="document.getElementById('session-error-alert-div').style.display='none'" class="alert-close">&#10005;</button>
                </div>
            @endif

            {{-- Statistiques --}}
            <div class="stats-grid">
                <div class="stat-card stat-card--primary">
                    <div class="stat-icon"><i class="fas fa-archive"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Total Archives') }}</div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
                <div class="stat-card stat-card--success">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Total Amount') }}</div>
                        <div class="stat-value">{{ number_format($stats['total_amount'], 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
                <div class="stat-card stat-card--info">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Approved') }}</div>
                        <div class="stat-value">{{ $stats['approved'] }}</div>
                    </div>
                </div>
                <div class="stat-card stat-card--warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('Pending') }}</div>
                        <div class="stat-value">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="filters-container" id="filtersContainer" style="display: none;">
                <form action="{{ route('payments-archive.index') }}" method="GET" class="filters-form">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label class="filter-label">{{ __('Company') }}</label>
                            <input type="text" name="company_name" class="filter-input"
                                   placeholder="{{ __('Search company...') }}"
                                   value="{{ request('company_name') }}">
                        </div>
                        <div class="filter-item">
                            <label class="filter-label">{{ __('Status') }}</label>
                            <select name="status" class="filter-select">
                                <option value="">{{ __('All status') }}</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Annulé</option>
                                <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Refusé</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label class="filter-label">{{ __('Date from') }}</label>
                            <input type="date" name="date_from" class="filter-input"
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="filter-item">
                            <label class="filter-label">{{ __('Date to') }}</label>
                            <input type="date" name="date_to" class="filter-input"
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="filter-item filter-actions">
                            <button type="submit" class="filter-btn filter-btn--apply">
                                <i class="fas fa-search"></i> {{ __('Apply') }}
                            </button>
                            <a href="{{ route('payments-archive.index') }}" class="filter-btn filter-btn--reset">
                                <i class="fas fa-undo"></i> {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tableau --}}
            <div class="table-wrapper">
                <table class="data-table" id="export-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAllCheckbox" class="row-checkbox">
                            </th>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Payment Date') }}</th>
                            <th>{{ __('Archived At') }}</th>
                            <th style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="archives-table-body">
                        @forelse($archives as $archive)
                            <tr id="archive-row-{{ $archive->id }}" data-archive-id="{{ $archive->id }}">
                                <td>
                                    <input type="checkbox" class="row-checkbox"
                                           name="archive_id" value="{{ $archive->id }}"
                                           data-archive-id="{{ $archive->id }}">
                                </td>
                                <td class="text-muted">#{{ $archive->id }}</td>
                                <td>
                                    <div class="company-cell">
                                        <div class="company-logo-placeholder" style="background: {{ $archive->company_id ? '#2e7d64' : '#dc2626' }}">
                                            {{ strtoupper(substr($archive->company_name ?? 'D', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="company-name">{{ $archive->company_name ?? 'N/A' }}</div>
                                            <div class="company-email">{{ $archive->company_email ?? 'N/A' }}</div>
                                            @if($archive->company_id)
                                                <span class="badge badge--info">ID: {{ $archive->company_id }}</span>
                                            @else
                                                <span class="badge badge--danger">{{ __('Deleted') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="plan-info">
                                        <span class="plan-name">{{ $archive->plan_name ?? 'N/A' }}</span>
                                        @if($archive->duration_months)
                                            <span class="plan-duration">{{ $archive->duration_months }} {{ __('months') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-cell">
                                        <span class="amount-value">{{ number_format($archive->amount, 0, ',', ' ') }}</span>
                                        <span class="amount-currency">{{ $archive->currency ?? 'FCFA' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="payment-method">
                                        <i class="fas {{ $archive->payment_method == 'mobile_money' ? 'fa-mobile-alt' : 'fa-credit-card' }}"></i>
                                        {{ $archive->payment_method ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($archive->status === 'approved')
                                        <span class="status-badge status-badge--active">
                                            <i class="fas fa-check-circle"></i> Approuvé
                                        </span>
                                    @elseif($archive->status === 'pending')
                                        <span class="status-badge status-badge--pending">
                                            <i class="fas fa-clock"></i> En attente
                                        </span>
                                    @elseif($archive->status === 'canceled')
                                        <span class="status-badge status-badge--canceled">
                                            <i class="fas fa-times-circle"></i> Annulé
                                        </span>
                                    @else
                                        <span class="status-badge status-badge--declined">
                                            <i class="fas fa-exclamation-circle"></i> {{ $archive->status }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-cell">
                                        {{ $archive->payment_date?->format('d/m/Y') ?? 'N/A' }}
                                        <span class="date-time">{{ $archive->payment_date?->format('H:i') ?? '' }}</span>
                                    </span>
                                </td>
                                <td>
                                    <span class="date-cell text-muted">
                                        {{ $archive->archived_at?->format('d/m/Y') ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown-container">
                                        <button class="dots-btn" onclick="toggleDropdown(this, event)">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu-custom">
                                            {{-- <a href="{{ route('payments-archive.show', $archive) }}" class="dropdown-item-custom">
                                                <i class="fas fa-eye"></i> {{ __('View') }}
                                            </a> --}}
                                            <button onclick="viewFullData({{ $archive->id }})" class="dropdown-item-custom">
                                                <i class="fas fa-code"></i> {{ __('View JSON') }}
                                            </button>
                                            {{-- @if($archive->company_id)
                                                <a href="{{ route('companies.show', $archive->company_id) }}" class="dropdown-item-custom">
                                                    <i class="fas fa-building"></i> {{ __('View Company') }}
                                                </a>
                                            @endif --}}
                                            <button onclick="confirmDeleteArchive({{ $archive->id }})" class="dropdown-item-custom" style="color: #dc2626;">
                                                <i class="fas fa-trash-alt"></i> {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="10" class="empty-row">
                                    <i class="fas fa-archive" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #d1d5db;"></i>
                                    {{ __('No archived payments found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper" id="pagination-wrapper">
                {{ $archives->links() }}
            </div>
        </div>
    </div>

    {{-- Modal pour voir les données JSON --}}
    <div id="jsonModal" class="modal-overlay hidden">
        <div class="modal-container modal-container--large">
            <div class="modal-card">
                <div class="modal-header">
                    <h3><i class="fas fa-code"></i> {{ __('Full Data') }}</h3>
                    <button type="button" class="modal-close" onclick="closeJsonModal()">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="jsonContent" class="json-preview"></pre>
                </div>
                <div class="modal-footer">
                    <button onclick="copyJson()" class="modal-btn-confirm">
                        <i class="fas fa-copy"></i> {{ __('Copy') }}
                    </button>
                    <button onclick="closeJsonModal()" class="modal-btn-cancel">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ============================================ */
        /* STYLES MODERNES POUR LA PAGE ARCHIVES        */
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

        /* Barre d'actions */
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
            padding: 0 24px;
            height: 46px;
            gap: 4px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: rgba(255,255,255,0.92);
            font-size: 0.82rem;
            font-weight: 500;
            padding: 6px 16px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.18s;
            white-space: nowrap;
            cursor: pointer;
            border: none;
            background: none;
        }

        .action-btn:hover { background: rgba(255,255,255,0.15); color: #fff; }
        .action-btn i { font-size: 0.78rem; opacity: 0.85; }
        .action-btn + .action-btn {
            border-left: 1px solid rgba(255,255,255,0.18);
            border-radius: 0;
            padding-left: 18px;
        }
        .action-btn.disabled { opacity: 0.5; pointer-events: none; }

        .page-content { padding-top: 46px; }
        .content-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--gl);
            padding: 24px;
            overflow: visible;
            position: relative;
            margin: 0 24px;
        }

        .content-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .page-title { font-size: 1.35rem; font-weight: 700; color: #1f2937; margin: 0; }

        /* Export buttons */
        .export-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            transition: filter 0.18s, transform 0.1s;
            color: #fff;
        }
        .export-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .export-btn--excel { background: #16a34a; }
        .export-btn--csv   { background: #2563eb; }
        .export-btn--pdf   { background: #dc2626; }

        /* Alertes */
        .alert {
            position: relative;
            padding: 12px 44px 12px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        .alert--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert--error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-close {
            position: absolute;
            top: 10px;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            opacity: 0.6;
        }

        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            transition: all 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .stat-card--primary .stat-icon { background: #e8f5f1; color: #2e7d64; }
        .stat-card--success .stat-icon { background: #dcfce7; color: #16a34a; }
        .stat-card--info .stat-icon { background: #dbeafe; color: #2563eb; }
        .stat-card--warning .stat-icon { background: #fef3c7; color: #d97706; }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .stat-content { flex: 1; }
        .stat-label { font-size: 0.75rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 600; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1f2937; }

        /* Filtres */
        .filters-container {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .filters-form { width: 100%; }
        .filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            align-items: end;
        }
        .filter-item { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; }
        .filter-input, .filter-select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.85rem;
            background: #fff;
            transition: border-color 0.15s;
        }
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #2e7d64;
            box-shadow: 0 0 0 3px rgba(46,125,100,0.1);
        }
        .filter-actions {
            flex-direction: row;
            gap: 8px;
            align-items: center;
        }
        .filter-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .filter-btn--apply { background: #2e7d64; color: #fff; }
        .filter-btn--apply:hover { background: #1a5a48; }
        .filter-btn--reset { background: #e5e7eb; color: #374151; }
        .filter-btn--reset:hover { background: #d1d5db; }

        /* Tableau */
        .table-wrapper {
            overflow-x: auto;
            position: relative;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.83rem;
            color: #374151;
        }
        .data-table thead tr {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        .data-table th {
            padding: 11px 16px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            text-align: left;
            white-space: nowrap;
        }
        .data-table td {
            padding: 10px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: #f9fafb; }

        .row-checkbox {
            width: 15px;
            height: 15px;
            accent-color: #2e7d64;
            cursor: pointer;
        }

        .text-muted { color: #6b7280; }

        /* Company cell */
        .company-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .company-logo-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
        }
        .company-name { font-weight: 600; color: #1f2937; }
        .company-email { font-size: 0.75rem; color: #6b7280; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
        }
        .badge--info { background: #dbeafe; color: #1e40af; }
        .badge--danger { background: #fee2e2; color: #991b1b; }
        .badge--success { background: #dcfce7; color: #166534; }

        /* Plan info */
        .plan-info { display: flex; flex-direction: column; gap: 2px; }
        .plan-name { font-weight: 500; }
        .plan-duration { font-size: 0.7rem; color: #6b7280; }

        /* Amount */
        .amount-cell { display: flex; flex-direction: column; }
        .amount-value { font-weight: 600; color: #1f2937; }
        .amount-currency { font-size: 0.65rem; color: #6b7280; }

        /* Payment method */
        .payment-method {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: #4b5563;
        }

        /* Status badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .status-badge--active { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .status-badge--pending { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .status-badge--canceled { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .status-badge--declined { background: #f3f4f6; color: #6b7280; border-color: #e5e7eb; }

        /* Date cell */
        .date-cell { display: flex; flex-direction: column; }
        .date-time { font-size: 0.65rem; color: #9ca3af; }

        /* Dropdown */
        .dropdown-container { position: relative; display: inline-block; }
        .dropdown-menu-custom {
            position: fixed;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            min-width: 180px;
            z-index: 9999;
            overflow: hidden;
            display: none;
        }
        .dropdown-menu-custom.show { display: block; }
        .dots-btn {
            background: none;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 10px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s;
        }
        .dots-btn:hover { background: #f3f4f6; color: #111; }
        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 16px;
            text-align: left;
            background: none;
            border: none;
            font-size: 0.8rem;
            color: #374151;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .dropdown-item-custom:hover { background: #f3f4f6; color: #2e7d64; }
        .dropdown-item-custom.danger { color: #dc2626; }
        .dropdown-item-custom.danger:hover { background: #fee2e2; }

        .empty-row {
            text-align: center;
            padding: 60px 16px;
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .pagination-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.hidden { display: none; }
        .modal-container { width: 100%; max-width: 500px; }
        .modal-container--large { max-width: 800px; }
        .modal-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            flex-shrink: 0;
        }
        .modal-header h3 { font-size: 0.95rem; font-weight: 700; color: #1f2937; margin: 0; }
        .modal-close {
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6b7280;
            transition: all 0.15s;
        }
        .modal-close:hover { background: #e5e7eb; }
        .modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid #f3f4f6;
            flex-shrink: 0;
        }
        .modal-btn-cancel {
            padding: 8px 16px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.15s;
        }
        .modal-btn-cancel:hover { background: #e5e7eb; }
        .modal-btn-confirm {
            padding: 8px 16px;
            background: #2e7d64;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            color: white;
            cursor: pointer;
            transition: all 0.15s;
        }
        .modal-btn-confirm:hover { background: #1a5a48; }

        /* JSON Preview */
        .json-preview {
            background: #1f2937;
            color: #e5e7eb;
            padding: 16px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Animation de suppression */
        .row-deleting {
            animation: rowFadeOut 0.4s ease forwards;
        }

        @keyframes rowFadeOut {
            0% { opacity: 1; transform: scale(1); max-height: 100px; padding: 10px 0; }
            100% { opacity: 0; transform: scale(0.95); max-height: 0; padding: 0; margin: 0; overflow: hidden; }
        }

        @media (max-width: 1280px) { .action-bar { left: 0; } }
        @media (max-width: 768px) {
            .page-content { padding: 46px 0 0 0; }
            .content-card { margin: 0 16px; padding: 16px; }
            .action-bar-inner { padding: 0 14px; gap: 0; overflow-x: auto; }
            .content-header { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .filter-group { grid-template-columns: 1fr; }
            .modal-container--large { max-width: 100%; margin: 10px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // =====================================================
        // DROPDOWN
        // =====================================================
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
                menu.style.left = (rect.right - 180) + 'px';
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

        // =====================================================
        // SELECT ALL & UPDATE BUTTONS
        // =====================================================
        document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                if(cb !== this) cb.checked = this.checked;
            });
            updateButtons();
        });

        function updateButtons() {
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            var selectedCount = selectedCheckboxes.length;

            var deleteBtn = document.getElementById('delete_button');

            if (selectedCount > 0) {
                if (deleteBtn) deleteBtn.classList.remove('disabled');
            } else {
                if (deleteBtn) deleteBtn.classList.add('disabled');
            }
        }

        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.addEventListener('change', updateButtons);
        });
        updateButtons();

        // =====================================================
        // FILTER TOGGLE
        // =====================================================
        document.getElementById('filter_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var container = document.getElementById('filtersContainer');
            if (container.style.display === 'none') {
                container.style.display = 'block';
                this.querySelector('span').textContent = '{{ __('Hide Filters') }}';
            } else {
                container.style.display = 'none';
                this.querySelector('span').textContent = '{{ __('Filter') }}';
            }
        });

        // =====================================================
        // VIEW FULL JSON DATA
        // =====================================================
        function viewFullData(archiveId) {
            var row = document.getElementById('archive-row-' + archiveId);
            if (!row) return;

            // Récupérer les données depuis l'élément ou via AJAX
            var archiveData = {
                id: archiveId,
                company_name: row.querySelector('.company-name')?.textContent || 'N/A',
                company_email: row.querySelector('.company-email')?.textContent || 'N/A',
                plan_name: row.querySelector('.plan-name')?.textContent || 'N/A',
                amount: row.querySelector('.amount-value')?.textContent || '0',
                status: row.querySelector('.status-badge')?.textContent?.trim() || 'N/A',
                // On peut aussi faire un appel AJAX pour récupérer toutes les données
            };

            // Simuler des données complètes (idéalement, faire un appel AJAX)
            var fullData = {
                archive: archiveData,
                message: "Pour voir les données complètes, faites un appel AJAX vers /payments-archive/" + archiveId + "/json"
            };

            document.getElementById('jsonContent').textContent = JSON.stringify(fullData, null, 2);
            document.getElementById('jsonModal').classList.remove('hidden');
        }

        function closeJsonModal() {
            document.getElementById('jsonModal').classList.add('hidden');
        }

        function copyJson() {
            var content = document.getElementById('jsonContent').textContent;
            navigator.clipboard?.writeText(content).then(() => {
                Swal.fire('Succès', 'Données copiées dans le presse-papier.', 'success');
            }).catch(() => {
                // Fallback
                var textarea = document.createElement('textarea');
                textarea.value = content;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                Swal.fire('Succès', 'Données copiées dans le presse-papier.', 'success');
            });
        }

        // Fermer la modal avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeJsonModal();
            }
        });

        // Fermer la modal en cliquant à l'extérieur
        document.getElementById('jsonModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeJsonModal();
            }
        });

        // =====================================================
        // SUPPRESSION D'UNE LIGNE DU DOM
        // =====================================================
        function removeArchiveRow(archiveId) {
            var row = document.getElementById('archive-row-' + archiveId);
            if (row) {
                row.classList.add('row-deleting');
                setTimeout(function() {
                    row.remove();
                    var remainingRows = document.querySelectorAll('#archives-table-body tr:not(#empty-row)');
                    if (remainingRows.length === 0) {
                        var tbody = document.getElementById('archives-table-body');
                        var emptyRow = document.getElementById('empty-row');
                        if (!emptyRow) {
                            var newEmptyRow = document.createElement('tr');
                            newEmptyRow.id = 'empty-row';
                            newEmptyRow.innerHTML = '<td colspan="10" class="empty-row"><i class="fas fa-archive" style="font-size: 2rem; display: block; margin-bottom: 10px; color: #d1d5db;"></i>{{ __("No archived payments found.") }}</td>';
                            tbody.appendChild(newEmptyRow);
                        } else {
                            emptyRow.style.display = '';
                        }
                    }
                    updateButtons();
                }, 400);
            }
        }

        // =====================================================
        // SUPPRESSION INDIVIDUELLE (Dropdown)
        // =====================================================
        function confirmDeleteArchive(archiveId) {
            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer définitivement cette archive ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    var deleteUrl = "{{ route('payments-archive.destroy', ':id') }}".replace(':id', archiveId);

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': token },
                        data: { _token: token },
                        success: function(response) {
                            removeArchiveRow(archiveId);
                            Swal.fire('Succès', response.message || 'L\'archive a été supprimée.', 'success');
                        },
                        error: function(xhr) {
                            if (xhr.status === 404) {
                                removeArchiveRow(archiveId);
                                Swal.fire('Information', 'Cette archive a déjà été supprimée.', 'info');
                            } else {
                                Swal.fire('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                            }
                        }
                    });
                }
            });
        }

        // =====================================================
        // SUPPRESSION MULTIPLE (Barre d'actions)
        // =====================================================
        document.getElementById('delete_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked:not(#selectAllCheckbox)');
            if (selectedCheckboxes.length === 0) {
                Swal.fire('Information', 'Veuillez sélectionner au moins une archive.', 'info');
                return;
            }

            var archiveIds = [];
            selectedCheckboxes.forEach(cb => { archiveIds.push(cb.value); });

            Swal.fire({
                title: 'Confirmation',
                text: 'Êtes-vous sûr de vouloir supprimer ' + selectedCheckboxes.length + ' archive(s) définitivement ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    var completed = 0;
                    var total = archiveIds.length;
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    archiveIds.forEach(function(archiveId) {
                        var deleteUrl = "{{ route('payments-archive.destroy', ':id') }}".replace(':id', archiveId);

                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': token },
                            data: { _token: token },
                            success: function() {
                                removeArchiveRow(archiveId);
                                completed++;
                                if (completed === total) {
                                    Swal.fire('Succès', 'Toutes les archives ont été supprimées.', 'success');
                                }
                            },
                            error: function(xhr) {
                                completed++;
                                if (xhr.status === 404) {
                                    removeArchiveRow(archiveId);
                                }
                                if (completed === total) {
                                    Swal.fire('Information', 'Suppression terminée.', 'info');
                                }
                            }
                        });
                    });
                }
            });
        });

        // =====================================================
        // EXPORT
        // =====================================================
        document.getElementById('export-excel')?.addEventListener('click', function() {
            window.location.href = "{{ route('payments-archive.export') }}?format=excel";
        });

        document.getElementById('export-csv')?.addEventListener('click', function() {
            window.location.href = "{{ route('payments-archive.export') }}?format=csv";
        });

        document.getElementById('export-pdf')?.addEventListener('click', function() {
            window.location.href = "{{ route('payments-archive.export') }}?format=pdf";
        });

        // Pour les boutons de la barre d'actions
        document.getElementById('export_excel_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('payments-archive.export') }}?format=excel";
        });

        document.getElementById('export_csv_button')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('payments-archive.export') }}?format=csv";
        });
    </script>
</x-app-layout>
