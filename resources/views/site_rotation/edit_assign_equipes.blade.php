<x-app-layout>
    <div class="md:w-9/12 2xl:w-8/12 mx-auto">
        <div class="my-10 px-6 py-6">
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Modifier l\'assignation des équipes à un site') }}
                </h1>
            </div>
            @if ($errors->has('users'))
                <div class="alert alert-danger text-red-500">
                    {{ $errors->first('users') }}
                </div>
            @endif
            <form action="{{ route('update.assign.equipes.site', $site->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="site_id" :value="__('Site *')" />
                        <x-select-input
                            id="site-select"
                            name="site_id"
                            :options="$site->pluck('name', 'id')"
                            :selected="$site->id" />
                        @error('site_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                   <div>
                        <x-input-label for="rotation_id" :value="__('Rotation *')" />
                        <x-select-input
                            id="rotation-select"
                            name="rotation_id"
                            :options="$rotations->pluck('nom', 'id')->toArray()"
                            :selected="old('rotation_id', $site->rotation_id)" />
                        @error('rotation_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <br><br>

                <x-input-label for="equipes" :value="__('Liste des Équipes*')" />
                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <x-input-label for="equipes" :value="__('Choisir des équipes :')" />

                        <select id="equipes" name="equipes[]" multiple
                            data-hs-select='{
                                "placeholder": "Sélectionnez des équipes...",
                                "dropdownClasses": "mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-secondary dark:border-primary-color",
                                "optionClasses": "py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 hs-select-disabled:pointer-events-none hs-select-disabled:opacity-50 dark:bg-secondary dark:hover:bg-neutral-800 dark:text-neutral-200 dark:focus:bg-neutral-800",
                                "mode": "tags",
                                "wrapperClasses": "relative ps-0.5 pe-9 min-h-[46px] flex items-center flex-wrap text-nowrap w-full border-t-0 border-r-0 border-l-0 border-b-2 border-primary-color rounded-lg text-start text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-secondary dark:border-primary-color dark:text-neutral-400",
                                "tagsItemTemplate": "<div class=\"flex flex-nowrap items-center relative z-10 bg-white border border-gray-200 rounded-full p-1 m-1 dark:bg-secondary dark:border-primary-color \"><div class=\"size-6 me-1\" data-icon></div><div class=\"whitespace-nowrap text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"inline-flex shrink-0 justify-center items-center size-5 ms-2 rounded-full text-gray-800 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 text-sm dark:bg-neutral-700/50 dark:hover:bg-neutral-700 dark:text-neutral-400 cursor-pointer\" data-remove><svg class=\"shrink-0 size-3\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 6 6 18\"/><path d=\"m6 6 12 12\"/></svg></div></div>",
                                "tagsInputId": "hs-tags-input-equipes",
                                "tagsInputClasses": "py-3 px-2 rounded-lg order-1 text-sm outline-none dark:bg-secondary dark:placeholder-neutral-500 text-neutral-400",
                                "optionTemplate": "<div class=\"flex items-center\"><div><div class=\"text-sm font-semibold text-gray-800 dark:text-neutral-200 \" data-title></div><div class=\"text-xs text-gray-500 dark:text-neutral-500 \" data-description></div></div><div class=\"ms-auto\"><span class=\"hidden hs-selected:block\"><svg class=\"shrink-0 size-4 text-blue-600\" xmlns=\"http://www.w3.org/2000/svg\" width=\"16\" height=\"16\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/></svg></span></div></div>",
                                "extraMarkup": "<div class=\"absolute top-1/2 end-3 -translate-y-1/2\"><svg class=\"shrink-0 size-3.5 text-gray-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"m7 15 5 5 5-5\"/><path d=\"m7 9 5-5 5 5\"/></svg></div>"
                            }' class="hidden">
                            @foreach ($equipes as $equipe)
                                <option value="{{ $equipe->id }}"
                                    {{ in_array($equipe->id, $site->equipes->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $equipe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="my-10 flex">
                    <x-secondary-button class="flex mr-4 justify-center shadow-md">
                        <a href="{{ route('site_rotation.voireSite', $site->id) }}">
                            {{ __('Annuler') }}
                        </a>
                    </x-secondary-button>

                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Mettre à jour') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .hs-select [data-remove] {
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 24px !important;
            height: 24px !important;
            border-radius: 50% !important;
            background-color: #e5e7eb !important;
            transition: background-color 0.2s ease-in-out !important;
        }

        .hs-select [data-remove]:hover {
            background-color: #d1d5db !important;
        }

        .hs-select [data-remove] svg {
            width: 14px !important;
            height: 14px !important;
            stroke: #374151 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.equipe-checkbox');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const equipeUsersSelect = checkbox.closest('.flex-col').querySelector('.equipe-users-select');

                    // Si la case est cochée, afficher la liste déroulante des utilisateurs
                    if (checkbox.checked) {
                        equipeUsersSelect.classList.remove('hidden');
                    } else {
                        equipeUsersSelect.classList.add('hidden');
                    }
                });
            });
        });
    </script>

</x-app-layout>
