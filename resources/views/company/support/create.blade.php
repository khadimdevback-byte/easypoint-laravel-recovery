<x-app-layout>
    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 md:px-8 max-w-2xl">
            <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-green-600 to-emerald-700 p-8 text-white shadow-xl">
                <div class="relative z-10">
                    <h1 class="text-2xl font-bold mb-2">Nouveau message</h1>
                    <p class="text-green-100">Décrivez votre problème, nous vous répondrons rapidement</p>
                </div>
            </div>

            <div class="rounded-2xl bg-white shadow-lg overflow-hidden">
                <form action="{{ route('support.store') }}" method="POST" class="p-6">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sujet *</label>
                        <input type="text" name="subject" required
                               class="w-full rounded-lg border border-gray-300 p-2.5 focus:border-green-500 focus:ring-green-500"
                               placeholder="Ex: Problème de connexion, Facture, etc.">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                        <select name="category" required class="w-full rounded-lg border border-gray-300 p-2.5 focus:border-green-500 focus:ring-green-500">
                            <option value="question">Question générale</option>
                            <option value="bug">Problème technique</option>
                            <option value="facturation">Facturation</option>
                            <option value="renouvellement">Renouvellement d'abonnement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" rows="6" required
                                  class="w-full rounded-lg border border-gray-300 p-2.5 focus:border-green-500 focus:ring-green-500"
                                  placeholder="Décrivez votre demande en détail..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 10 caractères</p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 rounded-lg bg-green-600 px-4 py-2 font-semibold text-white transition hover:bg-green-700">
                            <i class="fas fa-paper-plane mr-2"></i> Envoyer
                        </button>
                        <a href="{{ route('support.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-center font-semibold text-gray-700 transition hover:bg-gray-50">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
