@php
    use Carbon\Carbon;
@endphp

<x-app-layout>

    <style>

    </style>


    <div class="md:w-9/12  2xl:w-8/12 px-6 pt-10 pb-20 mx-auto">
        <!-- content -->

        <div class="flex flex-wrap -mx-3">
            <div class="max-w-full px-3 lg:w-2/3 lg:flex-none">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 mb-4 xl:mb-0 xl:w-2/3 xl:flex-none">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-transparent border-0 border-transparent border-solid shadow-xl rounded-2xl bg-clip-border">
                            <div class="relative overflow-hidden rounded-2xl"
                                style="background-image: url('../assets/img/curved-images/curved14.jpg')">
                                <span
                                    class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-80"></span>
                                <div class="relative z-10 flex-auto p-4">
                                    <i class=" p-2 text-white fa-solid fa-location-arrow"></i>
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="mb-0 leading-normal text-white text-sm opacity-80">Nom du site
                                            </p>
                                            <h5 class="pb-2 mt-2 mb-5 text-white">
                                                {{ $site->name }}</h5>
                                        </div>
                                        <div>
                                            <p class="mb-0 leading-normal text-white text-sm opacity-80">Statut
                                            </p>
                                            <h5 class="pb-2 mt-2 mb-5 text-white uppercase font-bold">
                                                {{ __($site->status) }}</h5>
                                        </div>
                                    </div>
                                    <div class="mr-6 mb-2">
                                        <p class="mb-0 leading-normal text-white text-sm opacity-80">Date de création
                                        </p>
                                        <h6 class="mb-0 text-white">{{ $site->created_at }}</h6>
                                    </div>

                                    <div class="mr-6">
                                        <p class="mb-0 leading-normal text-white text-sm opacity-80">Description
                                        </p>
                                        <h6 class="mb-0 text-white">{{ $site->description }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Client --}}
                    <div class="w-full max-w-full px-3 xl:w-1/3 xl:flex-none">
                        <div class="w-full max-w-full px-3 mt-6 md:mt-0 md:flex-none -mx-3">
                            <div
                                class="relative flex flex-col min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                                <div class="text-center py-5">
                                    <p class="mb-3">Client</p>
                                    <img class="rounded-full object-cover shadow size-14 mx-auto"
                                        src="{{ asset('storage/' . $site->customer->avatar) }}" alt="Avatar">
                                    <div class="mt-2 sm:mt-4">
                                        <a href="{{ route('users.show', $site->customer->id) }}">
                                            <h3 class="text-sm font-semibold">
                                                {{ $site->customer->name }} <br>
                                                <span>{{ $site->customer->first_name }}</span>
                                            </h3>
                                        </a>
                                        <p class="text-xs  sm:text-sm text-neutral-400">
                                            {{ __($site->customer->user_type) }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- Carte --}}
                    <div class="max-w-full px-3 mb-4 lg:mb-0 w-full lg:flex-none">
                        <div
                            class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                            <div
                                class="p-4 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="flex items-center flex-none w-1/2 max-w-full px-3">
                                        <h6 class="mb-0">Carte</h6>
                                    </div>

                                </div>
                            </div>
                            <div class="flex-auto p-4">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="max-w-full px-3 mb-6 md:mb-0 md:flex-none" id="map"
                                        style="height: 35vh; width: 100%;">

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations --}}
            <div class="w-full max-w-full lg:w-1/3 lg:flex-none">
                <div class="w-full max-w-full px-3">
                    <color:div
                        class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                        <div
                            class="p-4 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                            <div class="flex flex-wrap -mx-3">
                                <div class="flex items-center flex-none w-1/2 max-w-full px-3">
                                    <h6 class="mb-0">Informations</h6>
                                </div>

                            </div>
                        </div>
                        <div class="flex-auto p-4 pb-0">
                            <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                                {{-- <li
                                    class="relative flex justify-between px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-t-inherit text-inherit rounded-xl">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Heure de début</h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">
                                        <i class="fa-regular fa-clock text-base mt-1 mr-1"></i>
                                        {{ $site->start_time }}
                                    </div>
                                </li>
                                <li
                                    class="relative flex justify-between px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Heure de fin</h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">
                                        <i class="fa-regular fa-clock text-base mt-1 mr-1"></i>
                                        {{ $site->end_time }}
                                    </div>
                                </li> --}}
                                {{-- <li
                                    class="relative flex justify-between px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Marquage tardif
                                            après
                                            (minutes)</h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center  align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">

                                        {{ $site->late_mark_duration }} minute(s)
                                    </div>
                                </li> --}}
                                <li
                                    class="relative flex justify-between px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Adresse </h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">
                                        <i class="fa-solid fa-location-dot mt-1 mr-1"></i>
                                        {{ $site->address }}
                                    </div>
                                </li>
                                <li
                                    class="relative flex justify-between px-4 py-2 pl-0 bg-white border-0 rounded-b-inherit rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Rayon (en mètre)</h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">

                                        {{ $site->radius }}
                                    </div>
                                </li>
                                <li
                                    class="relative flex justify-between px-4 py-2 pl-0 bg-white border-0 rounded-b-inherit rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Latitude </h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">

                                        {{ $site->latitude }}
                                    </div>
                                </li>
                                <li
                                    class="relative flex justify-between px-4 py-2 pl-0 bg-white border-0 rounded-b-inherit rounded-xl text-inherit">
                                    <div class="flex flex-col">
                                        <h6 class="mb-1 font-semibold leading-normal text-slate-700">Longitude </h6>

                                    </div>
                                    <div
                                        class="inline-block px-0 mb-0 ml-6 font-bold leading-normal text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">

                                        {{ $site->longitude }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </color:div>
                </div>

                <div class="w-full max-w-full my-4 px-3">
                    <color:div
                        class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border">
                        <div
                            class="p-4 pb-0 mb-0 bg-white border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
                            <div class="flex flex-wrap -mx-3">
                                <div class="flex items-center flex-none w-1/2 max-w-full px-3">
                                    <h6 class="mb-0">Qrcode du site</h6>
                                </div>

                            </div>
                        </div>
                        <div class="flex-auto p-4  text-center items-center mx-auto">
                            {{ QrCode::size(200)->generate($site->qr_code) }}
                        </div>
                    </color:div>
                </div>
            </div>
        </div>


        <div
            class="relative my-5 w-full flex p-4 flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">



            <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl ">
                <h6 class="mb-3 font-semibold">Horaire de travail</h6>
                <div class="relative flex px-4 py-2 pl-0 mb-2 bg-white border-0 rounded-xl text-inherit">
                    <div class="flex flex-col">
                        <p class="mb-1 font-semibol leading-normal text-slate-700">Marquage tardif
                            après
                            (minutes) :</p>

                    </div>
                    <div
                        class="inline-block px-0 mb-0 ml-6 leading-normal text-center  align-middle transition-all bg-transparent border-0 rounded-lg shadow-none bg-150 text-sm bg-x-25 text-slate-700">

                        {{ $site->late_mark_duration }} minute(s)
                    </div>
                </div>
            </div>





            <div class="mt-6 border-t border-gray-100">
                <dl class="divide-y divide-gray-100">
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm font-semibold text-gray-900">{{ __($day) }}</dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">
                                {{ __('Heure de début') }} : <span class="ms-3">
                                    {{ $site->{'start_time_' . strtolower($day)} ?? '-' }}</span>
                                <br> <br>
                                {{ __('Heure de fin') }} : <span class="ms-3">
                                    {{ $site->{'end_time_' . strtolower($day)} ?? '-' }} </span>
                            </dd>
                        </div>
                    @endforeach

                </dl>
            </div>












        </div>

        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 mt-6 md:w-7/12 md:flex-none">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                        <h6 class="mb-0">Employés admis sur le site</h6>
                        <p class="mb-0 leading-normal  text-sm opacity-80">Total : {{ $site->users->count() }}</p>
                    </div>

                    <div class="flex-auto p-4 pt-6">
                        <div
                            class="grid grid-cols-2 lg:grid-cols-3 gap-8 md:gap-4 border-0 rounded-t-inherit rounded-xl bg-gray-50 p-4">
                            @foreach ($site->users as $user)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                                    <img class="rounded-full object-cover shadow size-14"
                                        src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">

                                    <div class="grow">
                                        <div>
                                            <a href="{{ route('users.show', $user->id) }}">
                                                <p class="text-sm font-semibold text-gray-500">
                                                    {{ $user->name }} <br>
                                                    <span>{{ $user->first_name }}</span>
                                                </p>
                                            </a>
                                            <p class="mt-1 text-xs uppercase text-gray-500">
                                                {{ __($user->user_type) }}
                                            </p>
                                        </div>


                                    </div>
                                </div>
                                <!-- End Col -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full max-w-full px-3 mt-6 md:w-5/12 md:flex-none">
                <div
                    class="relative flex flex-col h-full min-w-0 mb-6 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                        <h6 class="mb-0">Contrôleurs</h6>
                        <p class="mb-0 leading-normal  text-sm opacity-80">Total : {{ $site->controller->count() }}
                        </p>
                    </div>
                    <div class="flex-auto p-4 pt-6">
                        <div
                            class="grid grid-cols-2 gap-8 md:gap-4 border-0 rounded-t-inherit rounded-xl bg-gray-50 p-4">
                            @foreach ($site->controller as $user)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                                    <img class="rounded-full object-cover shadow size-14"
                                        src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar">

                                    <div class="grow">
                                        <div>
                                            <a href="{{ route('users.show', $user->id) }}">
                                                <p class="text-sm font-semibold text-gray-500">
                                                    {{ $user->name }} <br>
                                                    <span>{{ $user->first_name }}</span>
                                                </p>
                                            </a>
                                            <p class="mt-1 text-xs uppercase text-gray-500">
                                                {{ __($user->user_type) }}
                                            </p>
                                        </div>


                                    </div>
                                </div>
                                <!-- End Col -->
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCryF2Z5jtIq3WhUU_OW2_Fcwhn6CcIOUw"></script>
    <script>
        function initMap() {
            // Remplacez par vos coordonnées
            const coords = {
                lat: {{ $site->latitude }},
                lng: {{ $site->longitude }}
            }; // Paris, France
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: coords,
            });

            // Ajout d'un marqueur
            const marker = new google.maps.Marker({
                position: coords,
                map: map,
                title: "Localisation choisie",
            });
        }

        window.onload = initMap;
    </script>

</x-app-layout>
