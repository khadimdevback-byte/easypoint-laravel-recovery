<aside id="sidenav"
    class="text-slate-700 ease-nav-brand fixed left-0 block -translate-x-full rounded-2xl border-0 bg-secondary p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0"
    style="top: 0; height: 100vh; z-index: 999; display: flex; flex-direction: column;">

    @php
        $dashboardRoute = match (auth()->user()->user_type) {
            'super_admin' => 'dashboard.superadmin',
            'admin' => 'dashboard',
            'manager' => 'manager.dashboard',
            'controller' => 'dashboard.controller',
            'user' => 'employee.dashboard',
            'customer' => 'customer.dashboard',
        };
    @endphp

    <div class="sidebar-logo-wrapper" style="flex-shrink: 0; position: relative; padding: 16px 12px 12px; display: flex; align-items: center; gap: 8px;">
        <button type="button" class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer text-slate-400 bg-transparent border-0 xl:hidden"
            sidenav-close aria-label="{{ __('Fermer le menu') }}">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
        <a href="{{ route($dashboardRoute) }}" class="sidebar-logo-inner" aria-label="Easy Point">
            <img src="{{ asset('images/logo.png') }}" alt="Easy Point" />
            <span class="sidebar-collapsed-mark" aria-hidden="true">EP</span>
        </a>
        <button type="button" class="sidebar-collapse-btn" data-sidebar-collapse aria-label="{{ __('Réduire le menu') }}" aria-expanded="true" title="{{ __('Réduire le menu') }}">
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>
    </div>

    <div id="sidenavScrollable"
         style="flex: 1; min-height: 0; overflow-y: auto; overflow-x: hidden; overscroll-behavior: contain; padding: 0 16px;">
        <div style="display: flex; flex-direction: column; width: 100%; padding-bottom: 16px;">

            {{-- Menu principal --}}
            <div class="w-full">
                <ul class="flex flex-col pl-0 mb-0">
                    @if (is_super_admin())
                        @include('layouts.super_admin_li')
                    @elseif (is_user())
                        @include('layouts.employee_li')
                    @elseif (is_customer())
                        @include('layouts.customer_li')
                    @else
                        @include('layouts.company_li')
                    @endif

                    @if (!is_user() && !is_customer())
                        <li class="mt-0.5 w-full">
                            <a class="{{ Request::is('notifications*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors"
                                href="{{ route('notifications.index') }}">
                                <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Notifications') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Section General --}}
            <div class="w-full mt-6">
                @if (!is_user() && !is_customer())
                    <h6 class="pl-6 ml-2 font-bold leading-tight text-xs text-slate-400 uppercase">{{ __('General') }}</h6>
                    <ul class="flex flex-col pl-0 mb-0 mt-2">
                        @if (is_super_admin())
                            <li class="mt-0.5 w-full">
                                <a class="{{ Request::is('profile') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors"
                                    href="{{ route('profile.edit') }}">
                                    <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>
                                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Settings') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->user_type != 'super_admin')
                            <li class="mt-0.5 w-full">
                                <a class="{{ Request::is('profile*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors"
                                    @if (Auth::user()->user_type == 'admin') href="{{ route('profile.edit.company') }}"
                                    @else href="{{ route('profile.edit') }}" @endif>
                                    <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Account') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>

            {{-- Logout --}}
            <div class="w-full mt-6 mb-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="sidebar-logout-button inline-block w-full px-6 py-3 font-bold text-center bg-primary text-white uppercase align-middle transition-all ease-in border-0 rounded-lg select-none shadow-soft-md bg-150 bg-x-25 leading-pro hover:shadow-soft-2xl hover:scale-102">
                        <i class="fa-solid fa-right-from-bracket sidebar-logout-icon" aria-hidden="true"></i>
                        <span>{{ (is_user() || is_customer()) ? __('Déconnexion') : __('Logout') }}</span>
                    </button>
                </form>
            </div>

            {{-- Plan actuel --}}
            @if (auth()->check() && in_array(auth()->user()->user_type, ['admin', 'manager']))
                @php
                    $activeSub = auth()->user()->company?->activeSubscription;
                    $planName = $activeSub && $activeSub->plan ? $activeSub->plan->name : 'Aucun Plan';
                    $isFree = $activeSub && $activeSub->plan ? (strtolower($activeSub->plan->slug) === 'free' || strtolower($activeSub->plan->name) === 'free') : true;
                @endphp

                <div class="sidebar-current-plan mt-2 mb-6 p-3 bg-white/60 dark:bg-slate-800/40 rounded-xl border border-gray-200/50 flex flex-col items-center justify-center text-center shadow-sm">
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1.5">{{ __('Plan Actuel') }}</span>

                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $isFree ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                        <i class="fa-solid {{ $isFree ? 'fa-seedling' : 'fa-gem' }} mr-1"></i>
                        <span class="sidebar-plan-name">{{ $planName }}</span>
                    </span>

                    @if ($isFree)
                        <a href="{{ route('subscriptions.upgrade') }}"
                           title="{{ __('Upgrade Plan') }}"
                           class="sidebar-upgrade-link mt-2.5 w-full text-center text-[11px] bg-gradient-to-tl from-purple-700 to-pink-500 text-white font-bold py-1.5 px-3 rounded-lg hover:scale-102 transition-all duration-200 shadow-md">
                            <i class="fa-solid fa-arrow-up-from-bracket mr-1"></i><span>{{ __('Upgrade Plan') }}</span>
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
</aside>

<style>
    /* Scrollbar personnalisée */
    #sidenavScrollable::-webkit-scrollbar {
        width: 4px;
    }
    #sidenavScrollable::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    #sidenavScrollable::-webkit-scrollbar-thumb {
        background: #2e7d64;
        border-radius: 4px;
    }

    /* Dropdown notification */
    .notification-dropdown {
        position: fixed !important;
        top: 70px !important;
        right: 20px !important;
        left: auto !important;
        width: 380px !important;
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        z-index: 99999 !important;
        margin-top: 0 !important;
        overflow: hidden !important;
    }
    .notification-dropdown.hidden {
        display: none !important;
    }
    .notification-dropdown::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 20px;
        width: 16px;
        height: 16px;
        background: white;
        transform: rotate(45deg);
        border-left: 1px solid #e5e7eb;
        border-top: 1px solid #e5e7eb;
    }
    @media (max-width: 768px) {
        .notification-dropdown {
            position: fixed !important;
            top: 60px !important;
            left: 10px !important;
            right: 10px !important;
            width: auto !important;
        }
        .notification-dropdown::before {
            display: none;
        }
    }
    @keyframes dropdownFadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .notification-dropdown:not(.hidden) {
        animation: dropdownFadeIn 0.2s ease-out;
    }

    .sidebar-logo-wrapper {
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .sidebar-logo-inner {
        display: flex !important;
        align-items: center !important;
        background: #ffffff !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        text-decoration: none !important;
    }

    .sidebar-logo-inner img {
        height: 32px !important;
        width: auto !important;
        display: block !important;
    }

    .sidebar-collapsed-mark { display: none; }

    .sidebar-collapse-btn {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.78);
        background: rgba(255, 255, 255, 0.06);
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }

    .sidebar-collapse-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        color: #6ecfb5;
    }

    .sidebar-logout-icon { display: none; }

    @media (max-width: 1280px) {
        .sidebar-collapse-btn { display: none; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var scrollable = document.getElementById('sidenavScrollable');
        if (scrollable) {
            // Forcer le scroll tout en haut au chargement
            scrollable.scrollTop = 0;

            // Empêcher l'item actif de scroller automatiquement vers lui
            var activeItem = scrollable.querySelector('.bg-primary');
            if (activeItem) {
                activeItem.setAttribute('tabindex', '-1');
            }
        }
    });
</script>
