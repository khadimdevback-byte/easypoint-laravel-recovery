<x-app-layout>

    <!-- cards -->
    <div class="w-full mx-auto">


        <div class="md:w-9/12  2xl:w-8/12 mx-auto">
            <div class="px-6">
                <h1 class="mb-2 ml-2 mt-5 font-bold text-black text-2xl">

                    {{ __('Toute les notifications') }}
                    </h6>
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

            <div class="my-10 px-6 py-6 w-7/12 mx-auto">
                <ul>
                    
                    <li class="my-5 text-end">
                        <button onclick="markAllAsRead()">Marquer tout comme lues</button>
                    </li>

                    @if (Auth::user()->unreadNotifications->count() > 0)
                        @foreach (Auth::user()->unreadNotifications as $notification)
                            <li class="relative my-2">
                                <a class="ease-soft py-4 shadow clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 lg:transition-colors"
                                    href="javascript:;">
                                    <div class="flex py-1">
                                        <div class="my-auto">
                                            <img src="{{ asset('storage/' . $notification->data['avatar']) }}"
                                                class="inline-flex shadow items-center justify-center mr-4 text-white text-sm h-9 w-9 max-w-none rounded-xl" />
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-1 font-normal leading-normal text-sm">
                                                {{ $notification->data['message'] }}</h6>
                                            <p class="mb-0 leading-tight text-xs text-slate-400">
                                                <i class="mr-1 fa fa-clock"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @else
                        <div class="text-center p-6 my-auto">
                            <i class="fa fa-bell-slash text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-600 ">Vous n'avez aucune notification pour le moment.</p>
                        </div>

                    @endif
                </ul>
            </div>
        </div>

    </div>
    <!-- end cards -->

</x-app-layout>
