<x-app-layout>
    <div class="md:w-9/12 2xl:w-8/12 mx-auto">
        <div class="my-10 px-6 py-6">
            <!-- Titre -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Liste des Sites et Equipes') }}
                </h1>
            </div>

            <!-- Liste des sites -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($sites as $site)
                    <div class="bg-white shadow-lg rounded-lg p-4">
                        <!-- Menu déroulant pour le site, positionné à droite -->
                        <div class="relative">
                            <button id="menu-toggle-{{ $site->id }}" class="text-gray-600 hover:text-gray-800 absolute right-0 top-0 mt-2 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="bi bi-three-dots-vertical">
                                    <path d="M3 3a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM3 10a1 1 0 1 1 2 0 1 1 0 0 1-2 0zm0 7a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM10 3a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM10 10a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM10 17a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM17 3a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM17 10a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM17 17a1 1 0 1 1 2 0 1 1 0 0 1-2 0z"/>
                                </svg>
                            </button>

                            <!-- Menu déroulant pour le site -->
                            <div id="menu-{{ $site->id }}" class="hidden absolute right-0 mt-2 w-40 bg-white shadow-md rounded-lg border">
                                <a href="{{ route('edit.assign.equipes.site', ['site' => $site->id]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-200">
                                    Assigner à Site
                                </a>
                            </div>
                        </div>

                        <h3 class="font-bold text-xl">{{ $site->name }}</h3>

                        <!-- Affichage du nom de la rotation -->
                        @if ($site->rotation)  {{-- rotation au singulier, car c'est une relation belongsTo --}}
                            <p class="text-gray-600 text-sm mt-2">
                                Rotation :
                                <span class="font-semibold">{{ $site->rotation->nom }}</span>
                            </p>
                        @else
                            <p class="text-gray-600 text-sm mt-2">Aucune rotation assignée</p>
                        @endif

                        <div class="mt-4">
                            @foreach ($site->equipes as $equipe)
                                <div class="bg-gray-100 rounded-lg p-2 my-2 cursor-pointer flex justify-between items-center">
                                    <a href="{{ route('equipes.show', $equipe->id) }}" class="block">
                                        <span class="font-semibold">{{ $equipe->nom }}</span>
                                    </a>
                                    <!-- Formulaire de suppression -->
                                    <form action="{{ route('supprimer.assignation', ['site_id' => $site->id, 'equipe_id' => $equipe->id]) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message de succès -->
            @if (session('success'))
                <div class="mt-4 text-green-600">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Script pour activer/désactiver le menu -->
    <script>
        document.querySelectorAll('[id^="menu-toggle-"]').forEach(button => {
            button.addEventListener('click', function () {
                const menuId = this.id.replace('menu-toggle-', '');
                const menu = document.getElementById('menu-' + menuId);

                // Toggle visibility of the menu
                menu.classList.toggle('hidden');
            });
        });
    </script>
</x-app-layout>
