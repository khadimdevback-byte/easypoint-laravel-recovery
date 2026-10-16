<!-- Navbar -->
<nav class="navbar-custom">
    <div class="navbar-custom-container">

        <!-- Droite : notifications + user + bouton mobile -->
        <div class="navbar-custom-right">

            <!-- Notifications -->
            <div class="notification-wrapper">
                <a href="javascript:;" class="notification-trigger" id="notif-trigger">
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <span class="notification-badge"></span>
                    @endif
                    <i class="fa-regular fa-bell"></i>
                </a>

                <ul id="notifications" class="notification-dropdown">
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        @foreach (Auth::user()->unreadNotifications->sortByDesc('created_at')->take(4) as $notification)
                            <li class="notification-item">
                                <a href="javascript:;">
                                    <div class="notification-content">
                                        <div class="notification-avatar">
                                            <img src="{{ asset('storage/' . $notification->data['avatar']) }}" alt="" />
                                        </div>
                                        <div class="notification-text">
                                            <h6>{{ $notification->data['message'] }}</h6>
                                            <p>{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li class="notification-footer">
                            <button onclick="markAllAsRead()">
                                <i class="fas fa-check-double"></i> Marquer comme lues
                            </button>
                        </li>
                    @else
                        <li class="notification-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            <span>Aucune notification</span>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Dropdown utilisateur -->
            <div class="user-dropdown">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="user-trigger">
                            @if (Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="avatar" class="user-avatar" />
                            @else
                                <div class="user-avatar-placeholder">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->first_name ?? '', 0, 1)) }}
                                </div>
                            @endif
                            <div class="user-info">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-firstname">{{ Auth::user()->first_name }}</span>
                            </div>
                            <i class="fas fa-chevron-down user-chevron"></i>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- Profil --}}
                        @if (Auth::user()->user_type != 'super_admin')
                            @if (Auth::user()->user_type == 'admin')
                                <x-dropdown-link :href="route('profile.edit.company')">
                                    <i class="fas fa-user"></i> {{ __('Profil') }}
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="route('profile.edit')">
                                    <i class="fas fa-user"></i> {{ __('Profil') }}
                                </x-dropdown-link>
                            @endif
                        @else
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fas fa-user-cog"></i> {{ __('Profil ') }}
                            </x-dropdown-link>
                        @endif

                        {{-- Séparateur --}}
                        <div class="dropdown-divider"></div>

                        {{-- Déconnexion --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt"></i> {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Bouton menu mobile -->
            <button class="mobile-menu-btn" sidenav-trigger aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>

        </div>
    </div>
</nav>

<script>
    function markAllAsRead() {
        axios.post('/notifications/mark-all-as-read')
            .then(() => location.reload())
            .catch(error => {
                console.error(error);
                alert('Une erreur est survenue.');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const trigger  = document.getElementById('notif-trigger');
        const dropdown = document.getElementById('notifications');

        if (trigger && dropdown) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', function (e) {
                if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
</script>

<style>
    /* ============================================ */
    /* NAVBAR CUSTOM                                */
    /* ============================================ */

    .navbar-custom {
        position: fixed;
        top: 0;
        left: var(--sidebar-width, 260px);
        right: 0;
        z-index: 950;
        height: var(--navbar-height, 60px);
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 24px;
        display: flex;
        align-items: center;
        transition: left 0.32s cubic-bezier(.4,0,.2,1);
    }

    .navbar-custom-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .navbar-custom-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
    }

    /* ----- Partie droite ----- */
    .navbar-custom-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ----- Utilisateur ----- */
    .user-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        border: 1px solid #e5e7eb;
        padding: 5px 12px 5px 5px;
        border-radius: 40px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .user-trigger:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .user-avatar-placeholder {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #2e7d64;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
        font-family: 'Inter', sans-serif;
    }

    .user-info {
        display: none;
        text-align: left;
    }

    .user-name {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1f2937;
        line-height: 1.3;
    }

    .user-firstname {
        display: block;
        font-size: 0.7rem;
        color: #9ca3af;
        line-height: 1.3;
    }

    .user-chevron {
        font-size: 0.65rem;
        color: #9ca3af;
    }

    /* ----- Notifications ----- */
    .notification-wrapper {
        position: relative;
    }

    .notification-trigger {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 1.1rem;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .notification-trigger:hover {
        background: #e8f5f1;
        color: #2e7d64;
        border-color: #c6e6dc;
    }

    .notification-badge {
        position: absolute;
        top: 1px;
        right: 1px;
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    .notification-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 340px;
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-6px);
        transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
        z-index: 1100;
        list-style: none;
        padding: 6px 0;
        margin: 0;
    }

    .notification-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notification-item {
        border-bottom: 1px solid #f3f4f6;
    }

    .notification-item:last-of-type {
        border-bottom: none;
    }

    .notification-item a {
        display: block;
        padding: 10px 14px;
        text-decoration: none;
        transition: background 0.15s;
    }

    .notification-item a:hover {
        background: #f9fafb;
    }

    .notification-content {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .notification-avatar img {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .notification-text h6 {
        font-size: 0.78rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 3px 0;
        line-height: 1.4;
    }

    .notification-text p {
        font-size: 0.68rem;
        color: #9ca3af;
        margin: 0;
    }

    .notification-footer {
        padding: 8px 14px;
        border-top: 1px solid #f3f4f6;
        text-align: center;
    }

    .notification-footer button {
        background: none;
        border: none;
        color: #2e7d64;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.15s;
    }

    .notification-footer button:hover {
        background: #e8f5f1;
    }

    .notification-empty {
        padding: 28px 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 0.82rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .notification-empty i {
        font-size: 1.4rem;
        color: #d1d5db;
    }

    /* Dropdown divider */
    .dropdown-divider {
        height: 1px;
        background: #e5e7eb;
        margin: 6px 12px;
    }

    .mobile-menu-btn {
        display: none;
        background: transparent;
        border: 1px solid #e5e7eb;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 7px 10px;
        border-radius: 8px;
        color: #4b5563;
        transition: all 0.2s;
    }

    .mobile-menu-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    /* ========== RESPONSIVE ========== */
    @media (min-width: 1024px) {
        .user-info {
            display: block;
        }
    }

    @media (max-width: 1280px) {
        .navbar-custom {
            left: 0;
        }

        .navbar-custom-left {
            display: none;
        }

        .navbar-custom-center {
            display: block;
        }

        .mobile-menu-btn {
            display: block;
        }
    }

    @media (max-width: 768px) {
        .navbar-custom {
            padding: 0 14px;
        }

        .notification-dropdown {
            position: fixed;
            top: calc(var(--navbar-height, 60px) + 6px);
            left: 10px;
            right: 10px;
            width: auto;
        }

        .user-info {
            display: none !important;
        }
    }
</style>
