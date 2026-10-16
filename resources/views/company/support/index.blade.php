<x-app-layout>
    <div class="py-6">
        <div class="mx-auto px-4 sm:px-6 md:px-8">
            <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-green-600 to-emerald-700 p-8 text-white shadow-xl">
                <div class="relative z-10">
                    <h1 class="text-2xl font-bold mb-2">Support client</h1>
                    <p class="text-green-100">Besoin d'aide ? Notre équipe est là pour vous répondre</p>
                </div>
                <div class="absolute right-0 top-0 -mt-4 -mr-4 opacity-20">
                    <i class="fa-solid fa-headset text-9xl"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-2xl bg-white shadow-lg overflow-hidden">
                        <div class="border-b border-gray-200 px-6 py-4 bg-gray-50 flex justify-between items-center">
                            <h5 class="font-bold text-gray-700">
                                <i class="fas fa-comments mr-2 text-green-500"></i> Mes messages
                            </h5>
                            <a href="{{ route('support.create') }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i> Nouveau message
                            </a>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse($messages as $message)
                                <div class="p-4 hover:bg-gray-50 transition group">
                                    <div class="flex items-start justify-between">
                                        <a href="{{ route('support.show', $message) }}" class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-semibold text-gray-800">{{ $message->subject }}</span>
                                                @if($message->status == 'en_attente')
                                                    <span class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700">
                                                        En attente
                                                    </span>
                                                @elseif($message->status == 'en_cours')
                                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                                        En cours
                                                    </span>
                                                @elseif($message->status == 'resolu')
                                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                                        Résolu
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 line-clamp-2">{{ $message->message }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                                        </a>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                            @if($message->status != 'resolu' && $message->replies()->count() == 0)
                                                <form action="{{ route('support.destroy', $message) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($message->response || $message->replies()->count() > 0)
                                                <i class="fas fa-reply text-gray-400" title="Réponse reçue"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">Aucun message</p>
                                    <p class="text-sm text-gray-400 mt-1">Envoyez votre premier message au support</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <!-- Contact direct -->
                    <div class="rounded-2xl bg-white shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h5 class="font-bold text-gray-700 mb-3">
                                <i class="fas fa-phone-alt mr-2 text-green-500"></i> Contact direct
                            </h5>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-green-500 w-8"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium">support@easypoint.com</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-green-500 w-8"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Téléphone</p>
                                        <p class="text-sm font-medium">+228 70 144 668</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <i class="fab fa-whatsapp text-green-500 w-8"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">WhatsApp</p>
                                        <p class="text-sm font-medium">+228 70 144 668</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
