<x-app-layout>
    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 md:px-8 max-w-4xl">
            <div class="mb-6">
                <a href="{{ route('superadmin.support.index') }}" class="text-green-600 hover:text-green-700">
                    <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                </a>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Conversation principale -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">{{ $support->subject }}</h3>
                                    <div class="flex gap-3 mt-1">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-building mr-1"></i> {{ $support->company->compagny_name }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-user mr-1"></i> {{ $support->user->name ?? 'Utilisateur' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i> {{ $support->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('superadmin.support.status', $support) }}" method="POST" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-3 py-1 text-sm">
                                            <option value="en_attente" {{ $support->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                            <option value="en_cours" {{ $support->status == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="resolu" {{ $support->status == 'resolu' ? 'selected' : '' }}>Résolu</option>
                                            <option value="ferme" {{ $support->status == 'ferme' ? 'selected' : '' }}>Fermé</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Afficher toute la conversation -->
                            <div class="space-y-4">
                                <!-- Message original -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-building text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-1">
                                                <p class="font-semibold text-gray-800">{{ $support->company->compagny_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $support->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <p class="text-gray-700 whitespace-pre-wrap">{{ $support->message }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="bg-yellow-100 p-3 mb-4 rounded-lg text-sm">
                                    <strong>🔍 DEBUG:</strong><br>
                                    - replies existe: {{ isset($support->replies) ? '✅ OUI' : '❌ NON' }}<br>
                                    - Nombre de réponses: {{ $support->replies ? $support->replies->count() : '0' }}<br>
                                    - sender_type existe: {{ isset($support->replies[0]->sender_type) ? '✅ OUI' : '❌ NON' }}<br>
                                    - parent_id du message: {{ $support->parent_id ?? 'NULL' }}
                                </div> --}}

                                <!-- Les réponses (si existent) -->
                                @if(isset($support->replies) && $support->replies->count() > 0)
                                    @foreach($support->replies as $reply)
                                        @if($reply->sender_type == 'superadmin')
                                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 rounded-full bg-green-600 flex items-center justify-center">
                                                            <i class="fas fa-headset text-white text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <p class="font-semibold text-green-700">Support Easy Point</p>
                                                            <p class="text-xs text-gray-500">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                        <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <i class="fas fa-building text-blue-600 text-sm"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <p class="font-semibold text-gray-800">{{ $support->company->compagny_name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $reply->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                        <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de réponse -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-6">
                        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                            <h5 class="font-bold text-gray-800">
                                <i class="fas fa-reply-all mr-2 text-green-600"></i> Répondre
                            </h5>
                        </div>

                        @if($support->status != 'resolu' && $support->status != 'ferme')
                            <form action="{{ route('superadmin.support.reply', $support) }}" method="POST" class="p-6">
                                @csrf

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Votre réponse</label>
                                    <textarea name="response" rows="6" required
                                              class="w-full rounded-lg border border-gray-300 p-3 focus:border-green-500 focus:ring-green-500"
                                              placeholder="Tapez votre réponse ici..."></textarea>
                                </div>

                                <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 font-semibold text-white transition hover:bg-green-700">
                                    <i class="fas fa-paper-plane mr-2"></i> Envoyer la réponse
                                </button>
                            </form>
                        @else
                            <div class="p-6 text-center">
                                <i class="fas fa-check-circle text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">Cette conversation est terminée.</p>
                                @if($support->status == 'resolu')
                                    <p class="text-sm text-gray-400 mt-2">La demande a été résolue.</p>
                                @endif
                            </div>
                        @endif

                        <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                            <div class="text-xs text-gray-500">
                                <p><i class="fas fa-envelope mr-1"></i> Email: {{ $support->company->compagny_email }}</p>
                                <p class="mt-1"><i class="fas fa-phone mr-1"></i> Tél: {{ $support->company->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
