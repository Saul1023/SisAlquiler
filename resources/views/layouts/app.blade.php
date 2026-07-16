<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema Alquileres') }}</title>

        <!-- Google Fonts: Inter & Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- FontAwesome for Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @livewireStyles

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Outfit', sans-serif;
            }
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            .dark ::-webkit-scrollbar-thumb {
                background: #475569;
            }

            /* Responsive Tables to Card Stacks on Mobile */
            @media (max-width: 768px) {
                .responsive-table thead {
                    display: none;
                }
                .responsive-table tbody, 
                .responsive-table tr, 
                .responsive-table td {
                    display: block;
                    width: 100% !important;
                }
                .responsive-table tr {
                    margin-bottom: 1rem;
                    padding: 1rem;
                    background: #ffffff;
                    border: 1px solid #f1f5f9;
                    border-radius: 1.25rem;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
                }
                .dark .responsive-table tr {
                    background: #1e293b;
                    border-color: #334155/50;
                }
                .responsive-table td {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 0.65rem 0;
                    border-bottom: 1px dashed #f1f5f9;
                    text-align: right;
                }
                .dark .responsive-table td {
                    border-bottom-color: #334155/30;
                }
                .responsive-table td:last-child {
                    border-bottom: none;
                    padding-top: 0.75rem;
                    justify-content: flex-end;
                    gap: 0.5rem;
                }
                .responsive-table td::before {
                    content: attr(data-label);
                    font-weight: 750;
                    color: #64748b;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    text-align: left;
                    margin-right: 1rem;
                }
                .dark .responsive-table td::before {
                    color: #94a3b8;
                }
            }
        </style>
    </head>
    <body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 antialiased min-h-screen transition-colors duration-300">
        <div class="hidden"><livewire:layout.navigation /></div>
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', mobileSidebarOpen: false }">
            
            <!-- Sidebar (Desktop & Mobile) -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 flex flex-col h-full bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 transition-all duration-300"
                :class="{ 
                    'w-64': sidebarOpen, 
                    'w-20': !sidebarOpen, 
                    '-translate-x-full lg:translate-x-0': !mobileSidebarOpen,
                    'translate-x-0': mobileSidebarOpen
                }"
            >
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-slate-200 dark:border-slate-700">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20 shrink-0">
                            <i class="fa-solid fa-house-chimney text-lg"></i>
                        </div>
                        <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent transition-opacity duration-300 font-outfit" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0': !sidebarOpen }">
                            AlquiRent
                        </span>
                    </a>
                    
                    <!-- Toggle sidebar btn on desktop -->
                    <button @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400">
                        <i class="fa-solid" :class="sidebarOpen ? 'fa-angle-left' : 'fa-angle-right'"></i>
                    </button>

                    <!-- Close button on mobile -->
                    <button @click="mobileSidebarOpen = false" class="lg:hidden text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                    @php
                        $menuItems = [
                            ['route' => 'dashboard', 'icon' => 'fa-gauge', 'label' => 'Escritorio'],
                            ['route' => 'rooms.index', 'icon' => 'fa-door-open', 'label' => 'Habitaciones'],
                            ['route' => 'tenants.index', 'icon' => 'fa-users', 'label' => 'Inquilinos'],
                            ['route' => 'contracts.index', 'icon' => 'fa-file-signature', 'label' => 'Contratos'],
                            ['route' => 'payments.index', 'icon' => 'fa-file-invoice-dollar', 'label' => 'Pagos'],
                            ['route' => 'reports.index', 'icon' => 'fa-chart-pie', 'label' => 'Reportes'],
                            ['route' => 'settings.index', 'icon' => 'fa-sliders', 'label' => 'Configuración'],
                        ];
                    @endphp

                    @foreach ($menuItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || (request()->segment(1) == explode('.', $item['route'])[0]);
                        @endphp
                        <a 
                            href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group relative"
                            :class="{ 
                                'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400': '{{ $isActive }}' === '1',
                                'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200': '{{ $isActive }}' !== '1'
                            }"
                            title="{{ $item['label'] }}"
                        >
                            <i class="fa-solid {{ $item['icon'] }} text-lg w-6 shrink-0 text-center transition-transform group-hover:scale-110"></i>
                            <span class="transition-opacity duration-300" :class="{ 'opacity-100': sidebarOpen, 'opacity-0 w-0 pointer-events-none': !sidebarOpen }">
                                {{ $item['label'] }}
                            </span>

                            <!-- Active Indicator Dot when collapsed -->
                            <div x-show="!sidebarOpen" class="absolute right-2 w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 opacity-0 group-hover:opacity-100 transition-opacity" :class="{ 'opacity-100': '{{ $isActive }}' === '1' }"></div>
                        </a>
                    @endforeach
                </nav>

                <!-- Sidebar Footer User Panel -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm uppercase shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                        </div>
                        <div class="flex-1 text-left min-w-0" x-show="sidebarOpen">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Mobile Sidebar Backdrop -->
            <div x-show="mobileSidebarOpen" class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden" @click="mobileSidebarOpen = false"></div>

            <!-- Page Container -->
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden lg:pl-0 transition-all duration-300" :class="{ 'lg:pl-64': sidebarOpen, 'lg:pl-20': !sidebarOpen }">
                
                <!-- Navbar superior -->
                <header class="flex items-center justify-between h-16 px-4 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <!-- Toggle button for mobile -->
                        <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Page Title / Global Search -->
                        <div class="hidden md:block w-72">
                            <livewire:global-search />
                        </div>
                    </div>

                    <!-- Right side icons -->
                    <div class="flex items-center gap-3">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400">
                            <i class="fa-solid" :class="darkMode ? 'fa-sun text-yellow-500' : 'fa-moon'"></i>
                        </button>

                        <!-- Notification Hub -->
                        <livewire:notification-hub />

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-600">
                                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="hidden sm:inline text-sm font-semibold text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-angle-down text-xs text-slate-400"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-50 py-1" style="display: none;">
                                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <i class="fa-solid fa-user text-slate-400 w-4 text-center"></i> Mi Perfil
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                    <i class="fa-solid fa-sliders text-slate-400 w-4 text-center"></i> Configuración
                                </a>
                                <hr class="border-slate-200 dark:border-slate-700 my-1">
                                <!-- Authentication -->
                                <button onclick="document.getElementById('logout-form').submit();" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Cerrar Sesión
                                </button>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 md:p-6 pb-24 md:pb-6 bg-slate-50 dark:bg-slate-900 transition-colors duration-300">
                    {{ $slot }}
                </main>

                <!-- Mobile Bottom Navigation Bar -->
                <div class="fixed bottom-0 left-0 right-0 z-40 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 h-16 flex items-center justify-around pb-safe lg:hidden shadow-[0_-4px_12px_rgba(0,0,0,0.04)]">
                    @foreach ($menuItems as $item)
                        @if(in_array($item['route'], ['dashboard', 'rooms.index', 'tenants.index', 'contracts.index', 'payments.index']))
                            @php
                                $isActive = request()->routeIs($item['route']) || (request()->segment(1) == explode('.', $item['route'])[0]);
                            @endphp
                            <a 
                                href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" 
                                class="flex flex-col items-center justify-center flex-1 h-full text-[9px] font-bold gap-1 transition-all"
                                :class="{ 
                                    'text-blue-600 dark:text-blue-400': '{{ $isActive }}' === '1',
                                    'text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-350': '{{ $isActive }}' !== '1'
                                }"
                            >
                                <i class="fa-solid {{ $item['icon'] }} text-base"></i>
                                <span>{{ str_replace(['Escritorio', 'Habitaciones', 'Inquilinos', 'Contratos', 'Pagos'], ['Inicio', 'Cuartos', 'Inquilinos', 'Contratos', 'Pagos'], $item['label']) }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Footer -->
                <footer class="hidden md:flex h-10 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 text-xs text-slate-400 dark:text-slate-500">
                    <span>&copy; {{ date('Year') }} {{ \App\Models\Setting::get('company_name', 'Sistema Alquileres') }}. Todos los derechos reservados.</span>
                    <span>v1.0.0</span>
                </footer>
            </div>
        </div>

        @livewireScripts

        <!-- Real-time alerts notification script -->
        <script>
            document.addEventListener('livewire:init', () => {
                // Toast notification using SweetAlert2
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                // Listen for custom events from Livewire
                Livewire.on('swal:toast', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    Toast.fire({
                        icon: data.type || 'success',
                        title: data.message
                    });
                });

                Livewire.on('swal:alert', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    Swal.fire({
                        title: data.title || 'Atención',
                        text: data.message,
                        icon: data.type || 'info',
                        confirmButtonText: 'Entendido',
                        customClass: {
                            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl'
                        }
                    });
                });

                Livewire.on('swal:confirm', (event) => {
                    const data = Array.isArray(event) ? event[0] : event;
                    Swal.fire({
                        title: data.title || '¿Estás seguro?',
                        text: data.message,
                        icon: data.type || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: data.confirmText || 'Sí, eliminar',
                        cancelButtonText: data.cancelText || 'Cancelar',
                        customClass: {
                            confirmButton: 'rounded-xl px-4 py-2 font-semibold text-white',
                            cancelButton: 'rounded-xl px-4 py-2 font-semibold text-white'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch(data.action, { id: data.id });
                        }
                    });
                });
            });
        </script>
        <livewire:copilot-chat />
        @stack('js')
    </body>
</html>
