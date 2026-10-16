<x-app-layout>
    <div class="min-h-screen bg-white py-8 px-4">
        <div class="max-w-5xl mx-auto">
            <!-- En-tête -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ __('Nouveau Plan Tarifaire') }}
                </h1>
                <a href="{{ route('plans.index') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 text-sm font-medium border border-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Retour à la liste') }}
                </a>
            </div>

            <!-- Alertes -->
            @if (session('success'))
                <div id="session-success-alert-div" class="relative p-5 pr-12 mb-6 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-green-700 text-sm">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="document.getElementById('session-success-alert-div').style.display='none'" class="absolute top-3 right-3 text-green-400 hover:text-green-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div id="session-error-alert-div" class="relative p-5 pr-12 mb-6 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" onclick="document.getElementById('session-error-alert-div').style.display='none'" class="absolute top-3 right-3 text-red-400 hover:text-red-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Formulaire -->
            <form method="POST" action="{{ route('plans.store') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                @csrf

                <!-- Ligne 1: Nom + Prix -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <x-input-label for="name" :value="__('Nom du Plan * (ex: STARTER, GROWTH)')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <input id="name" name="name" type="text" placeholder="FREE" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="price" :value="__('Prix par mois *')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <input id="price" name="price" type="number" step="0.01" placeholder="0" value="{{ old('price', '0') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm" />
                        <x-input-error :messages="$errors->get('price')" class="mt-1.5" />
                    </div>
                </div>

                <!-- Ligne 2: Devise + Max employés -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <x-input-label for="currency" :value="__('Devise *')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <select id="currency" name="currency" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm appearance-none">
                            <option value="XOF" {{ old('currency', 'XOF') == 'XOF' ? 'selected' : '' }}>XOF (FCFA)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                        </select>
                        <x-input-error :messages="$errors->get('currency')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="max_employees" :value="__('Nombre maximum d\'employés * (-1 pour illimité)')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <input id="max_employees" name="max_employees" type="number" placeholder="3" value="{{ old('max_employees', '3') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm" />
                        <x-input-error :messages="$errors->get('max_employees')" class="mt-1.5" />
                    </div>
                </div>

                <!-- Ligne 3: Dashboard + Historique -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <x-input-label for="dashboard_type" :value="__('Type de Dashboard *')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <select id="dashboard_type" name="dashboard_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm appearance-none">
                            <option value="Basique" {{ old('dashboard_type', 'Basique') == 'Basique' ? 'selected' : '' }}>{{ __('Basique') }}</option>
                            <option value="Avancé" {{ old('dashboard_type') == 'Avancé' ? 'selected' : '' }}>{{ __('Avancé') }}</option>
                            <option value="Complet" {{ old('dashboard_type') == 'Complet' ? 'selected' : '' }}>{{ __('Complet') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('dashboard_type')" class="mt-1.5" />
                    </div>

                    <div>
                        <x-input-label for="history_duration" :value="__('Historique des données *')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <input id="history_duration" name="history_duration" type="text" placeholder="{{ __('ex: 7 jours, 1 mois, Illimité') }}" value="{{ old('history_duration') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm" />
                        <x-input-error :messages="$errors->get('history_duration')" class="mt-1.5" />
                    </div>
                </div>

                <!-- Ligne 4: Support -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <x-input-label for="support_type" :value="__('Canal de Support *')" class="text-sm font-semibold text-gray-700 mb-1.5 block" />
                        <select id="support_type" name="support_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-white text-gray-900 text-sm appearance-none">
                            <option value="Email" {{ old('support_type', 'Email') == 'Email' ? 'selected' : '' }}>{{ __('Email') }}</option>
                            <option value="WhatsApp" {{ old('support_type') == 'WhatsApp' ? 'selected' : '' }}>{{ __('WhatsApp') }}</option>
                            <option value="Prioritaire" {{ old('support_type') == 'Prioritaire' ? 'selected' : '' }}>{{ __('Prioritaire') }}</option>
                            <option value="Dédié" {{ old('support_type') == 'Dédié' ? 'selected' : '' }}>{{ __('Dédié') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('support_type')" class="mt-1.5" />
                    </div>
                </div>

                <!-- Fonctionnalités -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ __('Rattacher les fonctionnalités (Affichage Sidebar)') }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __('Sélectionnez les modules qui s\'afficheront dans le menu lateral des compagnies ayant souscrit à ce forfait :') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-6 rounded-xl border border-gray-200">
                        @foreach($features as $feature)
                            <div class="flex items-start space-x-3">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" id="feature_{{ $feature->id }}"
                                        {{ is_array(old('features')) && in_array($feature->id, old('features')) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                </div>
                                <div class="text-sm">
                                    <label for="feature_{{ $feature->id }}" class="font-semibold text-gray-800 flex items-center cursor-pointer select-none">
                                        <i class="fa-solid {{ $feature->icon }} text-gray-500 mr-2"></i>
                                        {{ __($feature->name) }}
                                    </label>
                                    <span class="text-xs text-gray-400 block mt-0.5">Slug code: <code class="bg-gray-200 px-1 rounded">{{ $feature->slug }}</code></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('features')" class="mt-2" />
                </div>

                <!-- Boutons -->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-wrap gap-4">
                    <button type="button" onclick="window.location.href='{{ route('plans.index') }}'" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium border border-gray-200">
                        {{ __('Annuler') }}
                    </button>

                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 text-sm font-medium shadow-md shadow-blue-600/25 ml-auto">
                        {{ __('Enregistrer le plan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
