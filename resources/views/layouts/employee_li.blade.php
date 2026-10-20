<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('dashboard', 'employee.dashboard') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('dashboard') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Tableau de bord') }}</span>
    </a>
</li>

@php
    $operationsOpen = Request::routeIs('sites.index', 'attendances.index');
@endphp

<li class="hs-accordion relative w-full" id="employee-operations-menu">
    <a href="#" class="py-2 my-0 mx-4 flex items-center px-4 transition-colors group w-full" onclick="toggleMenu(event, 'employee-operations-submenu')" aria-expanded="{{ $operationsOpen ? 'true' : 'false' }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-gear"></i>
        </div>
        <span class="ml-1 capitalize font-semibold">{{ __('Opérations') }}</span>
        <i class="fa-solid fa-chevron-down ml-auto text-xs transition-transform duration-200"></i>
    </a>
    <ul id="employee-operations-submenu" class="sub-menu {{ $operationsOpen ? '' : 'hidden' }} mt-1 space-y-1 transition-all duration-300 overflow-visible">
        <li class="flex items-center space-x-2 py-1 pl-4">
            <i class="fa-solid fa-map-location-dot w-5 text-sm"></i>
            <a class="{{ Request::routeIs('sites.index') ? 'text-primary font-bold' : '' }} text-sm hover:text-primary transition-colors" href="{{ route('sites.index') }}">{{ __('Lieux de travail') }}</a>
        </li>
        <li class="flex items-center space-x-2 py-1 pl-4">
            <i class="fa-solid fa-calendar-check w-5 text-sm"></i>
            <a class="{{ Request::routeIs('attendances.index') ? 'text-primary font-bold' : '' }} text-sm hover:text-primary transition-colors" href="{{ route('attendances.index') }}">{{ __('Présences') }}</a>
        </li>
    </ul>
</li>

<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('company.subscription') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('company.subscription') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-tags"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Mon abonnement') }}</span>
    </a>
</li>
