{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Easy Point') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="font-sans text-black antialiased bg-secondary">

    <div class="w-full ">
        <!-- cards -->
        <div class="md:w-10/12 my-5 p-5 bg-white rounded-2xl 2xl:w-10/12 mx-auto">


            <div class="px-6 mt-5 lg:w-4/6 xl:w-3/6 2xl:w-2/6 mx-auto">
                <h1 class=" mb-3 ml-2 font-bold text-black text-center text-xl sm:text-2xl">

                    {{ __('Demandez votre démo dès maintenant !') }}
                </h1>

                <p class="text-center font-semibold">
                    {{ __('Découvrez Easy Point en action et voyez comment notre solution peut simplifier et optimiser votre suivi des présences.') }}
                </p>
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
            <form method="POST" action="{{ route('companies.store.company') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid lg:grid-cols-3 gap-4">

                    <div class="col-span-2">
                        <div class="  px-5 lg:px-10 py-6">
                            <!-- card 1 -->



                            <div class="md:grid grid-cols-2 gap-16 mt-4">
                                <!-- Company name -->
                                <div>
                                    <x-input-label class=" text-sm" for="compagny_name" :value="__('Raison social*')" />
                                    <input id="compagny_name" name="compagny_name" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('compagny_name')" required autofocus>

                                    <x-input-error :messages="$errors->get('compagny_name')" class="mt-2" />
                                </div>
                                <!-- Company address -->
                                <div>
                                    <x-input-label class=" text-sm" for="address" :value="__('Company address *')" />
                                    <input id="address" name="address" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('address')" required autofocus />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                            </div>

                            <div class="md:grid grid-cols-2 gap-16 mt-4">
                                <!-- Phone -->
                                <div>
                                    <x-input-label class=" text-sm" for="phone" :value="__('Phone *')" />
                                    <input id="phone" name="phone" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('phone')" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>


                                <!-- Company name -->
                                <div>
                                    <x-input-label class=" text-sm" for="n_siret" :value="__('Numéro Siret *')" />
                                    <input id="n_siret" name="n_siret" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('n_siret')" required autofocus />
                                    <x-input-error :messages="$errors->get('n_siret')" class="mt-2" />
                                </div>

                            </div>

                            <div class="md:grid grid-cols-2 gap-16 mt-4">

                                <!-- Company email -->
                                <div>
                                    <x-input-label class=" text-sm" for="n_siren" :value="__('Numéro Siren *')" />
                                    <input id="n_siren" name="n_siren" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('n_siren')" required autofocus />
                                    <x-input-error :messages="$errors->get('n_siren')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label class=" text-sm" for="iban" :value="__('IBAN *')" />
                                    <input id="iban" name="iban" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('iban')" required autofocus />
                                    <x-input-error :messages="$errors->get('iban')" class="mt-2" />
                                </div>




                            </div>



                            <div class="md:grid grid-cols-2 gap-16 mt-4">

                                <!-- Status -->


                                <div>
                                    <x-input-label class=" text-sm" for="country" :value="__('Pays')" />
                                    <x-select-input-2 id="country" name="country" :options="[
                                        'togo' => 'TOGO',
                                        'benin' => 'BENIN',
                                        'ghana' => 'GHANA',
                                        'Sénégal' => 'SENEGAL',
                                        'France' => 'FRANCE',
                                        'États-Unis' => 'USA',
                                        'Royaume-Uni' => 'R.U',
                                        'Suisse' => 'SUISSE',
                                        'Canada' => 'CANADA',
                                        'Côte d\'Ivoire' => 'COTE D\'IVOIRE',
                                        'Nigeria' => 'NIGERIA',
                                        'Afrique du Sud' => 'AFRIQUE DU SUD',
                                        'Maroc' => 'MAROC',
                                    ]"
                                        :selected="old('country')" :disabled="false" />

                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label class=" text-sm" for="city" :value="__('Ville')" />
                                    <input id="city" name="city" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('city')" autofocus />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>




                            </div>

                            <div class="md:grid grid-cols-2 gap-16 mt-4">

                                <div>
                                    <x-input-label class=" text-sm" for="currency" :value="__('Devise')" />
                                    <x-select-input-2 id="currency" name="currency" :options="[]"
                                        :selected="old('currency')" :disabled="false" />

                                    <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                                </div>


                                <!-- Default time zone -->

                                <div>
                                    <x-input-label class=" text-sm" for="timezone" :value="__('Default time zone *')" />
                                    <x-select-input-2 name="timezone" :options="$timezones" :disabled="false" required />
                                    <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                                </div>
                            </div>

                            <div class="md:grid grid-cols-2 gap-16 mt-4">

                                <!-- Company email -->
                                <div>
                                    <x-input-label class=" text-sm" for="compagny_email" :value="__('Email *')" />
                                    <input id="compagny_email" name="compagny_email" type="email"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('compagny_email')" required autofocus />
                                    <x-input-error :messages="$errors->get('compagny_email')" class="mt-2" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label class=" text-sm" for="password" :value="__('Password *')" />
                                    <input id="password" name="password" type="password"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('password')" required autofocus />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>


                            </div>
                            <div class="md:grid grid-cols-2 gap-16 mt-4">
                                <!-- Website -->
                                <div>
                                    <x-input-label class=" text-sm" for="website" :value="__('Website')" />
                                    <input id="website" name="website" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('website')" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>
                            </div>


                            <div class="md:grid grid-cols-2 gap-16 mt-4">
                                <!-- Website -->
                                <div>
                                    <x-input-label class=" text-sm" for="activity" :value="__('Domaine d’activité')" />
                                    <input id="activity" name="activity" type="text"
                                        class="block w-full bg-white border-0 shadow-md text-black py-2 mt-1 focus:ring-0 focus:border-primary-color rounded-lg pl-4"
                                        :value="old('activity')" />
                                    <x-input-error :messages="$errors->get('activity')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label class=" text-sm" for="No_of_employees" :value="__('Nombre d’employé')" />
                                    <x-select-input-2 id="No_of_employees" name="No_of_employees" :options="[
                                        '2-10' => '2-10',
                                        '11-30' => '11-30',
                                        '31-50' => '31-50',
                                        '51-90' => '51-90',
                                        '91-120' => '91-120',
                                        '121-250' => '121-250',
                                        '251-500' => '251-500',
                                        '501-1000' => '501-1000',
                                        'Plus de 1000' => 'Plus de 1000',
                                    ]"
                                        :selected="old('No_of_employees')" :disabled="false" />

                                    <x-input-error :messages="$errors->get('No_of_employees')" class="mt-2" />
                                </div>
                            </div>





                        </div>
                    </div>
                    <div class="py-56 hidden lg:block">
                        <img src="{{ asset('images/demo.png') }}"
                            class="inline w-3/4 transition-all duration-200 ease-nav-brand" alt="main_logo" />
                    </div>



                </div>



                <div class="md:grid lg:grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <div class="md:grid lg:grid-cols-2 gap-16 px-5 lg:px-10 py-6">
                            <div class="hidden lg:block"></div>
                            <div class="">
                                <x-primary-button
                                    class="flex w-full md:mx-4 px-0 py-3 justify-center shadow-md shadow-green-600/50">
                                    {{ __('Demander une démo') }}
                                </x-primary-button>

                                <p class="text-center my-4 text-md font-semibold">
                                    <a href="https://easy-point.com/"> {{ __('Revenir à la page d’accueil') }} </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div></div>
                </div>

            </form>



        </div>
        <!-- end cards -->
    </div>
    <script>
        // Exemple de données de devises selon les pays
        const currencies = {
            'togo': ['XOF'], // Franc CFA
            'benin': ['XOF'], // Franc CFA
            'ghana': ['GHS'], // Cedi ghanéen
            // Ajoute d'autres pays et leurs devises ici
        };

        document.getElementById('country').addEventListener('change', function() {
            const country = this.value;
            const currencySelect = document.getElementById('currency');

            // Réinitialiser les options de devise
            currencySelect.innerHTML = '<option value="">Sélectionnez une devise</option>';

            // Remplir le select de devises selon le pays sélectionné
            if (currencies[country]) {
                currencies[country].forEach(currency => {
                    const option = document.createElement('option');
                    option.value = currency;
                    option.textContent = currency;
                    currencySelect.appendChild(option);
                });
            }
        });
    </script>
</body>

</html> --}}
