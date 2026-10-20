<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
        <header class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ __('Espace manager') }}</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ __('Bonjour') }}, {{ $manager->first_name }} {{ $manager->name }}</h1>
            <p class="mt-1 text-slate-600">{{ $company?->compagny_name ?? __('Votre société') }} · {{ now()->translatedFormat('d F Y') }}</p>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" aria-label="{{ __('Statistiques de la société') }}">
            @foreach ([
                [__('Employés actifs'), $employeeCount, 'fa-users'],
                [__('Contrôleurs actifs'), $controllerCount, 'fa-user-shield'],
                [__('Sites'), $siteCount, 'fa-location-dot'],
                [__('Présents aujourd’hui'), $presentCount, 'fa-user-check'],
                [__('Absents aujourd’hui'), $absentCount, 'fa-user-xmark'],
            ] as [$label, $value, $icon])
                <article class="rounded-2xl bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-600">{{ $label }}</p>
                        <i class="fa-solid {{ $icon }} text-emerald-700" aria-hidden="true"></i>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-slate-900">{{ $value }}</p>
                </article>
            @endforeach
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Équipe de la société') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ __('Employés actifs associés à votre société') }}</p>
                </div>
                <p class="text-sm text-slate-600">{{ __('En retard aujourd’hui') }} : <strong class="text-slate-900">{{ $lateCount }}</strong></p>
            </div>

            @if ($recentEmployees->isEmpty())
                <p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">{{ __('Aucun employé actif pour le moment.') }}</p>
            @else
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($recentEmployees as $employee)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-3">
                            <span class="font-medium text-slate-800">{{ $employee->first_name }} {{ $employee->name }}</span>
                            <span class="text-sm text-slate-500">{{ $employee->email }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-app-layout>
