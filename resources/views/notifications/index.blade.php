<x-app-layout>

    <div class="w-full mx-auto px-4 py-8">
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Mes notifications') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $unreadCount }} {{ __('non lue(s)') }}
                    </p>
                </div>
                @if ($unreadCount > 0)
                    <button type="button" onclick="markAllAsRead()"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                        <i class="fas fa-check-double mr-2"></i>{{ __('Tout marquer comme lu') }}
                    </button>
                @endif
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

            <ul class="space-y-3">
                @forelse ($notifications as $notification)
                    <li class="rounded-xl border {{ $notification->read_at ? 'border-gray-200 bg-white' : 'border-blue-200 bg-blue-50' }} p-4 shadow-sm">
                        <a class="flex items-start gap-4 text-gray-800"
                            href="{{ $notification->data['url'] ?? route('notifications.index') }}">
                            @if (!empty($notification->data['avatar']))
                                <img src="{{ asset('storage/' . $notification->data['avatar']) }}"
                                    alt="" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <i class="fas fa-bell" aria-hidden="true"></i>
                                </span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium">
                                    {{ $notification->data['message'] ?? __('Nouvelle notification') }}
                                </span>
                                <span class="mt-1 block text-xs text-gray-500">
                                    <i class="fa fa-clock mr-1" aria-hidden="true"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </span>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $notification->read_at ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700' }}">
                                {{ $notification->read_at ? __('Lue') : __('Non lue') }}
                            </span>
                        </a>
                    </li>
                @empty
                    <li class="rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center">
                        <i class="fa fa-bell-slash mb-4 text-4xl text-gray-400" aria-hidden="true"></i>
                        <p class="text-gray-600">{{ __('Vous n’avez aucune notification pour le moment.') }}</p>
                    </li>
                @endforelse
            </ul>
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
