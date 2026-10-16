<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Easy Point') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-black antialiased bg-gray-100 flex justify-center items-center h-screen">
    <div class="flex flex-col justify-center items-center w-full mb-10">

        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <x-application-logo class=" fill-current text-black" />
            </a>
        </div>

        <!-- Conteneur principal -->
        <div class="grid md:grid-cols-2 w-5/6 xl:w-4/6 bg-white rounded-[20px] p-0 lg:p-5 xl:p-10 shadow-lg">

            <!-- Formulaire -->
            <div class="col-span-1 flex justify-center items-center px-6 py-8" >
                <div class="w-full">
                    <h1 class="text-center font-semibold text-black mt-0 mb-5"
                        >
                        {{ __('Connectez-vous à votre compte') }}
                    </h1>

                    <div>
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <!-- Image à droite -->
            <div class="bg-loginpana col-span-1 hidden md:block"></div>
        </div>

    </div>
</body>


</html>
