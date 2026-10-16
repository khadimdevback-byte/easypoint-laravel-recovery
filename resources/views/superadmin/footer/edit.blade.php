<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier la configuration du Footer
                        </h2>
                        <a href="{{ route('superadmin.footer.index') }}"
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                                <ul class="list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('superadmin.footer.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Email --}}
                            <div class="col-span-1">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                                       id="email"
                                       name="email"
                                       placeholder="exemple@domaine.com"
                                       value="{{ old('email', $footer->email ?? '') }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div class="col-span-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-phone mr-1"></i>
                                    Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror"
                                       id="phone"
                                       name="phone"
                                       placeholder="+228 90 00 00 00"
                                       value="{{ old('phone', $footer->phone ?? '') }}">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    Adresse
                                </label>
                                <input type="text"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('address') border-red-500 @enderror"
                                       id="address"
                                       name="address"
                                       placeholder="Lomé, Togo"
                                       value="{{ old('address', $footer->address ?? '') }}">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Copyright --}}
                            <div class="col-span-2">
                                <label for="copyright_text" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-copyright mr-1"></i>
                                    Texte de copyright
                                </label>
                                <input type="text"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('copyright_text') border-red-500 @enderror"
                                       id="copyright_text"
                                       name="copyright_text"
                                       placeholder="2026 EasyPoint. Tous droits réservés."
                                       value="{{ old('copyright_text', $footer->copyright_text ?? '') }}">
                                @error('copyright_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Boutons --}}
                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Mettre à jour
                            </button>

                            <button type="button"
                                    class="w-full sm:w-auto px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition duration-150 ease-in-out flex items-center justify-center"
                                    onclick="if(confirm('⚠️ Voulez-vous vraiment réinitialiser le footer aux valeurs par défaut ? Cette action est irréversible.')) document.getElementById('reset-form').submit();">
                                <i class="fas fa-undo mr-2"></i>
                                Réinitialiser
                            </button>
                        </div>
                    </form>

                    {{-- Formulaire de réinitialisation caché --}}
                    <form id="reset-form"
                          action="{{ route('superadmin.footer.reset') }}"
                          method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
