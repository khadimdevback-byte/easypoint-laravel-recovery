@php
    use Carbon\Carbon;
@endphp

<x-app-layout>

    <!-- cards -->
    <div class="w-full mx-auto pb-20 bg-gray-50">

        <div class="ease-soft-in-out relative h-full py-10 md:w-9/12  2xl:w-8/12 mx-auto transition-all duration-200">

            <div class="bg-white p-5 shadow-sm rounded-lg">
                <dl class="divide-y divide-gray-100">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-semibold text-gray-900">
                            <h3 class="text-xl font-semibold text-gray-900 mb-5">Détails de la société</h3>
                            <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Statut : <span
                                    class="text-base capitalize font-semibold text-gray-900">{{ __($company->status) ?? '-' }}</span>
                            </p>
                        </dt>
                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0 items-center">
                            @if ($company->logo)
                                <img class="object-cover shadow rounded-xl size-24"
                                    src="{{ asset('storage/' . $company->logo) }}" alt="logo">
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-gray-100">
                    <dl class="divide-y divide-gray-100">
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Nom de la société</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">
                                {{ $company->compagny_name ?? '-' }}</dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Email de la société</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">
                                {{ $company->compagny_email ?? '-' }}</dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Adresse de la société </dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->address ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Numéro Siret</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->n_siret ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Numéro Siren</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->n_siren ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Téléphone</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->phone ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Site Web</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->website ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">Fuseau horaire</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->timezone ?? '-' }}
                            </dd>
                        </div>
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">IBAN </dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">{{ $company->iban ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

    </div>
    <!-- end cards -->

</x-app-layout>
