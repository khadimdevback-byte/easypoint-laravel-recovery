<x-app-layout>

    <div class="md:w-9/12  2xl:w-8/12 mx-auto">
        <div class=" px-6 mt-10">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">

                {{ __('Profil') }}
                </h6>
        </div>
        <div class="">
            @if (session('success'))
                <div id="session-success-alert-div" alert
                    class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-primary">
                    {{ session('success') }}
                    <button type="button" alert-close
                        onclick="document.getElementById('session-success-alert-div').style.display='none'"
                        class="box-content absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4 z-2">
                        <span aria-hidden="true" class="text-center cursor-pointer">&#10005;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="session-error-alert-div" alert
                    class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-danger">
                    {{ session('error') }}
                    <button type="button" alert-close
                        onclick="document.getElementById('session-error-alert-div').style.display='none'"
                        class="box-content absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4 z-2">
                        <span aria-hidden="true" class="text-center cursor-pointer">&#10005;</span>
                    </button>
                </div>
            @endif
        </div>
        <div class="py-12">
            <div class="">
                <div class="mx-auto">
                    <div class="flex flex-wrap">
                        <div class="grid lg:grid-cols-2 gap-6 w-full">
                            <div class="p-4 sm:p-8 bg-white  shadow sm:rounded-lg">

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
                                            <span class="text-slate-700 leading-normal font-semibold">Nom:</span>
                                            &nbsp;
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
                                            <span class="text-slate-700 leading-normal font-semibold">Numéro
                                                Social
                                                (SS)
                                                :</span> &nbsp;
                                            {{ $user->n_social ?? '-' }}
                                        </li>
                                        {{-- <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Nom
                                                d'utilisateur:</span> &nbsp;
                                            {{ $user->email ?? '-' }}
                                        </li> --}}

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
                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Taux
                                                salarial:</span> &nbsp;
                                            {{ $user->salary_rate ?? '-' }}
                                        </li>
                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Statut
                                                :</span>
                                            &nbsp;
                                            {{ $user->status ?? '-' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="p-4 sm:p-8 bg-white  shadow sm:rounded-lg">
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
                                            <span class="text-slate-700 leading-normal font-semibold">Fonction
                                                de
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
                                            <span class="text-slate-700 leading-normal font-semibold">Quotient
                                                de
                                                l'employé :</span> &nbsp;
                                            {{ $user->employee_quotient ?? '-' }}
                                        </li>
                                        <li
                                            class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Type de
                                                pièce
                                                d'identité:</span> &nbsp;
                                            {{ __($user->identity_document_type) ?? '-' }}
                                        </li>
                                        <li
                                            class="relative block px-4 py-2 pt-0 pl-0 leading-normal bg-white border-0 rounded-t-lg  text-inherit">
                                            <span class="text-slate-700 leading-normal font-semibold">Numéro de
                                                la
                                                pièce
                                                d'identité:</span>
                                            &nbsp;
                                            {{ $user->id_number ?? '-' }}
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="p-4 sm:p-8 bg-white w-full my-5 shadow sm:rounded-lg">
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
                                        <span class="text-slate-700 leading-normal font-semibold">Nom et
                                            prenoms:</span>
                                        &nbsp;
                                        {{ $user->emergency_contact_name ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Email:</span>
                                        &nbsp;
                                        {{ $user->emergency_contact_email ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Adresse:</span>
                                        &nbsp;
                                        {{ $user->emergency_contact_address ?? '-' }}
                                    </li>
                                    <li
                                        class="relative block px-4 py-2 pl-0 leading-normal bg-white border-0 border-t-0  text-inherit">
                                        <span class="text-slate-700 leading-normal font-semibold">Mobile:</span>
                                        &nbsp;
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
                    <div class="p-4 sm:p-8 bg-white w-full my-5 shadow sm:rounded-lg">
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
                    <div class="flex-none w-full">
                        <div class="p-4 sm:p-8 bg-white w-full my-5 shadow sm:rounded-lg">
                            {{-- <div
                            class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border"> --}}

                            <div class=" bg-white rounded-t-2xl mb-4">
                                <h6 class="mb-1 font-semibold">Lieux de travail actif</h6>
                            </div>

                            <div class="flex-none w-full max-w-full mb-20 2xl:overflow-visible overflow-x-auto ">
                                <div class="bg-white xl:border border-gray-200">
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
                                                            {{ $site->name ?? '' }}
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
                            <div class="mt-6 justify-end">
                                {{ $sites->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
