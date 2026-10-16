<x-app-layout>
    <div class="flex justify-center bg-gray-100 min-h-screen py-10">
        <div style="zoom: 2;">
            @include('components.badge', ['user' => $user])
        </div>
    </div>
</x-app-layout>

