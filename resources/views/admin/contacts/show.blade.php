@php
    use Carbon\Carbon;
@endphp

<x-app-layout>
    <div class="md:w-12/12 2xl:w-12/12 px-2 lg:px-6 py-6 mx-auto">
        <div class="mx-3">
            <!-- En-tête avec bouton retour -->
            <div class="flex justify-between items-center mb-6 mt-6">
                <div>
                    <h1 class="font-bold text-black text-2xl mb-1">Détail du message</h1>
                    <p class="text-gray-500 text-sm">Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <a href="{{ route('admin.contacts.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow flex items-center space-x-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
            </div>

            <!-- Grille d'informations -->
            <div class="grid 2xl:grid-cols-2 2xl:gap-4 mb-6">
                <!-- Carte informations contact -->
                <div class="border-black/12.5 p-5 shadow-soft-xl relative z-20 min-w-0 break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h3 class="font-semibold text-black text-lg">Informations contact</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Nom complet :</div>
                            <div class="flex-1 font-medium text-gray-800">{{ $contact->first_name }} {{ $contact->last_name }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Email :</div>
                            <div class="flex-1">
                                <a href="mailto:{{ $contact->email }}" class="text-primary hover:underline">{{ $contact->email }}</a>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Téléphone :</div>
                            <div class="flex-1 text-gray-800">{{ $contact->phone ?? 'Non renseigné' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Entreprise :</div>
                            <div class="flex-1 font-medium text-gray-800">{{ $contact->company }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Employés :</div>
                            <div class="flex-1 text-gray-800">{{ $contact->employees ?? 'Non renseigné' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Carte type de demande et statut -->
                <div class="border-black/12.5 p-5 shadow-soft-xl relative z-20 min-w-0 break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                            <i class="fas fa-tag text-primary"></i>
                        </div>
                        <h3 class="font-semibold text-black text-lg">Type & Statut</h3>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Type de demande :</div>
                            <div class="flex-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($contact->intent == 'general') bg-gray-100 text-gray-700
                                    @elseif($contact->intent == 'devis') bg-purple-100 text-purple-700
                                    @else bg-orange-100 text-orange-700 @endif">
                                    @if($contact->intent == 'general')
                                        <i class="fas fa-comment mr-1"></i>Renseignement
                                    @elseif($contact->intent == 'devis')
                                        <i class="fas fa-file-invoice mr-1"></i>Devis
                                    @else
                                        <i class="fas fa-headset mr-1"></i>Support
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Statut actuel :</div>
                            <div class="flex-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($contact->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($contact->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($contact->status == 'responded') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($contact->status == 'pending')
                                        <i class="fas fa-clock mr-1"></i>
                                    @elseif($contact->status == 'processing')
                                        <i class="fas fa-spinner mr-1"></i>
                                    @elseif($contact->status == 'responded')
                                        <i class="fas fa-check-circle mr-1"></i>
                                    @else
                                        <i class="fas fa-archive mr-1"></i>
                                    @endif
                                    {{ $contact->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">IP Address :</div>
                            <div class="flex-1 text-gray-800 text-sm">{{ $contact->ip_address ?? 'Non renseignée' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32 text-gray-500 text-sm">Reçu le :</div>
                            <div class="flex-1 text-gray-800 text-sm">{{ $contact->created_at->format('d/m/Y à H:i:s') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte sujet et message -->
            <div class="border-black/12.5 p-5 mb-6 shadow-soft-xl relative z-20 min-w-0 break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-envelope text-primary"></i>
                    </div>
                    <h3 class="font-semibold text-black text-lg">Contenu du message</h3>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-sm mb-2">Sujet :</label>
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <p class="text-gray-800 font-medium">{{ $contact->subject }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-500 text-sm mb-2">Message :</label>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ nl2br(e($contact->message)) }}</p>
                    </div>
                </div>

                @if($contact->attachment)
                <div>
                    <label class="block text-gray-500 text-sm mb-2">Pièce jointe :</label>
                    <a href="{{ Storage::url($contact->attachment) }}" target="_blank"
                       class="inline-flex items-center gap-2 text-primary hover:text-primary-dark bg-primary/10 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download"></i>
                        <span>Télécharger la pièce jointe</span>
                        <i class="fas fa-external-link-alt text-xs ml-1"></i>
                    </a>
                </div>
                @endif
            </div>

            <!-- Carte changement de statut -->
            <div class="border-black/12.5 p-5 mb-10 shadow-soft-xl relative z-20 min-w-0 break-words rounded-2xl border-0 border-solid bg-white bg-clip-border">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-primary"></i>
                    </div>
                    <h3 class="font-semibold text-black text-lg">Changer le statut</h3>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <label class="text-gray-600 font-medium">Nouveau statut :</label>
                    <select name="status" onchange="updateStatus(this.value)"
                            class="py-2 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        <option value="pending" {{ $contact->status == 'pending' ? 'selected' : '' }}>📋 En attente</option>
                        <option value="processing" {{ $contact->status == 'processing' ? 'selected' : '' }}>🔄 En traitement</option>
                        <option value="responded" {{ $contact->status == 'responded' ? 'selected' : '' }}>✅ Répondu</option>
                        <option value="closed" {{ $contact->status == 'closed' ? 'selected' : '' }}>📦 Clôturé</option>
                    </select>

                    <div class="text-sm text-gray-400">
                        <i class="fas fa-info-circle"></i>
                        Le statut sera visible par l'équipe administrative
                    </div>
                </div>
            </div>

            <!-- Actions supplémentaires -->
            <div class="flex justify-end gap-3 mt-4">
                <button onclick="deleteContact({{ $contact->id }})"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded-lg shadow flex items-center space-x-2 transition-colors">
                    <i class="fas fa-trash-alt"></i>
                    <span>Supprimer ce message</span>
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function updateStatus(status) {
        Swal.fire({
            title: 'Changement de statut',
            text: 'Voulez-vous mettre à jour le statut de ce message ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2e7d64',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, mettre à jour',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/contacts/{{ $contact->id }}/status`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Succès!', 'Le statut a été mis à jour.', 'success')
                              .then(() => location.reload());
                      }
                  }).catch(error => {
                      Swal.fire('Erreur!', 'Une erreur est survenue.', 'error');
                  });
            } else {
                // Revert le select à l'ancienne valeur
                event.target.value = '{{ $contact->status }}';
            }
        });
    }

    function deleteContact(id) {
        Swal.fire({
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer ce message ? Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/contacts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Supprimé!', 'Le message a été supprimé.', 'success')
                              .then(() => window.location.href = '{{ route("admin.contacts.index") }}');
                      }
                  }).catch(error => {
                      Swal.fire('Erreur!', 'Une erreur est survenue.', 'error');
                  });
            }
        });
    }
    </script>
    @endpush
</x-app-layout>
