{{-- <x-app-layout>
<div class="container">
    <h2 class="text-xl font-bold mb-4">Menus visibles pour : {{ $compagnie->compagny_name }}</h2>

    <form method="POST" action="{{ route('superadmin.menu-management.update', $compagnie->id) }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($menus as $menu)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="menus[]" value="{{ $menu->id }}"
                        {{ $compagnie->menus->contains($menu->id) ? 'checked' : '' }}>
                    <span>{{ $menu->name }}</span>
                </label>
            @endforeach
        </div>

        <button type="submit"
            class="mt-4 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Enregistrer
        </button>
    </form>
</div>
</x-app-layout> --}}

<x-app-layout>
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6">Menus visibles pour : {{ $compagnie->compagny_name }}</h2>

        <form method="POST" action="{{ route('superadmin.menu-management.update', $compagnie->id) }}">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($menus as $groupe => $sousMenus)
                    <div class="bg-white shadow rounded-xl p-4 border border-gray-200">
                        <h3 class="text-base font-semibold mb-3 text-gray-800 text-center">{{ $groupe }}</h3>

                        <div class="space-y-2">
                            @foreach ($sousMenus as $menu)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="menus[]" value="{{ $menu->id }}"
                                        class="form-checkbox text-green-600 rounded focus:ring-0"
                                        {{ $compagnie->menus->contains($menu->id) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $menu->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-app-layout>



