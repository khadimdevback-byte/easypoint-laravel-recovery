<x-app-layout>
    <div class="md:w-9/12 2xl:w-8/12 mx-auto">
        <div class="my-10 px-6 py-6">
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Modifier L\'Equipe') }}
                </h1>
            </div>

            <!-- Affichage des alertes -->
            @if (session('success'))
                <div id="session-success-alert-div" class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-primary">
                    {{ session('success') }}
                    <button type="button" alert-close onclick="document.getElementById('session-success-alert-div').style.display='none'" class="absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4">
                        <span aria-hidden="true" class="cursor-pointer">&#10005;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="session-error-alert-div" class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-danger">
                    {{ session('error') }}
                    <button type="button" alert-close onclick="document.getElementById('session-error-alert-div').style.display='none'" class="absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4">
                        <span aria-hidden="true" class="cursor-pointer">&#10005;</span>
                    </button>
                </div>
            @endif

            <!-- Formulaire d'édition -->
            <form action="{{ route('equipe.update', $equipe->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="nom" :value="__('Nom de l\'équipe *')" />
                        <x-text-input-2 id="nom" name="nom" type="text" placeholder="{{ __('Nom de l\'équipe') }}" :value="old('nom', $equipe->nom)" autofocus required />
                        @error('nom')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <br>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="employees" :value="__('Liste des Employés assignés *')" />

                        <!-- Sélecteur multiple pour les employés -->
                        <label class="hidden" for="hs-tags-input">Tags label</label>
                        <select multiple name="employees[]"
                                data-hs-select='{
                                    "placeholder": "Sélectionnez les employés...",
                                    "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-secondary dark:border-primary-color",
                                    "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 dark:bg-secondary dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                    "mode": "tags",
                                    "wrapperClasses": "relative ps-0.5 pe-9 min-h-[46px] flex items-center flex-wrap text-nowrap w-full border-t-0 border-r-0 border-l-0 border-b-2 border-primary-color rounded-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-secondary dark:border-primary-color dark:text-neutral-400",
                                    "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center relative z-10 bg-white border border-gray-200 rounded-full p-1 m-1 dark:bg-secondary dark:border-primary-color \"><div class=\"size-6 me-1\" data-icon></div><div class=\"whitespace-nowrap text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"inline-flex shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm dark:bg-neutral-700/50 dark:hover:bg-neutral-700 dark:text-neutral-400 cursor-pointer\" data-remove><svg class=\"shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
                                    "tagsInputId": "hs-tags-input",
                                    "tagsInputClasses": "py-3 px-2 rounded-lg order-1 text-sm outline-none dark:bg-secondary dark:placeholder-neutral-500 text-neutral-400",
                                    "optionTemplate": "<div class=\"flex items-center\"><div class=\"size-8 me-2\" data-icon></div><div><div class=\"text-sm font-semibold text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"text-xs text-gray-500 dark:text-neutral-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                                    "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500  \" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                                }'
                                class="hidden">
                            <option value="">Sélectionnez les employés...</option>
                            @foreach ($users_disponibles as $user)
                                <option value="{{ $user->id }}"
                                        @if (in_array($user->id, old('employees', $equipe->users->pluck('id')->toArray())))
                                            selected
                                        @endif>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <br><br><br>

                <div class="my-19 flex">
                    <x-secondary-button class="mr-2" href="{{ route('equipe.index') }}">
                        {{ __('Retour') }}
                    </x-secondary-button>
                    <x-primary-button>
                        {{ __('Mettre à jour') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
