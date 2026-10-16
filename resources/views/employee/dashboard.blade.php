 @php
     \Carbon\Carbon::setLocale('fr');
 @endphp


 <x-app-layout>
     <div class="container md:w-9/12  2xl:w-8/12 mx-auto px-3 pb-10">
         <div class="w-full lg:px-6 pt-5 mx-auto">

             <div class="shadow-lg border rounded-lg p-4 lg:p-5">
                 <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 " style="align-items: center">
                     <div class=" lg:flex text-center lg:text-start">
                         <img class="rounded-full size-32 mx-auto shadow" src="{{ asset('storage/' . $user->avatar) }}"
                             alt="Avatar">
                         <div class="mt-2 sm:mt-4 ml-10">
                             <h3 class="font-bold text-xl text-gray-800 pb-3">
                                 {{ $user->first_name }}
                                 <span class="uppercase">{{ $user->name }}</span>
                             </h3>
                             @switch($today_data["status"])
                                 @case('late')
                                     <p class="font-semibold text-gray-600 pb-2">
                                         Aujourd'hui: <span class=" text-yellow-500 "> Retard </span></p>
                                     <p class="text-sm text-yellow-600">
                                         Vous êtes arrivé à {{ $today_data['last_timestamp'] }}
                                     </p>
                                 @break

                                 @case('present')
                                     <p class="font-semibold text-gray-600 pb-2">
                                         Aujourd'hui: <span class=" text-green-500 "> À l'heure </span></p>
                                     <p class="text-sm text-green-600">
                                         Vous êtes arrivé à {{ $today_data['last_timestamp'] }}
                                     </p>
                                 @break

                                 @case('absent')
                                     <p class="font-semibold text-gray-600 pb-2">
                                         Aujourd'hui: <span class=" text-red-500 "> - </span></p>
                                     <p class="text-sm text-red-600">
                                         Vous n'avez pas encore pointé votre présence
                                     </p>
                                 @break

                                 @default
                             @endswitch



                         </div>
                     </div>
                     <div>
                         <p class="font-semibold text-gray-600 pb-2">
                             7 derniers jours</p>
                         <div class="grid grid-cols-7 gap-2 mt-3 xl:w-3/4">
                             @foreach ($seven_day_status as $date => $data)
                                 @php
                                     $carbonDate = \Carbon\Carbon::parse($date);
                                     $day = $carbonDate->format('d');
                                     $month = $carbonDate->locale('fr')->isoFormat('MMM');
                                     $status = $data['status'] ?? 'absent';

                                     $statusClasses = [
                                         'present' => 'bg-green-500',
                                         'late' => 'bg-yellow-400',
                                         'absent' => 'bg-red-500',
                                     ];
                                     $bgClass =
                                         $day == now()->format('d') && $status == 'absent'
                                             ? 'bg-gray-500'
                                             : $statusClasses[$status] ?? 'bg-gray-400';
                                 @endphp

                                 <div class="flex flex-col items-center justify-between w-12 h-12 rounded-full text-white font-semibold shadow-md cursor-default {{ $bgClass }}"
                                     title="{{ ucfirst($status) }} - {{ $date }}">
                                     <div class="text-sm pt-2 leading-none">{{ $day }}</div>
                                     <div class="text-xs pb-2 opacity-80">{{ $month }}</div>
                                 </div>
                             @endforeach
                         </div>
                     </div>

                 </div>
             </div>



             <div class="mt-10 shadow-lg border rounded-lg p-10">
                 <h2 class=" font-bold mb-4 text-2xl text-black">Bilan</h2>


                 <div class="py-5 lg:w-3/4 mx-auto">

                     <h3 class=" mb-4 text-xl text-gray-600"> Aujourd'hui</h3>

                     <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

                         <div
                             class="shadow-md flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-red-600">Retard</span>
                             </div>

                             <div class="text-center">
                                 <p class="text-xl uppercase sm:text-4xl lg:text-2xl font-bold text-gray-800">
                                     @if ($today_status == 'late')
                                         OUI
                                     @else
                                         NON
                                     @endif
                                 </p>
                             </div>


                         </div>

                         <div
                             class="shadow-md flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-red-600">Absence</span>
                             </div>

                             <div class="text-center">
                                 <p class="text-xl uppercase sm:text-4xl lg:text-2xl font-bold text-gray-800">
                                     @if ($today_status == 'absent')
                                         OUI
                                     @else
                                         NON
                                     @endif
                                 </p>
                             </div>


                         </div>

                         <div
                             class="shadow-md flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-gray-600">Temps</span>
                             </div>

                             <div class="text-center">
                                 <h3 class="2xl:text-xl font-bold text-gray-800">
                                     {{ $today_work_duration }}
                                 </h3>
                             </div>


                         </div>



                     </div>

                 </div>

                 <div class="py-5 mx-auto lg:w-3/4">

                     <h3 class=" mb-4 text-xl text-gray-600"> Ce mois</h3>

                     <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

                         <div
                             class="shadow-md flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-red-600">Retard</span>
                             </div>

                             <div class="text-center">
                                 <h3 class="text-xl uppercase sm:text-4xl lg:text-2xl font-bold text-gray-800">
                                     {{ $total_retard }}
                                 </h3>
                             </div>


                         </div>

                         <div
                             class="shadow-md flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-red-600">Absence</span>
                             </div>

                             <div class="text-center">
                                 <h3 class="text-xl uppercase sm:text-4xl lg:text-2xl font-bold text-gray-800">
                                     {{ $total_absence }}
                                 </h3>
                             </div>


                         </div>

                         <div
                             class="shadow-md  flex flex-col gap-y-3 lg:gap-y-5 p-4 md:p-5 bg-white border border-gray-200 shadow-2xs rounded-xl">
                             <div class="inline-flex justify-center items-center">

                                 <span class="text-xs font-bold uppercase text-gray-600">Temps</span>
                             </div>

                             <div class="text-center">
                                 <h3 class="2xl:text-xl font-bold text-gray-800">
                                     {{ $total_work_duration }}
                                 </h3>
                             </div>


                         </div>



                     </div>

                 </div>

             </div>


         </div>
     </div>

 </x-app-layout>
