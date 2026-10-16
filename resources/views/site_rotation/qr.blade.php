<x-app-layout>
    <div class="w-full mx-auto">
        <!-- row 1 -->
        <div class="">
            <!-- card1 -->
            <div class="w-full max-w-full px-3 mb-6">
                <h2 class="text-xl font-bold mb-4">QR Code du site : {{ $site->name }}</h2>
                <div class="mb-6">
                    {!! $qr !!}
                </div>
                <p><strong>Adresse:</strong> {{ $site->address }}</p>
                <p><strong>Coordonnées:</strong> {{ $site->latitude }}, {{ $site->longitude }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
