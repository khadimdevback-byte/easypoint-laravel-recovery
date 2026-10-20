<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('customer.dashboard') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('customer.dashboard') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-house-user"></i>
        </div>
        <span class="ml-1 font-semibold">{{ __('Mon espace client') }}</span>
    </a>
</li>
