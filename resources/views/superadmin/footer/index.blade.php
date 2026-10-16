<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">
                            <i class="fas fa-cog mr-2"></i>
                            Configuration du Footer
                        </h2>
                        <a href="{{ route('superadmin.footer.edit') }}"
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition duration-150 ease-in-out flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Email --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-envelope mr-1"></i>
                                Email
                            </label>
                            <p class="text-gray-900">{{ $footer->email ?? 'Non défini' }}</p>
                        </div>

                        {{-- Téléphone --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-phone mr-1"></i>
                                Téléphone
                            </label>
                            <p class="text-gray-900">{{ $footer->phone ?? 'Non défini' }}</p>
                        </div>

                        {{-- Adresse --}}
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Adresse
                            </label>
                            <p class="text-gray-900">{{ $footer->address ?? 'Non défini' }}</p>
                        </div>

                        {{-- Copyright --}}
                        <div class="bg-gray-50 p-4 rounded-lg col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                <i class="fas fa-copyright mr-1"></i>
                                Texte de copyright
                            </label>
                            <p class="text-gray-900">{{ $footer->copyright_text ?? 'Non défini' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
