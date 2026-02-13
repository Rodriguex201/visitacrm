<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VisitaCRM') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans antialiased text-slate-900">
        <div class="min-h-screen">
            <aside class="hidden md:fixed md:inset-y-0 md:flex md:w-[260px] md:flex-col md:border-r md:border-slate-200 md:bg-white">
                <div class="border-b border-slate-200 px-6 py-7">
                    <p class="text-4xl font-bold leading-none text-slate-950">VisitaCRM</p>
                    <p class="mt-1 text-lg text-slate-600">Gestión comercial</p>
                </div>

                <nav class="flex-1 space-y-2 px-4 py-5 text-lg">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl bg-blue-600 px-4 py-3 font-semibold text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 4.75h6.5v6.5h-6.5zM12.75 4.75h6.5v6.5h-6.5zM4.75 12.75h6.5v6.5h-6.5zM12.75 12.75h6.5v6.5h-6.5z"/>
                        </svg>
                        Inicio
                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                        </svg>
                        Empresas
                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v3M15.75 3.75v3M4.5 9h15M5.25 6.75h13.5A.75.75 0 0119.5 7.5v11.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75z"/>
                        </svg>
                        Agenda
                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5M5.25 12h13.5"/>
                        </svg>
                        Nueva
                    </a>

                    <a href="#" class="flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5v-1.125A3.375 3.375 0 0012.375 15h-4.5A3.375 3.375 0 004.5 18.375V19.5m15-7.875a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0zm-8.25-2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        Usuarios
                    </a>
                </nav>

                <div class="border-t border-slate-200 px-4 py-5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-lg font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75h3a1.5 1.5 0 011.5 1.5v13.5a1.5 1.5 0 01-1.5 1.5h-3m-6-4.5l-3.75-3.75m0 0L9.75 8.25m-3.75 3.75h9.75"/>
                            </svg>
                            Salir
                        </button>
                    </form>
                </div>
            </aside>

            <main class="min-h-screen pb-24 md:pb-8 md:pl-[260px]">
                <div class="mx-auto w-full max-w-[1400px] px-4 py-5 md:px-6 md:py-6">
                    @yield('content')
                    @isset($slot)
                        {{ $slot }}
                    @endisset
                </div>
            </main>
        </div>

        <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white px-2 py-2 md:hidden">
            <div class="grid grid-cols-6 gap-1">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-semibold text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 4.75h6.5v6.5h-6.5zM12.75 4.75h6.5v6.5h-6.5zM4.75 12.75h6.5v6.5h-6.5zM12.75 12.75h6.5v6.5h-6.5z"/>
                    </svg>
                    Inicio
                </a>
                <a href="#" class="flex flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-medium text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                    </svg>
                    Empresas
                </a>
                <a href="#" class="flex flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-medium text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v3M15.75 3.75v3M4.5 9h15M5.25 6.75h13.5A.75.75 0 0119.5 7.5v11.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75z"/>
                    </svg>
                    Agenda
                </a>
                <a href="#" class="flex flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-medium text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5M5.25 12h13.5"/>
                    </svg>
                    Nueva
                </a>
                <a href="#" class="flex flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-medium text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5v-1.125A3.375 3.375 0 0012.375 15h-4.5A3.375 3.375 0 004.5 18.375V19.5m15-7.875a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0zm-8.25-2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    Usuarios
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex">
                    @csrf
                    <button type="submit" class="flex w-full flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs font-medium text-slate-500">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75h3a1.5 1.5 0 011.5 1.5v13.5a1.5 1.5 0 01-1.5 1.5h-3m-6-4.5l-3.75-3.75m0 0L9.75 8.25m-3.75 3.75h9.75"/>
                        </svg>
                        Salir
                    </button>
                </form>
            </div>
        </nav>
    </body>
</html>
