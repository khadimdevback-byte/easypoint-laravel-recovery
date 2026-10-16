@foreach($fonction as $fonctions)
    <tr>
        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
            <input id="remember_me" type="checkbox" class="rounded checkbox border-gray-300 text-primary focus:ring-0" name="fonction_id" value="{{ $fonctions->id }}">
        </td>
        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
            <p class="mb-0 font-semibold text-sm leading-normal">{{ $fonctions->nom_fonction ?? '-' }}</p>
        </td>
        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
            <p class="mb-0 font-semibold text-sm leading-normal">{{ $fonctions->description_fonction ?? '-' }}</p>
        </td>
        <td class="p-2 action-column align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-md text-slate-500 focus:outline-none transition ease-in-out duration-150">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link class="text-slate-500" href="{{ route('fonctions.edit', $fonctions->id) }}">
                        {{ __('Modifier') }}
                    </x-dropdown-link>
                    <form method="POST" id="delete-form-{{ $fonctions->id }}" action="{{ route('fonctions.delete', $fonctions->id) }}" class="cursor-pointer">
                        @csrf
                        @method('DELETE')
                        <x-dropdown-link class="text-slate-500" onclick="event.preventDefault(); if(confirm('Êtes-vous sûr de vouloir supprimer cette fonction ?')) document.getElementById('delete-form-{{ $fonctions->id }}').submit();">
                            {{ __('Supprimer') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </td>
    </tr>
@endforeach
