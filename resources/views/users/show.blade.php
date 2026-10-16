@php
    use Carbon\Carbon;
@endphp

<x-app-layout>

    <!-- cards -->
    <div class="w-full mx-auto pb-20 bg-gray-50">

        <div class="ease-soft-in-out relative h-full  md:w-9/12  2xl:w-8/12 mx-auto transition-all duration-200">

            <div class="w-full lg:px-6 pt-10 mx-auto">
                <!-- Exemple : en haut de la fiche utilisateur -->
                {{-- @if($user->user_type=='user')
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('/users.badge', $user->id) }}" target="_blank"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">
                            Générer le badge
                        </a>
                    </div>
                @endif --}}

                <div class="relative flex flex-col flex-auto min-w-0 p-4 mx-6 overflow-hidden break-words border-0 shadow-blur rounded-2xl bg-white/80 bg-clip-border backdrop-blur-2xl backdrop-saturate-200">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex-none w-auto max-w-full px-3">
                            <div
                                class="text-base ease-soft-in-out h-18.5 w-18.5 relative inline-flex items-center justify-center rounded-xl text-white transition-all duration-200">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="profile_image"
                                    class="w-full shadow-soft-sm rounded-xl h-18.5 w-18.5 object-cover" />
                            </div>
                        </div>
                        <div class="flex-none w-auto max-w-full px-3 my-auto">
                            <div class="h-full">
                                <h5 class="mb-1  font-semibold">{{ $user->first_name ?? '-' }}
                                    <span class=" uppercase"> {{ $user->name ?? '-' }}</span>
                                </h5>
                                <p class="mb-0 font-semibold leading-normal ">
                                    {{ $user->company->compagny_name ?? '-' }} / {{ __($user->user_type) ?? '-' }}</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @if($user->user_type=='user')
                 <div class="flex justify-center items-center min-h-[200px]">
                    <div class="bg-white shadow-md rounded p-4">
                        @if (!is_null($user->matricule))
                            {!! QrCode::size(150)->generate($user->matricule) !!}
                        @else
                            <p class="text-red-500">Aucun matricule attribué à cet employé.</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="w-full py-6 px-3 lg:p-6 mx-auto">
                <div class="flex flex-wrap -mx-3">

                    <div class="flex w-full">
                        <div class="w-full max-w-full mt-4 px-3 lg-max:mt-6 ">
                            <div
                                class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                                    <div class="flex flex-wrap -mx-3">
                                        <div
                                            class="flex items-center w-full max-w-full px-3 shrink-0 md:w-8/12 md:flex-none">
                                            <h6 class="mb-0 font-semibold"> Informations sur le profil</h6>
                                        </div>

                                    </div>
                                </div>
                                <div class="flex-auto p-4">


                                    <ul class="flex flex-col pl-0 mt-5 mb-0 rounded-lg text-bas">

                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Nom:</span> &nbsp;
                                            {{ $user->name ?? '-' }}
                                        </li>
                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Prénoms:</span>
                                            &nbsp;
                                            {{ $user->first_name ?? '-' }}
                                        </li>
                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Numéro Social (SS)
                                                :</span> &nbsp;
                                            {{ $user->n_social ?? '-' }}
                                        </li>
                                        {{-- @if ($user->user_type == 'user')
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Nom
                                                    d'utilisateur:</span> &nbsp;
                                                {{ $user->username ?? '-' }}
                                            </li>
                                        @endif --}}

                                        <li
                                            class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Email:</span>
                                            &nbsp;
                                            {{ $user->email ?? '-' }}
                                        </li>

                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Téléphone:</span>
                                            &nbsp;
                                            {{ $user->phone ?? '-' }}
                                        </li>

                                        <li
                                            class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Adresse</span>
                                            &nbsp;
                                            {{ $user->address ?? '-' }}
                                        </li>
                                        @if ($user->user_type == 'user')
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Taux
                                                    salarial:</span> &nbsp;
                                                {{ $user->salary_rate ?? '-' }}
                                            </li>
                                        @endif

                                        @if ($user->user_type != 'customer')
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Statut
                                                    :</span>
                                                &nbsp;
                                                {{ $user->status ?? '-' }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if ($user->user_type == 'user')
                            <div class="w-full max-w-full mt-4 px-3 lg-max:mt-6 ">
                                <div
                                    class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                                    <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                                        <div class="flex flex-wrap -mx-3">
                                            <div
                                                class="flex items-center w-full max-w-full px-3 shrink-0 md:w-8/12 md:flex-none">
                                                <h6 class="mb-0 font-semibold"> Caractéristiques & Pièce
                                                    d'identité</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-auto p-4">
                                        <ul class="flex flex-col pl-0 mt-5 mb-0 rounded-lg text-base">
                                            <li
                                                class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Fonction de
                                                    l'employé:</span> &nbsp;
                                                {{ $user->fonction->nom_fonction ?? '-' }}
                                            </li>
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span
                                                    class="text-slate-700 leading-normal font-semibold">Qualification:</span>
                                                &nbsp;
                                                {{ $user->qualification ?? '-' }}
                                            </li>
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Quotient de
                                                    l'employé :</span> &nbsp;
                                                {{ $user->employee_quotient ?? '-' }}
                                            </li>
                                            <li
                                                class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Type de pièce
                                                    d'identité:</span> &nbsp;
                                                {{ __($user->identity_document_type) ?? '-' }}
                                            </li>
                                            <li
                                                class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                                <span class="text-slate-700 leading-normal font-semibold">Numéro de la
                                                    pièce
                                                    d'identité:</span>
                                                &nbsp;
                                                {{ $user->id_number ?? '-' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="w-full max-w-full mt-4 px-3 lg-max:mt-6 ">
                        <div
                            class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                                <div class="flex flex-wrap -mx-3">
                                    <div
                                        class="flex items-center w-full max-w-full px-3 shrink-0 md:w-8/12 md:flex-none">
                                        <h6 class="mb-0 font-semibold">Contact d'urgence</h6>
                                    </div>

                                </div>
                            </div>
                            <div class="flex-auto p-4">

                                <ul class="flex flex-col pl-0 mt-5 mb-0 rounded-lg text-base">
                                    <li
                                        class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Nom et prenoms:</span>
                                        &nbsp;
                                        {{ $user->emergency_contact_name ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Email:</span> &nbsp;
                                        {{ $user->emergency_contact_email ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Adresse:</span> &nbsp;
                                        {{ $user->emergency_contact_address ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Mobile:</span> &nbsp;
                                        {{ __($user->emergency_contact_phone) ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Relation:</span>
                                        &nbsp;
                                        {{ $user->emergency_contact_relationship ?? '-' }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($user->user_type == 'user')

                    <div
                        class="relative my-5 w-full flex p-4 flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">



                        <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl ">
                            <h6 class="mb-3 font-semibold">Horaire de travail personnalisé</h6>

                        </div>

                        <div class="mt-6 border-t border-gray-100 flex justify-between">
                            <dl class="divide-y divide-gray-100 w-full">
                                @foreach (['Monday', 'Wednesday', 'Friday', 'Sunday'] as $day)
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-semibold text-gray-900">{{ __($day) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">
                                            {{ __('Heure de début') }} : <span class="ms-3">
                                                {{ $user->{'start_time_' . strtolower($day)} ?? '-' }}</span>
                                            <br> <br>
                                            {{ __('Heure de fin') }} : <span class="ms-3">
                                                {{ $user->{'end_time_' . strtolower($day)} ?? '-' }} </span>
                                        </dd>
                                    </div>
                                @endforeach

                            </dl>
                            <dl class="divide-y divide-gray-100 w-full">
                                @foreach (['Tuesday', 'Thursday', 'Saturday'] as $day)
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-semibold text-gray-900">{{ __($day) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-700 sm:col-span-2 sm:mt-0">
                                            {{ __('Heure de début') }} : <span class="ms-3">
                                                {{ $user->{'start_time_' . strtolower($day)} ?? '-' }}</span>
                                            <br> <br>
                                            {{ __('Heure de fin') }} : <span class="ms-3">
                                                {{ $user->{'end_time_' . strtolower($day)} ?? '-' }} </span>
                                        </dd>
                                    </div>
                                @endforeach

                            </dl>

                        </div>
                    </div>
                    <div class="flex-none w-full max-w-full px-3 mt-6">
                        <div
                            class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="p-4 pb-0 mb-0 bg-white rounded-t-2xl">
                                <h6 class="mb-1 font-semibold">Présences</h6>
                                <p class="leading-normal ">Tableau des Pointages</p>
                            </div>
                            <div class="flex-auto p-4">
                                <div class=" -mx-3 container mb-20">

                                    <!-- Formulaire de filtrage -->
                                    <form method="GET" action="{{ route('users.show', $user->id) }}"
                                        class="flex space-x-4 my-10">
                                        <select name="month" class="p-2 border rounded capitalize">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ $month == $i ? 'selected' : '' }}>
                                                    {{ Carbon::create()->month($i)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>

                                        <select name="year" class="p-2 border rounded">
                                            @foreach (range(now()->year - 5, now()->year + 5) as $yearOption)
                                                <option value="{{ $yearOption }}"
                                                    {{ $year == $yearOption ? 'selected' : '' }}>
                                                    {{ $yearOption }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <x-primary-button class="flex ml-4 justify-center shadow-md text-sm">
                                            {{ __('Filtrer') }}
                                        </x-primary-button>
                                    </form>

                                    <div class="flex flex-wrap my-5">
                                        <div class="flex justify-between items-center">
                                            <i class="fa-solid fa-check text-green-500 "></i> <i
                                                class="fa-solid fa-arrow-right-long mx-4"></i> <span
                                                class="text-black font-semibold">Présent</span>
                                        </div>

                                        <div class="flex justify-between items-center mx-5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-exclamation-circle  text-yellow-500"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                            </svg> <i class="fa-solid fa-arrow-right-long mx-4"></i> <span
                                                class="text-black font-semibold">En retard</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <i class="fa-solid fa-xmark "></i> <i
                                                class="fa-solid fa-arrow-right-long mx-4"></i> <span
                                                class="text-black font-semibold">Absent</span>
                                        </div>
                                    </div>

                                    <!-- Tableau des pointages -->

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full table-auto border-collapse">
                                            <thead>
                                                <tr>
                                                    <th class="border-y border-l p-2 text-left text-xs font-semibold">
                                                        Nom</th>
                                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                                        @php
                                                            $currentDay = Carbon::create($year, $month, $i);
                                                            $isSunday = $currentDay->dayOfWeek === Carbon::SUNDAY;
                                                            $dayName = $currentDay->translatedFormat('D');
                                                            $isDay = $currentDay->isSameDay(Carbon::today());

                                                        @endphp
                                                        <th
                                                            class="border-y p-2 text-center text-xs font-semibold  {{ $isDay ? 'bg-green-500 text-white' : '' }} {{ $isSunday ? 'bg-red-100 text-red-500' : 'text-black' }}">
                                                            <div>{{ $i }}</div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ strtolower($dayName) }}</div>
                                                        </th>
                                                    @endfor
                                                    <th
                                                        class="border-y border-r p-2 text-center text-xs font-semibold">
                                                        Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td class="border-b border-l p-2 text-xs font-semibold">
                                                        {{ $user->name }}</td>

                                                    @php
                                                        $workedDays = 0;
                                                    @endphp

                                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                                        <td class="border-b p-2 text-center">
                                                            @php
                                                                $attendance = $attendances[$user->id][$i] ?? null;
                                                                $currentDate = Carbon::create($year, $month, $i);
                                                                $today = now();
                                                            @endphp

                                                            <!-- Affichage des statuts -->
                                                            @if ($currentDate->isFuture())
                                                                <!-- Jour à venir -->
                                                                <span class="text-gray-400"> -</span>
                                                            @elseif ($attendance)
                                                                @if ($attendance['status'] == 'present' || $attendance['status'] == 'late')
                                                                    @php
                                                                        $workedDays++; // Compter les jours travaillés (présent ou en retard)
                                                                    @endphp
                                                                    @if ($attendance['status'] == 'present')
                                                                        <a href="{{ route('attendances.show', ['user' => $user->id, 'day' => $i, 'month' => $month, 'year' => $year]) }}"
                                                                            class="text-green-500">
                                                                            <i
                                                                                class="fa-solid fa-check text-green-500"></i>
                                                                        </a>
                                                                    @elseif ($attendance['status'] == 'late')
                                                                        <a href="{{ route('attendances.show', ['user' => $user->id, 'day' => $i, 'month' => $month, 'year' => $year]) }}"
                                                                            class="text-yellow-500">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="16" height="16"
                                                                                fill="currentColor"
                                                                                class="bi bi-exclamation-circle text-yellow-500"
                                                                                viewBox="0 0 16 16">
                                                                                <path
                                                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                                                <path
                                                                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                                                            </svg>
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <span><i
                                                                            class="fa-solid fa-xmark text-xs"></i></span>
                                                                @endif
                                                            @else
                                                                <!-- Absent -->
                                                                <span><i class="fa-solid fa-xmark text-xs"></i></span>
                                                            @endif
                                                        </td>
                                                    @endfor

                                                    <!-- Afficher le total des jours travaillés -->
                                                    <td
                                                        class="border-b border-r p-2 text-center text-xs font-semibold">
                                                        <span class="text-black">{{ $workedDays }}</span> /
                                                        {{ $daysInMonth }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                @endif


                @if ($user->user_type == 'customer' || $user->user_type == 'controller' || $user->user_type == 'user')
                    <div class="flex-none w-full max-w-full px-3 mt-6">
                        <div
                            class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="p-4 pb-0 mb-0 bg-white rounded-t-2xl">
                                <h6 class="mb-1 font-semibold">Lieux de travail actif</h6>
                            </div>
                            <div class="flex-auto p-4">
                                <div class=" -mx-3 container mb-20">
                                    <div class="flex flex-wrap -mx-3">
                                        <div
                                            class="flex-none w-full max-w-full px-3 mb-20 2xl:overflow-visible overflow-x-auto ">
                                            <div class="bg-white">

                                                <table
                                                    class="min-w-full table-auto divide-y divide-gray-200 border border-separate rounded-lg "
                                                    id="export-table">
                                                    <thead class="align-bottom bg-gray-50 rounded-2xl">
                                                        <tr>

                                                            <th
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                                                {{ __('Nom du site') }}
                                                            </th>
                                                            <th
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                                                {{ __('Propriétaire') }}
                                                            </th>
                                                            <th
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                                                {{ __('Adresse') }}
                                                            </th>

                                                            <th
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                                                {{ __('Marquage tardif après') }}
                                                            </th>

                                                            <th
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                                                {{ __('Status') }}
                                                            </th>

                                                        </tr>
                                                    </thead>
                                                    <tbody class="">
                                                        @forelse($sites as $site)
                                                            <tr>

                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                                    <h6
                                                                        class="mb-0 ms-4 text-sm leading-normal font-semibold w-40 truncate">
                                                                        <a
                                                                            href="{{ route('sites.show', $site->id) }}">
                                                                            {{ $site->name ?? '' }}
                                                                        </a>
                                                                    </h6>

                                                                </td>
                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                                    <p
                                                                        class="mb-0 font-semibold text-sm leading-normal w-40 truncate">
                                                                        {{ $site->customer->name ?? '-' }}
                                                                    </p>
                                                                    </p>
                                                                </td>
                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                                    <p
                                                                        class="mb-0 font-semibold text-sm leading-normal w-40 truncate">
                                                                        {{ $site->address ?? '-' }}
                                                                    </p>
                                                                </td>
                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                                    <p
                                                                        class="mb-0 font-semibold text-sm leading-normal w-40 truncate">
                                                                        {{ $site->late_mark_duration ?? '-' }} min
                                                                    </p>
                                                                </td>


                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                                    <button type="button"
                                                                        class="inline-block text-sm py-1 px-6 font-bold text-center pointer-events-none capitalize align-middle border rounded-md
                                                                        {{ $site->status == 'active' ? 'text-primary bg-primary-2 border-green-700' : ' text-danger bg-danger-2 border-red-700' }}
                                                                         ">
                                                                        {{ __($site->status) ?? '-' }}
                                                                    </button>


                                                                </td>


                                                            </tr>

                                                        @empty
                                                            <tr>
                                                                <td
                                                                    class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent w-40 truncate">
                                                                    {{ __('No Content') }}
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 justify-end">
                                        {{ $sites->links() }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <div
                    class="relative my-5 w-full flex p-4 flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">



                    <div class="p-6 px-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl ">
                        <h6 class="mb-3 font-semibold">Motifs d'activation / désactivation</h6>

                    </div>

                    <div class="flex flex-col">
                        <div class="-m-1.5 overflow-x-auto">
                            <div class="p-1.5 min-w-full inline-block align-middle">
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                    Date</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                    Statut</th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">
                                                    Motif</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($userDeactivationReasons as $userDeactivationReason)
                                                <tr class="odd:bg-white even:bg-gray-100">
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                        {{ $userDeactivationReason->created_at }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                        {{ $userDeactivationReason->status }}</td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 whitespace-pre-wrap">
                                                        {{ $userDeactivationReason->reason }}
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
    <!-- end cards -->

</x-app-layout>
