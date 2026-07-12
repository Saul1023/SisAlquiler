<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AlquiRent') }} - Acceso</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;850&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="font-sans antialiased text-slate-800 dark:text-slate-200">
        <!-- Premium background with mesh gradient -->
        <div class="min-h-screen flex items-center justify-center bg-slate-900 relative overflow-hidden px-4">
            
            <!-- Floating light blobs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full bg-blue-600/10 blur-[120px] pointer-events-none animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full bg-indigo-650/10 blur-[120px] pointer-events-none animate-pulse" style="animation-delay: 2s;"></div>

            <!-- Login Card Container -->
            <div class="w-full sm:max-w-md bg-slate-800/80 backdrop-blur-md border border-slate-700/50 p-8 rounded-3xl shadow-2xl relative z-10 space-y-6">
                <!-- Logo & Brand Header -->
                <div class="text-center space-y-2.5">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25">
                        <i class="fa-solid fa-house-chimney text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-black tracking-tight text-white font-outfit">AlquiRent</h2>
                    <p class="text-xs text-slate-400">Sistema Integral de Gestión de Alquileres</p>
                </div>

                <!-- Slot Content -->
                <div class="text-xs">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
