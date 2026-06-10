<!doctype html>
<html lang="fr" class="h-full" x-data="layoutState()" x-init="init()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'ECAR')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        ecar: {
                            50:'#eef9f4',100:'#d8f0e3',200:'#b4e3cb',300:'#88d1ad',
                            400:'#51b985',500:'#1f8f5f',600:'#18734d',700:'#135a3d',
                            800:'#104834',900:'#0f3c2d'
                        }
                    }
                }
            }
        };

        function layoutState() {
            return {
                sidebarOpen: true,
                dark: false,
                init() {
                    const savedDark = localStorage.getItem('ecar_dark');
                    const savedSidebar = localStorage.getItem('ecar_sidebar');

                    this.dark = savedDark === '1';
                    this.sidebarOpen = savedSidebar !== '0';

                    document.documentElement.classList.toggle('dark', this.dark);
                },
                toggleDark() {
                    this.dark = !this.dark;
                    document.documentElement.classList.toggle('dark', this.dark);
                    localStorage.setItem('ecar_dark', this.dark ? '1' : '0');
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('ecar_sidebar', this.sidebarOpen ? '1' : '0');
                }
            }
        }
    </script>
</head>

<body class="h-full bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'w-64' : 'w-20'"
        class="transition-all duration-200 bg-slate-900 dark:bg-slate-900 text-slate-100 flex flex-col"
    >
        <div class="h-16 px-4 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-ecar-600 flex items-center justify-center font-bold">E</div>
                <span x-show="sidebarOpen" class="font-semibold">ECAR</span>
            </div>
            <button @click="toggleSidebar()" class="p-1.5 rounded hover:bg-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <nav class="p-3 space-y-1 flex-1">
            {{-- Accueil --}}
            <a href="{{ route('accueil') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('home') || request()->routeIs('accueil') ? 'bg-ecar-600 text-white' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.125 1.125 0 0 1 1.591 0L21.75 12M4.5 9.75V19.5A2.25 2.25 0 0 0 6.75 21.75h10.5A2.25 2.25 0 0 0 19.5 19.5V9.75"/>
                </svg>
                <span x-show="sidebarOpen">Accueil</span>
            </a>

            {{-- Fideles --}}
            <a href="{{ route('fideles.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('fideles.*') ? 'bg-ecar-600 text-white' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 20.25h1.5A2.25 2.25 0 0 0 21.75 18V6A2.25 2.25 0 0 0 19.5 3.75h-15A2.25 2.25 0 0 0 2.25 6v12A2.25 2.25 0 0 0 4.5 20.25H6m12 0v-1.5A2.25 2.25 0 0 0 15.75 16.5h-7.5A2.25 2.25 0 0 0 6 18.75v1.5m12 0h-12m9-10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                <span x-show="sidebarOpen">Fidèles</span>
            </a>

            {{-- Biens --}}
            <li class="mb-2">
                <div class="font-semibold">Biens</div>

                <ul class="ml-4 mt-2 space-y-1">

                    <li>
                        <a href="{{ route('biens.index') }}"
                        class="block px-3 py-2 rounded hover:bg-slate-200 dark:hover:bg-slate-700">
                            Liste des biens
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('biens.qrcode') }}"
                        class="block px-3 py-2 rounded hover:bg-slate-200 dark:hover:bg-slate-700">
                            Liste avec QR Code
                        </a>
                    </li>

                </ul>
            </li>

            {{-- Finances --}}
            <div x-data="{ openFinance: {{ request()->routeIs('finances.*') ? 'true' : 'false' }} }">
                <button type="button"
                    @click="openFinance = !openFinance"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('finances.*') ? 'bg-ecar-600 text-white' : '' }}">
                    <span x-show="sidebarOpen">Finances</span>
                    <span x-show="!sidebarOpen">Fi</span>
                    <span x-show="sidebarOpen">▾</span>
                </button>

                <div x-show="openFinance" x-cloak class="mt-1 ml-2 space-y-1">
                    <a href="{{ route('finances.livre_journal') }}"
                    class="block px-3 py-2 rounded-lg text-sm hover:bg-slate-800 {{ request()->routeIs('finances.livre_journal') ? 'bg-slate-800 text-white' : '' }}">
                        <span x-show="sidebarOpen">Livre Journal</span>
                    </a>

                    <a href="{{ route('finances.detail_compte') }}"
                    class="block px-3 py-2 rounded-lg text-sm hover:bg-slate-800 {{ request()->routeIs('finances.detail_compte') ? 'bg-slate-800 text-white' : '' }}">
                        <span x-show="sidebarOpen">Détail par compte</span>
                    </a>
                </div>
            </div>

            {{-- Parametres --}}
            <a href="{{ route('parametres.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('parametres.*') ? 'bg-ecar-600 text-white' : '' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 1 15 0m-15 0a7.5 7.5 0 0 0 15 0M12 8.25v3.75l2.25 2.25"/>
                </svg>
                <span x-show="sidebarOpen">Paramètres</span>
            </a>
        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 min-w-0">
        <!-- Topbar -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between">
            <div>
                <h1 class="font-semibold">@yield('page_title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-3">
                <button @click="toggleDark()" class="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
                    <span x-show="!dark">Dark</span>
                    <span x-show="dark">Light</span>
                </button>

                <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 rounded-full px-3 py-1.5">
                    <div class="h-8 w-8 rounded-full bg-ecar-600 text-white grid place-items-center text-sm font-semibold">AD</div>
                    <div class="text-sm leading-tight">
                        <div class="font-medium">Admin ECAR</div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs">Superviseur</div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>