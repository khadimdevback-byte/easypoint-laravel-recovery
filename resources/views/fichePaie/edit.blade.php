<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-6">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">✏️ Ajuster la fiche de paie de : {{ $fichePaie->user->name }}</h2>

        <form action="{{ route('fichepaie.update', $fichePaie->id) }}" method="POST" class="space-y-6 bg-white p-6 shadow rounded-xl">
            @csrf
            @method('PUT')

            {{-- Salaire brut caché pour calculs --}}
            <input type="hidden" id="salaire_brut_cache" value="{{ $fichePaie->salaire_brut }}">

            {{-- Section 1 : Éléments fixes --}}
            <div>
                <label for="salaire_brut" class="block text-sm font-medium text-gray-700">Salaire brut (€)</label>
                <input type="number" name="salaire_brut" id="salaire_brut" step="0.01" value="{{ old('salaire_brut', $fichePaie->salaire_brut) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="primes" class="block text-sm font-medium text-gray-700">Primes (€)</label>
                    <input type="number" name="primes" id="primes" step="0.01" value="{{ old('primes', $fichePaie->primes) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer">
                </div>
                <div>
                    <label for="avancements" class="block text-sm font-medium text-gray-700">Avancements (€)</label>
                    <input type="number" name="avancements" id="avancements" step="0.01" value="{{ old('avancements', $fichePaie->avancements) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="retenues" class="block text-sm font-medium text-gray-700">Retenues (€)</label>
                    <input type="number" name="retenues" id="retenues" step="0.01" value="{{ old('retenues', $fichePaie->retenues) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer">
                </div>
                <div>
                    <label for="cotisations" class="block text-sm font-medium text-gray-700">Cotisations (€)</label>
                    <input type="number" name="cotisations" id="cotisations" step="0.01" value="{{ old('cotisations', $fichePaie->cotisations) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
            </div>

            {{-- Section 2 : Calculs dynamiques --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="net_avant_impot" class="block text-sm font-medium text-gray-700">Net avant impôt (€)</label>
                    <input type="number" step="0.01" id="net_avant_impot" name="net_avant_impot" value="{{ old('net_avant_impot', $fichePaie->net_avant_impot) }}"
                        class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm" readonly>
                </div>

                <div>
                    <label for="impot_source" class="block text-sm font-medium text-gray-700">Impôt à la source (€)</label>
                    <input type="number" name="impot_source" id="impot_source" step="0.01" value="{{ old('impot_source', $fichePaie->impot_source) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer">
                </div>

                <div>
                    <label for="salaire_net" class="block text-sm font-medium text-gray-700">Net à payer (€)</label>
                    <input type="number" disabled id="salaire_net" value="{{ $fichePaie->salaire_net }}"
                        class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm">
                </div>
            </div>

            {{-- Section 3 : Autres --}}
            <div>
                <label for="net_social" class="block text-sm font-medium text-gray-700">Net social (€)</label>
                <input type="number" name="net_social" id="net_social" step="0.01" value="{{ old('net_social', $fichePaie->net_social) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 recalculer" readonly>
            </div>

            <div>
                <label for="date_paiement" class="block text-sm font-medium text-gray-700">Date de paiement</label>
                <input type="date" name="date_paiement" id="date_paiement" value="{{ old('date_paiement', $fichePaie->date_paiement) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Actions --}}
            <div class="pt-4 flex justify-between items-center">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    ✅ Mettre à jour
                </button>
                <a href="{{ route($fichePaie->horaire_type === 'fixe' ? 'fichepaie.fixe' : 'fichepaie.rotatif') }}"
                   class="text-blue-600 hover:underline">
                    ← Retour à la liste
                </a>
            </div>
        </form>
    </div>

    {{-- Script pour recalculer dynamiquement --}}
    <script>
        function recalculerSalaireNet() {
            const salaireBrut = parseFloat(document.getElementById('salaire_brut').value) || 0;
            const primes = parseFloat(document.getElementById('primes').value) || 0;
            const avancements = parseFloat(document.getElementById('avancements').value) || 0;
            const retenues = parseFloat(document.getElementById('retenues').value) || 0;
            const cotisations = parseFloat(document.getElementById('cotisations').value) || 0;
            const impotSource = parseFloat(document.getElementById('impot_source').value) || 0;

            const netAvantImpot = salaireBrut + primes - avancements - retenues - cotisations;
            const netFinal = netAvantImpot - impotSource;

            document.getElementById('net_avant_impot').value = netAvantImpot.toFixed(2);
            document.getElementById('salaire_net').value = netFinal.toFixed(2);

            // Calcul net social
            const netSocial = salaireBrut + primes - retenues - cotisations - avancements;
            document.getElementById('net_social').value = netSocial.toFixed(2);
        }

        document.querySelectorAll('.recalculer').forEach(input => {
            input.addEventListener('input', recalculerSalaireNet);
        });

        // Initial recalcul
        recalculerSalaireNet();
    </script>
</x-app-layout>
