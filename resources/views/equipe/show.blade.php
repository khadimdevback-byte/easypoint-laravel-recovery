<x-app-layout>
    <div class="w-full mx-auto">
        <!-- row 1 -->
        <div class="md:w-9/12  2xl:w-7/12 mx-auto">
            <br>
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    Employé associés à l'équipe: {{ $equipe->nom }}
                </h1>
            </div>

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

            <div class="mb-6 py-3">
                <!-- card 1 -->
                <div class="flex flex-wrap -mx-60">
                    <div class="flex-none w-full max-w-full px-3 pb-40 2xl:overflow-visible overflow-x-auto">
                        <div class="bg-white ">
                            <table
                                class="min-w-full table-auto divide-y divide-gray-200 border border-separate rounded-lg"
                                id="export-table">
                                <thead class="align-bottom bg-gray-50 rounded-2xl">
                                    <tr>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Nom Employé') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Numéros Social') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Email') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Status') }}
                                        </th>
                                        <th
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-800 px-6 py-3 text-start">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-black">
                                    @forelse($equipe->users as $user)
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $user->name ?? '-' }} &nbsp; {{ $user->first_name }}
                                                </p>
                                            </td>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $user->n_social ?? '-' }}
                                                </p>
                                            </td>

                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <p class="mb-0 font-semibold text-sm leading-normal">
                                                    {{ $user->email ?? '-' }}
                                                </p>
                                            </td>

                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <button type="button"
                                                    class="inline-block text-sm py-1 px-6 font-bold text-center pointer-events-none capitalize align-middle border rounded-md
                                                    {{ $user->status == 'active' ? 'text-primary bg-primary-2 border-green-700' : ' text-danger bg-danger-2 border-red-700' }}">
                                                    {{ __($user->status) ?? '-' }}
                                                </button>
                                            </td>

                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                <!-- Formulaire de détachement de l'employé -->
                                                <form
                                                    action="{{ route('equipes.detachEmployee', ['equipeId' => $equipe->id, 'employeeId' => $user->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-500 hover:text-red-700 text-sm">
                                                        Détacher
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                {{ __('Pas d\'employé') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6 justify-end">
                    {{-- {{ $fonction->links() }} --}}
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
