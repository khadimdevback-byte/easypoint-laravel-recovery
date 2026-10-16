<x-app-layout>
    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 md:px-8 max-w-3xl">
            <div class="mb-6">
                <a href="{{ route('support.index') }}" class="text-green-600 hover:text-green-700">
                    <i class="fas fa-arrow-left mr-2"></i> Retour aux messages
                </a>
            </div>

            <div class="rounded-2xl bg-white shadow-lg overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="text-lg font-bold text-gray-800">{{ $support->subject }}</h5>
                            <div class="flex gap-2 mt-1">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ $support->created_at->format('d/m/Y H:i') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-tag mr-1"></i> {{ ucfirst($support->category) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            @if($support->status == 'en_attente')
                                <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">En attente</span>
                            @elseif($support->status == 'en_cours')
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">En cours</span>
                            @elseif($support->status == 'resolu')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Résolu</span>
                            @endif
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

                        <!-- Les réponses -->
                        @if($support->replies && $support->replies->count() > 0)
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

                    <!-- Formulaire de réponse (si le message n'est pas résolu) -->
                    @if($support->status != 'resolu' && $support->status != 'ferme')
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Répondre</h4>
                            <form action="{{ route('support.reply', $support) }}" method="POST">
                                @csrf
                                <textarea name="reply_message" rows="4" required
                                          class="w-full rounded-lg border border-gray-300 p-3 focus:border-green-500 focus:ring-green-500"
                                          placeholder="Tapez votre réponse ici..."></textarea>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 font-semibold text-white transition hover:bg-green-700">
                                        <i class="fas fa-paper-plane mr-2"></i> Envoyer la réponse
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 rounded-lg bg-gray-50 p-4 text-center">
                            <i class="fas fa-check-circle text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500">Cette conversation est terminée.</p>
                            @if($support->status == 'resolu')
                                <p class="text-sm text-gray-400">Le support a résolu votre demande.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
