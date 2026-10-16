
<x-app-layout>
    <br><br><br>
    <div class="md:w-9/12 2xl:w-7/12 mx-auto">
        <div class="px-6">
            <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                {{ __('Génération Fiche de Paie - Rotatif') }}
            </h1>
        </div>

        {{-- Messages de session --}}
        @if (session('success'))
            <div class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-primary">
                {{ session('success') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4">
                    <span aria-hidden="true">&#10005;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="relative p-4 pr-12 my-4 text-white border border-solid rounded-lg bg-danger">
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 p-4 text-sm text-white bg-transparent border-0 rounded w-4 h-4">
                    <span aria-hidden="true">&#10005;</span>
                </button>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="my-10 px-6 py-6">
            <form action="{{ route('fichepaie.genererRotatif') }}" method="post">
                @csrf
                <input type="hidden" name="company_id" value="{{ Auth::user()->company_id }}" required>

                <div class="md:grid grid-cols-2 gap-16 mt-7">
                    <div>
                        <label for="mois" class="col-form-label text-md-end">Mois</label>
                        <x-select-input id="mois" name="mois" :options="[
                            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
                            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
                            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
                            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                        ]" :selected="now()->format('m')" required />
                    </div>

                    <div>
                        <x-input-label for="annee" :value="__('Année *')" />
                        <x-text-input id="annee" name="annee" type="number" min="{{ now()->year }}" max="{{ now()->year }}" :value="now()->year" required />
                    </div>
                </div>

                <div class="my-19 flex mt-8">
                    <x-secondary-button class="flex mr-4 justify-center shadow-md">
                        <a href="{{ route('fichepaie.rotatif') }}">
                            {{ __('Retour à la liste') }}
                        </a>
                    </x-secondary-button>
                    <x-primary-button class="flex ml-4 justify-center shadow-md shadow-green-600/50">
                        {{ __('Générer les fiches rotatives') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
