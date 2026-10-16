<x-app-layout>
    <div class="w-full mx-auto">
        <!-- row 1 -->
        <div class="">
            <!-- card1 -->
            <div class="w-full max-w-full px-3 mb-6">
                <div class="bg-primary text-white">
                    <div class="flex-auto">
                        @if (is_admin())
                            <div class="flex flex-row -mx-3 flex-wrap justify-center">
                                <a href="{{ route('rotation.create') }}" id="add_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Ajouter') }}
                                    <svg width="18" height="18" class="mt-1 ms-2" viewBox="0 0 18 18"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.6187 16.4259C7.6187 16.7809 7.75973 17.1214 8.01077 17.3724C8.26181 17.6234 8.60229 17.7645 8.95731 17.7645C9.31233 17.7645 9.6528 17.6234 9.90384 17.3724C10.1549 17.1214 10.2959 16.7809 10.2959 16.4259V10.6253H16.0965C16.4515 10.6253 16.792 10.4842 17.0431 10.2332C17.2941 9.98215 17.4351 9.64167 17.4351 9.28665C17.4351 8.93163 17.2941 8.59115 17.0431 8.34012C16.792 8.08908 16.4515 7.94805 16.0965 7.94805H10.2959V2.14744C10.2959 1.79242 10.1549 1.45194 9.90384 1.20091C9.6528 0.949869 9.31233 0.808838 8.95731 0.808838C8.60229 0.808838 8.26181 0.949869 8.01077 1.20091C7.75973 1.45194 7.6187 1.79242 7.6187 2.14744V7.94805H1.81809C1.46307 7.94805 1.1226 8.08908 0.87156 8.34012C0.620523 8.59115 0.479492 8.93163 0.479492 9.28665C0.479492 9.64167 0.620523 9.98215 0.87156 10.2332C1.1226 10.4842 1.46307 10.6253 1.81809 10.6253H7.6187V16.4259Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                                <a href="#" id="delete_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Delete') }}
                                    <svg width="14" height="16" class="mt-1 ms-2" viewBox="0 0 14 16"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.4268 5.50681V14.1416H3.16007V5.50681H10.4268ZM9.06426 0.325928H4.52258L3.61424 1.18941H0.435059V2.91637H13.1518V1.18941H9.9726L9.06426 0.325928ZM12.2434 3.77985H1.3434V14.1416C1.3434 15.0915 2.1609 15.8686 3.16007 15.8686H10.4268C11.4259 15.8686 12.2434 15.0915 12.2434 14.1416V3.77985Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                                <a href="#" id="edit_button"
                                    class="px-6 py-1 mr-3 flex text-center align-middle cursor-pointer hover:font-bold">
                                    {{ __('Edit') }}
                                    <svg width="19" height="19" class="mb-1 ms-2" viewBox="0 0 19 19"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.19281 16.8276H3.64699L13.6222 6.85247L12.168 5.39829L2.19281 15.3735V16.8276ZM0.151855 18.8686V14.5316L13.6222 1.08677C13.8262 0.899687 14.0518 0.755119 14.2987 0.653072C14.5457 0.551024 14.8049 0.5 15.0763 0.5C15.3478 0.5 15.6114 0.551024 15.8672 0.653072C16.123 0.755119 16.3441 0.908191 16.5305 1.11229L17.9337 2.54096C18.1378 2.72804 18.2868 2.94915 18.3806 3.20427C18.4745 3.45938 18.5211 3.7145 18.5204 3.96962C18.5204 4.24175 18.4738 4.50129 18.3806 4.74825C18.2874 4.9952 18.1384 5.22039 17.9337 5.4238L4.48888 18.8686H0.151855ZM12.8823 6.13814L12.168 5.39829L13.6222 6.85247L12.8823 6.13814Z"
                                            fill="#FAFFFC" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <br>
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Rotation et Site') }}
                </h1>
            </div>


            <div class="flex flex-wrap -mx-60">
                <div class="flex-none w-full max-w-full px-3 pb-40 2xl:overflow-visible overflow-x-auto">
                    <div class="bg-white ">
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border p-2">Site</th>
                                    <th class="border p-2">Rotation</th>
                                    <th class="border p-2">Équipes Assignées</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sites as $site)
                                    @foreach ($site->equipes->groupBy(function ($equipe) {
                                            return $equipe->rotations->pluck('id')->implode(',');
                                        }) as $rotationIds => $equipes)
                                        @php
                                            $rotation = $equipes->first()->rotations->first();
                                        @endphp
                                        <tr>
                                            <td class="border p-2">{{ $site->name }}</td>
                                            <td class="border p-2">{{ $rotation->nom ?? 'Aucune Rotation' }}</td>
                                            <td class="border p-2">
                                                @foreach ($equipes as $equipe)
                                                    {{ $equipe->nom }},
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('rotations.calendrier', ['site_id' => $site->id, 'rotation_id' => $rotation->id ?? null]) }}"
                                                    class="btn btn-primary">
                                                    Voir le calendrier
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
