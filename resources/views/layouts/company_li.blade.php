@php
    $activeFeatures = [];
    $user = auth()->user();

    // 1. Vérification stricte du Super Admin global
    $isSuperAdmin = auth()->check() && $user->user_type === 'super_admin';
    $isManager = auth()->check() && $user->user_type === 'manager';
    $isController = auth()->check() && $user->user_type === 'controller';

    // 2. Récupération des fonctionnalités (uniquement si ce n'est pas le Super Admin)
    if (!$isSuperAdmin && auth()->check() && $user->company_id) {
        $freshUser = $user->fresh(['company.activeSubscription.plan.features']);
        if ($freshUser && $freshUser->company && $freshUser->company->activeSubscription && $freshUser->company->activeSubscription->plan) {
            $activeFeatures = $freshUser->company->activeSubscription->plan->features->pluck('slug')->toArray();
        }
    }
@endphp

{{-- Tableau de bord --}}
@if($isSuperAdmin || is_admin() || is_manager() || in_array('dashboard', $activeFeatures))
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('dashboard*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ is_controller() ? route('dashboard.controller') : (is_manager() ? route('manager.dashboard') : route('dashboard')) }}">
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
@if($isSuperAdmin || is_admin() || ($isManager && ($user->hasFeature('controllers_management') || $user->hasFeature('employees_management'))))
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

    </ul>
</li>
@endif

{{-- ============================================= --}}
{{-- GESTION DES OPÉRATIONS (Sous-menu) --}}
{{-- ============================================= --}}
@if(!$isManager || $user->hasFeature('workplaces_management') || $user->hasFeature('attendances_management'))
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
@endif

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
