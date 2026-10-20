<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <header class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ __('Espace client') }}</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ __('Bienvenue') }}, {{ $customer->first_name }} {{ $customer->name }}</h1>
            <p class="mt-1 text-slate-600">{{ __('Retrouvez ici vos informations personnelles et celles de votre société.') }}</p>
        </header>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">{{ __('Mon profil') }}</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-slate-500">{{ __('Nom') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->first_name }} {{ $customer->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">{{ __('Adresse e-mail') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">{{ __('Téléphone') }}</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $customer->phone ?: __('Non renseigné') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">{{ __('Ma société') }}</h2>
            @if ($company)
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-slate-500">{{ __('Nom') }}</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $company->compagny_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">{{ __('Adresse e-mail') }}</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $company->compagny_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">{{ __('Téléphone') }}</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $company->phone ?: __('Non renseigné') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-500">{{ __('Adresse') }}</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $company->address ?: __('Non renseignée') }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-600">{{ __('Aucune société n’est associée à votre compte.') }}</p>
            @endif
        </section>
    </div>
</x-app-layout>
