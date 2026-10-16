<x-app-layout>
    <div class="md:w-9/12 2xl:w-8/12 mx-auto">
        <div class="my-10 px-6 py-6">
            <!-- Titre -->
            <div class="px-6">
                <h1 class="mb-2 ml-2 font-bold text-black text-2xl">
                    {{ __('Modifier la cotisation') }}
                </h1>
            </div>

            <form method="POST" action="{{ route('cotisations-sociales.update', $cotisationSociale) }}">
                @csrf
                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                        <strong>Erreurs :</strong>
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @include('cotisations._form')
            </form>
        </div>
    </div>
</x-app-layout>
