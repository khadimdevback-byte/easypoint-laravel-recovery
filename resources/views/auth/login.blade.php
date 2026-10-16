<x-guest-layout>
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="w-full mt-0 px-5 py-2">
        @csrf

        <!-- Adresse email -->
        <div class="mt-2">
            <label for="email" class="block font-medium text-sm text-gray-700">Adresse email</label>
            <div class="flex items-center mt-1 gap-2">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-[#2e7d64]/30 focus:border-[#2e7d64] transition-all duration-200">

                <img src="{{ asset('images/email-login.png') }}" alt="Email" class="w-10 h-10 opacity-80">
            </div>
            @if(isset($errors) && $errors->has('email'))
                <p class="text-red-500 text-sm my-2">{{ $errors->first('email') }}</p>
            @endif
        </div>

        <!-- Mot de passe -->
        <div class="mt-3">
            <label for="password" class="block font-medium text-sm text-gray-700">Mot de passe</label>
            <div class="flex items-center mt-1 gap-2">
                <input id="password" type="password" name="password"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-[#2e7d64]/30 focus:border-[#2e7d64] transition-all duration-200">

                <img src="{{ asset('images/password-login.png') }}" alt="Password" class="w-10 h-10 opacity-80">
            </div>
        </div>

        <!-- Mot de passe oublié -->
        <div class="mt-2 text-right">
            <a href="{{ route('password.request') }}" class="text-sm text-[#2e7d64] hover:text-[#1a5a48] hover:underline transition duration-200">
                Mot de passe oublié ?
            </a>
        </div>

        <!-- Bouton Connexion -->
        <div class="mt-4">
            <x-primary-button class="shadow-md w-full py-2.5 bg-[#2e7d64] hover:bg-[#1a5a48] transition-all duration-200 rounded-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>
                {{ __('Connexion') }}
            </x-primary-button>
        </div>

        <!-- Lien vers l'accueil -->
        <div class="mt-4 text-center">
            <a href="{{ route('siteVitrine.acceuil') }}" class="text-gray-500 hover:text-[#2e7d64] transition duration-200 text-sm inline-flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i>
                Revenir à la page d'accueil
            </a>
        </div>

    </form>

    <style>
        /* Améliorations supplémentaires */
        .bg-primary {
            background-color: #2e7d64 !important;
        }

        .focus\:ring-primary:focus {
            --tw-ring-color: #2e7d64 !important;
        }

        input:focus {
            border-color: #2e7d64 !important;
            outline: none !important;
        }

        /* Animation douce au survol du bouton */
        .btn-primary-custom {
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(46,125,100,0.3);
        }

        /* Style pour le session status */
        [x-data] .session-status {
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #bbf7d0;
        }
    </style>
</x-guest-layout>
