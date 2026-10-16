<li class="mt-0.5 w-full">
    <a class="{{ Request::is('dashboard*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2  ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('employee.dashboard') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span
            class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold ">{{ __('Dashboard') }}</span>
    </a>
</li>

<li class="mt-0.5 w-full">
    <a class="{{ Request::is('bilan*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2  ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('employee.bilan') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span
            class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold ">{{ __('Présence') }}</span>
    </a>
</li>


<li class="mt-0.5 w-full">
    <a class="{{ Request::is('planning*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2  ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('employee.planning_notification') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <span
            class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold ">{{ __('Planning & Notification') }}</span>
    </a>
</li>

<li class="mt-0.5 w-full">
    <a class="{{ Request::is('account*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2  ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('employee.account') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-user"></i>
        </div>
        <span
            class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold ">{{ __('Profil') }}</span>
    </a>
</li>


<li class="mt-0.5 w-full">
    <a class="{{ Request::is('password*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside' : '' }} py-2  ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('employee.password') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-lock"></i>
        </div>
        <span
            class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold ">{{ __('Mot de passe') }}</span>
    </a>
</li>
