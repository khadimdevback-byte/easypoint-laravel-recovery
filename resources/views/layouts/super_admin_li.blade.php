{{-- ============================================= --}}
{{-- MENU SÉPARÉ : VUE SUPERADMIN                  --}}
{{-- ============================================= --}}

{{-- Tableau de bord Superadmin --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('dashboard*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('dashboard.superadmin') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-chart-simple"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Tableau de bord') }}</span>
    </a>
</li>

{{-- Gestion des Comptes Super-Admins --}}
<li class="mt-0.5 w-full">
    <a class="{{ (Route::currentRouteName() == 'users.index' && request('user_type') == 'super_admin') || Request::is('super-admin/*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('users.index', 'super_admin') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Super admins') }}</span>
    </a>
</li>

{{-- Sociétés --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('companies*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('companies.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-building"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Sociétés') }}</span>
    </a>
</li>

{{-- Fonctions --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('fonctions*') || Request::routeIs('fonctions.*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('fonctions.liste') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-briefcase"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Fonctions') }}</span>
    </a>
</li>

{{-- Bilan Global --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('balance*') || Request::is('superadmin-balance*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('balance.sheet.superadmin.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Bilan Global') }}</span>
    </a>
</li>

{{-- Plans Tarifaires --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('plans*') || Request::is('superadmin/plans*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('plans.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Plans Tarifaires') }}</span>
    </a>
</li>

{{-- Souscriptions des clients --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('superadmin/subscriptions*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('superadmin.subscriptions.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-id-badge"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Souscriptions') }}</span>
    </a>
</li>

{{-- ⭐ NOUVEAU : Archives des paiements --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::is('payments-archive*') || Request::routeIs('payments-archive.*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('payments-archive.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-archive"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Archives paiements') }}</span>
        @php
            $pendingArchivesCount = \App\Models\PaymentArchive::where('status', 'pending')->count();
        @endphp
        @if($pendingArchivesCount > 0)
            <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-2 py-0.5 font-bold">{{ $pendingArchivesCount }}</span>
        @endif
    </a>
</li>

{{-- Support Client --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('superadmin.support.*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('superadmin.support.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-headset"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Support client') }}</span>
        @php
            $unreadCount = App\Models\SupportMessage::where('status', 'en_attente')->count();
        @endphp
        @if($unreadCount > 0)
            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-bold">{{ $unreadCount }}</span>
        @endif
    </a>
</li>

{{-- Messages de contact --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('admin.contacts.*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('admin.contacts.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft font-semibold">{{ __('Messages contact') }}</span>
        @php
            $unreadContactsCount = \App\Models\Contact::where('is_read', false)->count();
        @endphp
        @if($unreadContactsCount > 0)
            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center">
                {{ $unreadContactsCount }}
            </span>
        @endif
    </a>
</li>

{{-- Configuration du Footer --}}
<li class="mt-0.5 w-full">
    <a class="{{ Request::routeIs('superadmin.footer.*') ? 'bg-primary font-bold shadow-soft-xl rounded-lg btnAside text-white' : '' }} py-2 ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors duration-300"
        href="{{ route('superadmin.footer.index') }}">
        <div class="mr-2 flex h-8 w-8 items-center justify-center text-center">
            <i class="fa-solid fa-cog"></i>
        </div>
        <span class="ml-1 opacity-100 pointer-events-none ease-soft capitalize font-semibold">{{ __('Configuration Footer') }}</span>
    </a>
</li>
