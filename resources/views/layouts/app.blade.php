<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Easy Point') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800;900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />

    <script src="https://unpkg.com/@popperjs/core@2" defer></script>

    <link href="{{ asset('assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js" defer></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/fr.global.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Variables globales */
        :root {
            --vp: #2e7d64;
            --vc: #e8f5f1;
            --vf: #1a5a48;
            --va: #40a088;
            --gf: #2d3a3e;
            --gm: #5a6b6f;
            --gl: #d4dfe2;
            --gtc: #eef3f5;
            --blanc: #ffffff;
            --sidebar-width: 260px;
            --navbar-height: 60px;
            --t: all 0.32s cubic-bezier(.4,0,.2,1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Layout Architecture */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: var(--t);
        }

        .page-container {
            padding: calc(var(--navbar-height) + 28px) 24px 24px 24px;
        }

        /* Sidebar Styling */
        #sidenav {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--gf);
            z-index: 1000;
            transition: var(--t);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        #sidenav .sidebar-logo-wrapper {
            padding: 18px 16px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            background: transparent;
        }

        #sidenav .sidebar-logo-inner {
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            width: 100%;
            box-sizing: border-box;
        }

        #sidenav .sidebar-logo-inner img {
            height: 32px;
            width: auto;
            display: block;
            flex-shrink: 0;
        }

        #sidenav .sidebar-logo-inner .logo-label {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--vp);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        #sidenav .sidebar-logo-inner .logo-sublabel {
            font-size: 10px;
            color: #9ca3af;
            display: block;
            font-weight: 400;
        }

        #sidenav a {
            color: rgba(255, 255, 255, 0.65);
            transition: var(--t);
            text-decoration: none;
        }

        #sidenav a:hover {
            color: #6ecfb5;
        }

        #sidenav .btnAside,
        #sidenav .bg-primary {
            background: rgba(46, 125, 100, 0.18) !important;
            color: #6ecfb5 !important;
            border-left: 3px solid #6ecfb5 !important;
            border-radius: 0 !important;
        }

        #sidenav::-webkit-scrollbar { width: 3px; }
        #sidenav::-webkit-scrollbar-track { background: transparent; }
        #sidenav::-webkit-scrollbar-thumb {
            background: rgba(46, 125, 100, 0.4);
            border-radius: 4px;
        }

        /* Accordions & Submenus */
        .sub-menu {
            list-style: none;
            padding-left: 2.2rem !important;
        }

        .sub-menu li { margin: 0.2rem 0; }
        .sub-menu li a {
            display: block;
            padding: 0.3rem 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.82rem;
        }

        .sub-menu li a:hover { color: #6ecfb5; }
        .sub-menu li i { color: rgba(255, 255, 255, 0.3); }

        .fa-chevron-down { transition: transform 0.3s ease; }
        .active .fa-chevron-down { transform: rotate(180deg); }

        .hs-accordion ul { display: none; }
        .hs-accordion ul.show {
            display: block;
            animation: fadeInMenu 0.25s ease;
        }

        @keyframes fadeInMenu {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Responsive breakpoints */
        @media (max-width: 1280px) {
            .main-content { margin-left: 0; }
            #sidenav { transform: translateX(-100%); }
            #sidenav:not(.hidden) { transform: translateX(0); }
            .page-container { padding: calc(var(--navbar-height) + 16px) 16px 16px 16px; }
        }
    </style>
</head>

<body class="bg-gray-50">

    @include('layouts.aside')

    <main class="main-content">
        @include('layouts.navigation')

        <div class="page-container">
            {{ $slot }}
        </div>
    </main>

    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}" defer></script>
    <script src="{{ asset('assets/js/ajax-refresh.js') }}" defer></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5') }}" defer></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const defaultImage = "{{ asset('images/favicon.png') }}";

            // Écoute globale des erreurs de chargement d'image (plus performant qu'un fetch HEAD sur chaque image)
            document.body.addEventListener('error', function(event) {
                if (event.target.tagName.toLowerCase() === 'img') {
                    event.target.src = defaultImage;
                }
            }, true);
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidenavTrigger = document.querySelector("[sidenav-trigger]");
            const sidenav = document.getElementById("sidenav");

            if (sidenavTrigger && sidenav) {
                sidenavTrigger.addEventListener("click", function() {
                    sidenav.classList.toggle("-translate-x-full");
                    sidenav.classList.toggle("hidden");
                });
            }
        });

        function toggleMenu(event, submenuId) {
            event.preventDefault();
            const submenu = document.getElementById(submenuId);
            const parentItem = event.currentTarget.closest('.hs-accordion');

            document.querySelectorAll('.hs-accordion ul').forEach(menu => {
                if (menu.id !== submenuId && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    menu.classList.remove('show');
                    menu.closest('.hs-accordion').classList.remove('active');
                }
            });

            submenu.classList.toggle('hidden');
            submenu.classList.toggle('show');
            parentItem.classList.toggle('active');

            if (submenu.classList.contains('show')) {
                submenu.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidenav = document.getElementById('sidenav');
            if (window.innerWidth < 1280) {
                sidenav.classList.add('hidden');
            }

            const currentPath = window.location.pathname;
            document.querySelectorAll('.hs-accordion').forEach(accordion => {
                const submenu = accordion.querySelector('ul');
                if (submenu) {
                    const activeLink = submenu.querySelector(`a[href*="${currentPath}"]`);
                    if (activeLink) {
                        submenu.classList.remove('hidden');
                        submenu.classList.add('show');
                        accordion.classList.add('active');
                    }
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1280) {
                    sidenav.classList.remove('hidden');
                } else {
                    sidenav.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
