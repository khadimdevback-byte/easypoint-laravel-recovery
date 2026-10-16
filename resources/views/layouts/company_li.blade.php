@php
    $activeFeatures = [];
    $user = auth()->user();

    // 1. Vérification stricte du Super Admin global
    $isSuperAdmin = auth()->check() && $user->user_type === 'super_admin';

    // 2. Récupération des fonctionnalités (uniquement si ce n'est pas le Super Admin)
    if (!$isSuperAdmin && auth()->check() && $user->company_id) {
        $freshUser = $user->fresh(['company.activeSubscription.plan.features']);
        if ($freshUser && $freshUser->company && $freshUser->company->activeSubscription && $freshUser->company->activeSubscription->plan) {
            $activeFeatures = $freshUser->company->activeSubscription->plan->features->pluck('slug')->toArray();
        }
    }
@endphp

{{-- Tableau de bord --}}
@if($isSuperAdmin || in_array('dashboard', $activeFeatures))
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('dashboard*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ is_controller() ? route('dashboard.controller') : route('dashboard') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft  font-semibold">{{ __('Tableau de bord') }}</span>
    </a>
</li>
@endif

{{-- ============================================= --}}
{{-- GESTION DES UTILISATEURS (Sous-menu) --}}
{{-- ============================================= --}}
@if($isSuperAdmin || is_admin() || is_manager())
<li class="hs-accordion relative w-full" id="users-menu">
    <a href="#" class="py-2 my-0 mx-4 flex items-center px-4 transition-colors group w-full" onclick="toggleMenu(event, 'users-submenu')">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-users-gear"></i>
        </div>
        <span class="ml-1 capitalize font-semibold">{{ __('Utilisateurs') }}</span>
        <i class="fa-solid fa-chevron-down ml-auto text-xs transition-transform duration-200"></i>
    </a>
    <ul id="users-submenu" class="sub-menu hidden mt-1 space-y-1 transition-all duration-300 overflow-visible">

        @if($isSuperAdmin || auth()->user()->hasFeature('controllers_management'))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-user-group w-5 text-sm"></i>
                <a href="{{ route('users.index', 'controller') }}" class="text-sm hover:text-primary transition-colors">{{ __('Contrôleurs') }}</a>
            </li>
        @endif

        @if($isSuperAdmin || auth()->user()->hasFeature('employees_management'))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-users w-5 text-sm"></i>
                <a href="{{ route('users.index', 'user') }}" class="text-sm hover:text-primary transition-colors">{{ __('Employé(e)s') }}</a>
            </li>
        @endif

        {{-- @if($isSuperAdmin || auth()->user()->hasFeature('customers_management'))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-circle-user w-5 text-sm"></i>
                <a href="{{ route('users.index', 'customer') }}" class="text-sm hover:text-primary transition-colors">{{ __('Clients') }}</a>
            </li>
        @endif --}}

        @if($isSuperAdmin || (auth()->user()->hasFeature('badges_management') && is_admin()))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-id-badge w-5 text-sm"></i>
                <a href="{{ route('badges.select') }}" class="text-sm hover:text-primary transition-colors">{{ __('Générer badges') }}</a>
            </li>
        @endif

        
    </ul>
</li>
@endif

{{-- ============================================= --}}
{{-- GESTION DES OPÉRATIONS (Sous-menu) --}}
{{-- ============================================= --}}
<li class="hs-accordion relative w-full" id="operations-menu">
    <a href="#" class="py-2 my-0 mx-4 flex items-center px-4 transition-colors group w-full" onclick="toggleMenu(event, 'operations-submenu')">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-gear"></i>
        </div>
        <span class="ml-1 capitalize font-semibold">{{ __('Opérations') }}</span>
        <i class="fa-solid fa-chevron-down ml-auto text-xs transition-transform duration-200"></i>
    </a>
    <ul id="operations-submenu" class="sub-menu hidden mt-1 space-y-1 transition-all duration-300 overflow-visible">

        @if($isSuperAdmin || auth()->user()->hasFeature('workplaces_management'))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-map-location-dot w-5 text-sm"></i>
                <a href="{{ route('sites.index') }}" class="text-sm hover:text-primary transition-colors">{{ __('Lieux de travail') }}</a>
            </li>
        @endif

        @if($isSuperAdmin || auth()->user()->hasFeature('attendances_management'))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-calendar-check w-5 text-sm"></i>
                <a href="{{ route('attendances.index') }}" class="text-sm hover:text-primary transition-colors">{{ __('Présences') }}</a>
            </li>
        @endif

        {{-- Borne de pointage avec parenthèses de priorité corrigées --}}
{{-- Borne de pointage --}}
@if($isSuperAdmin || (auth()->user()->hasFeature('attendancesBorne_management') && in_array($user->user_type, ['admin', 'controller'])))
    @php
        $borneUrl = '#';
        $errorMessage = '';

        if ($user->user_type == 'controller') {
            // Utilise bien $user->id au lieu de auth()->id() pour éviter les conflits de session
            // Et vérifie le nom exact de la table/modèle (SiteControllerUser ou requête DB)
            $siteController = \DB::table('site_controller')->where('user_id', $user->id)->first();

            if ($siteController) {
                $borneUrl = route('borne.index', ['siteId' => $siteController->site_id]);
            } else {
                $errorMessage = 'Aucun site associé';
            }
        } elseif ($user->user_type == 'admin') {
            $firstSite = App\Models\Site::where('company_id', $user->company_id)->first();
            if ($firstSite) {
                $borneUrl = route('borne.index', ['siteId' => $firstSite->id]);
            } else {
                $errorMessage = 'Aucun site dans votre entreprise';
            }
        } elseif ($isSuperAdmin) {
            $firstSite = App\Models\Site::first();
            if ($firstSite) {
                $borneUrl = route('borne.index', ['siteId' => $firstSite->id]);
            } else {
                $errorMessage = 'Aucun site dans la base';
            }
        }
    @endphp

    @if($borneUrl != '#')
        <li class="flex items-center space-x-2 py-1 pl-4">
            <i class="fa-solid fa-qrcode w-5 text-sm"></i>
            {{-- Suppression du target="_top" qui peut casser le clic natif --}}
            <a href="{{ $borneUrl }}"
                onclick="window.location.href = this.href;"
                class="text-sm hover:text-primary transition-colors">
                    {{ __('Borne de pointage') }}
            </a>
        </li>
    @else
        <li class="flex items-center space-x-2 py-1 pl-4 text-red-400 opacity-75">
            <i class="fa-solid fa-qrcode w-5 text-sm"></i>
            <span class="text-sm cursor-not-allowed" title="{{ $errorMessage }}">
                {{ __('Borne indisponible') }}
            </span>
        </li>
    @endif
@endif

        @if($isSuperAdmin || (auth()->user()->hasFeature('balance_sheet') && (is_admin() || is_controller())))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-sm"></i>
                <a href="{{ route('balance.sheet.index') }}" class="text-sm hover:text-primary transition-colors">{{ __('Bilan') }}</a>
            </li>
        @endif

        @if($isSuperAdmin || $user->user_type == 'admin')
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-clock w-5 text-sm"></i>
                <a href="{{ route('admin.missing-departures') }}" class="text-sm hover:text-primary transition-colors">{{ __('Départs manquants') }}</a>
            </li>
        @endif

        @if($isSuperAdmin || (auth()->user()->hasFeature('demandes_management') && is_admin()))
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-calendar w-5 text-sm"></i>
                <a href="{{ route('admin.demandes.index') }}" class="text-sm hover:text-primary transition-colors">{{ __('Demandes') }}</a>
            </li>
        @endif

        @if($isSuperAdmin)
            <li class="flex items-center space-x-2 py-1 pl-4">
                <i class="fa-solid fa-envelope w-5 text-sm"></i>
                <a href="{{ route('admin.contacts.index') }}" class="text-sm hover:text-primary transition-colors">
                    {{ __('Messages contact') }}
                    @php $unreadCount = \App\Models\Contact::where('is_read', false)->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="ml-1 bg-red-500 text-white text-[9px] rounded-full px-1.5 py-0.5">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>
        @endif
    </ul>
</li>

{{-- Mon abonnement (Masqué pour le Super Admin global qui n'a pas d'abonnement propre) --}}
@if(!$isSuperAdmin)
<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('company.subscription') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('company.subscription') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-tags"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft  font-semibold">{{ __('Mon abonnement') }}</span>
    </a>
</li>
@endif
