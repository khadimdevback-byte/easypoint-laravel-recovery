<x-app-layout>

    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
        <div class=" my-10 px-6 py-6 ">
            <!-- card 1 -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Créer un menu') }}
                </h1>
            </div>

           <form method="POST" action="{{ route('superadmin.menus.store') }}">
                @csrf
                @method('POST')

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="name" :value="__('Nom du menu *')" />
                        <x-text-input-2 id="name" name="name" type="text"
                            placeholder="{{ __('Nom du menu ') }}" :value="old('name')"  autofocus />
                            @error('Nom du menu')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                    </div>

                    <div>
                        <x-input-label for="group" :value="__('Groupe ')" />
                        <x-text-input-2 id="groupe" name="group" type="text"
                            placeholder="{{ __('Nom du menu ') }}" :value="old('group')"  autofocus />
                            @error('Nom Groupe')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                    </div>
                </div>
                <br>
                <div class="my-19 flex">
                    <x-secondary-button  class="flex mr-4 justify-center shadow-md"><a href="">
                        {{ __('Annuler') }}
                    </a></x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Ajouter') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
