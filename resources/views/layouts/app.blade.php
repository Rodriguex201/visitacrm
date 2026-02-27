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
    <body class="min-h-screen bg-gray-50 font-sans antialiased text-slate-900"
        x-data="{
            sidebarCollapsed: false,
            mobileSidebarOpen: false,
            init() {
                this.sidebarCollapsed = localStorage.getItem('sidebar') === 'collapsed';
            },
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebar', this.sidebarCollapsed ? 'collapsed' : 'open');
            }
        }"
        x-init="init()"
    >

        @php
            $isDashboard = request()->routeIs('dashboard');
            $isEmpresas = request()->routeIs('empresas.*');
            $isUsuarios = request()->routeIs('usuarios.*');
            $isAdmin = auth()->user()?->tipo_usuario === 'administracion';
        @endphp

        <div class="min-h-screen md:flex">
            <div
                x-show="mobileSidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-slate-900/40 md:hidden"
                @click="mobileSidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out md:sticky md:top-0 md:z-10 md:h-screen md:translate-x-0 md:transition-all"
                :class="[
                    mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    sidebarCollapsed ? 'md:w-20' : 'md:w-64'
                ]"
            >
                <div class="flex items-start justify-between border-b border-slate-200 px-5 py-5" :class="sidebarCollapsed ? 'md:px-3' : ''">
                    <div x-show="!sidebarCollapsed" x-transition class="md:block">
                        <p class="text-3xl font-bold leading-none text-slate-950">VisitaCRM</p>
                        <p class="mt-1 text-sm text-slate-600">Gestión comercial</p>
                    </div>

                    <button
                        type="button"
                        class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                        @click="toggleSidebar()"
                        :aria-label="sidebarCollapsed ? 'Expandir sidebar' : 'Colapsar sidebar'"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-1.5 px-3 py-4 text-sm" :class="sidebarCollapsed ? 'md:px-2' : ''">

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold transition {{ $isDashboard ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" @click="mobileSidebarOpen = false" title="Inicio">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.75 4.75h6.5v6.5h-6.5zM12.75 4.75h6.5v6.5h-6.5zM4.75 12.75h6.5v6.5h-6.5zM12.75 12.75h6.5v6.5h-6.5z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>Inicio</span>
                    </a>


                    <a href="{{ route('empresas.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold transition {{ $isEmpresas ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" @click="mobileSidebarOpen = false" title="Empresas">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>Empresas</span>
                    </a>


                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" @click="mobileSidebarOpen = false" title="Agenda">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v3M15.75 3.75v3M4.5 9h15M5.25 6.75h13.5A.75.75 0 0119.5 7.5v11.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>Agenda</span>
                    </a>


                    @if ($isAdmin)
                        <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-semibold transition {{ $isUsuarios ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" @click="mobileSidebarOpen = false" title="Usuarios">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5v-1.125A3.375 3.375 0 0012.375 15h-4.5A3.375 3.375 0 004.5 18.375V19.5m15-7.875a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0zm-8.25-2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition>Usuarios</span>
                        </a>
                    @endif


                    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" @click="mobileSidebarOpen = false" title="Nueva">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5M5.25 12h13.5"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition>Nueva</span>
                    </a>
                </nav>


                <div class="border-t border-slate-200 px-3 py-4" :class="sidebarCollapsed ? 'md:px-2' : ''">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800" :class="sidebarCollapsed ? 'md:justify-center md:px-2' : ''" title="Salir">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75h3a1.5 1.5 0 011.5 1.5v13.5a1.5 1.5 0 01-1.5 1.5h-3m-6-4.5l-3.75-3.75m0 0L9.75 8.25m-3.75 3.75h9.75"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" x-transition>Salir</span>
                        </button>
                    </form>
                </div>
            </aside>


            <main class="min-h-screen flex-1 pb-6">
                <div class="mx-auto flex w-full max-w-[1300px] items-center justify-between px-4 py-4 md:hidden">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-100"
                        @click="mobileSidebarOpen = true"
                        aria-label="Abrir menú"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
                        </svg>
                    </button>
                    <p class="text-lg font-semibold text-slate-900">VisitaCRM</p>
                </div>

                <div class="mx-auto w-full max-w-[1300px] px-4 py-2 md:px-5 md:py-5">

                    @yield('content')
                    @isset($slot)
                        {{ $slot }}
                    @endisset
                </div>
            </main>
        </div>


    </body>
</html>
