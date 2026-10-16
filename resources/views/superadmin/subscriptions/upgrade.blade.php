<x-app-layout>
    <div class="container mx-auto px-4 py-8">

        {{-- En-tête de la page --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">Faites évoluer votre abonnement</h2>
            <p class="text-gray-600 mt-2">Augmentez vos quotas pour ajouter plus d'employés et débloquer toutes les fonctionnalités.</p>
        </div>

        {{-- Affichage des messages de notification (Erreur / Succès) --}}
        @if(session('error'))
            <div class="max-w-6xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="max-w-6xl mx-auto mb-6 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl text-sm shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Section résumé du plan actuellement actif --}}
        @if(isset($currentPlan))
            <div class="max-w-md mx-auto mb-12 bg-blue-50 border border-blue-200 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">Votre abonnement actuel</span>
                    <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $currentPlan->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Limite :
                        <strong class="text-gray-700">{{ $currentPlan->max_employees === -1 ? 'Illimitée' : $currentPlan->max_employees . ' employés' }}</strong>
                    </p>
                </div>
                <div class="bg-green-600 text-white font-bold px-4 py-2 rounded-xl text-sm tracking-wide shadow-sm">
                    Actif
                </div>
            </div>
        @endif

        {{-- Grille des forfaits disponibles --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($plans as $plan)
                @php
                    $isCurrent = isset($currentPlan) && $currentPlan->id === $plan->id;
                @endphp

                <div class="bg-white border {{ $isCurrent ? 'border-blue-500 ring-4 ring-blue-100' : 'border-gray-200' }} rounded-2xl p-8 shadow-sm flex flex-col justify-between relative transition-all hover:shadow-md">

                    @if($isCurrent)
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider shadow-sm">
                            Plan Actuel
                        </span>
                    @endif

                    <div>
                        <h4 class="text-xl font-bold text-gray-800 text-center">{{ $plan->name }}</h4>
                        <p class="text-center mt-4">
                            <span class="text-4xl font-extrabold text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                            <span class="text-gray-500 text-sm block md:inline mt-1 md:mt-0 ml-1">F CFA / mois</span>
                        </p>
                        <p class="text-gray-500 text-sm text-center mt-3 min-h-[40px]">{{ $plan->description }}</p>

                        <hr class="my-6 border-gray-100">

                        {{-- Liste des avantages / caractéristiques --}}
                        <ul class="space-y-4 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Gestion des pointages & horaires</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Maximum de : <strong class="text-gray-800 ml-1">{{ $plan->max_employees === -1 ? 'Illimité' : $plan->max_employees }} employés</strong></span>
                            </li>
                        </ul>
                    </div>

                    {{-- Bouton d'action --}}
                    <div class="mt-8">
                        @if($isCurrent)
                            <button disabled class="w-full bg-gray-100 text-gray-400 font-bold py-3 px-4 rounded-xl cursor-not-allowed text-center text-sm border border-gray-200">
                                Votre forfait actuel
                            </button>
                        @else
                            <form action="{{ route('subscriptions.processUpgrade', $plan->id) }}" method="POST" onsubmit="disableSubmitButton(this)">
                                @csrf
                                <button type="submit"
                                        class="submit-btn w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl text-center text-sm transition-colors shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 outline-none">
                                    @if(isset($currentPlan))
                                        {{ $plan->price > $currentPlan->price ? 'Passer à ce plan (Upgrade)' : 'Choisir ce plan (Downgrade)' }}
                                    @else
                                        Choisir ce plan
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script sécurisé pour éviter le double-clic lors du chargement de la passerelle FedaPay --}}
    <script>
        function disableSubmitButton(form) {
            const btn = form.querySelector('.submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                btn.classList.add('bg-blue-400', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin inline-block mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Redirection vers FedaPay...
                `;
            }
        }
    </script>
</x-app-layout>
