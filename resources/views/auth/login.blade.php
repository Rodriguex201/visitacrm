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
    <body class="min-h-screen bg-[#e5e7eb] font-sans antialiased text-slate-900">
        <main class="flex min-h-screen items-center justify-center px-4 py-8">
            <section class="w-full max-w-xl text-center">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand shadow-lg shadow-blue-500/30">
                    <svg class="h-9 w-9 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 7.25V5.5C10 4.25736 11.0074 3.25 12.25 3.25H13.75C14.9926 3.25 16 4.25736 16 5.5V7.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M5.75 8.25H18.25C19.2165 8.25 20 9.0335 20 10V17.25C20 18.2165 19.2165 19 18.25 19H5.75C4.7835 19 4 18.2165 4 17.25V10C4 9.0335 4.7835 8.25 5.75 8.25Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M8 8.25V19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>

                <h1 class="text-4xl font-bold tracking-tight text-slate-950">VisitaCRM</h1>
                <p class="mt-2 text-lg text-slate-600">Gestión de visitas comerciales</p>

                <div class="mt-10 rounded-2xl bg-white p-8 text-left shadow-xl shadow-slate-300/60 md:p-10">
                    <h2 class="text-center text-4xl font-bold text-slate-950">Iniciar Sesión</h2>
                    <p class="mt-3 text-center text-xl text-slate-600">Ingresa tus credenciales para continuar</p>

                    <x-auth-session-status class="mt-5" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-6">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-lg font-semibold text-slate-900">Correo electrónico</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9A2.25 2.25 0 0119.5 18.75h-15A2.25 2.25 0 012.25 16.5v-9m19.5 0A2.25 2.25 0 0019.5 5.25h-15A2.25 2.25 0 002.25 7.5m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615A2.25 2.25 0 012.25 7.743V7.5" />
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="tu@correo.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="h-14 w-full rounded-xl border border-slate-200 bg-slate-100 pl-12 pr-4 text-lg text-slate-700 placeholder-slate-500 outline-none transition focus:border-brand focus:ring-4 focus:ring-blue-200"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-lg font-semibold text-slate-900">Contraseña</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5m-.75 0h9.75A2.25 2.25 0 0119.5 12.75v6A2.25 2.25 0 0117.25 21h-9.5A2.25 2.25 0 015.5 18.75v-6A2.25 2.25 0 017.75 10.5z" />
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                    class="h-14 w-full rounded-xl border border-slate-200 bg-slate-100 pl-12 pr-4 text-lg text-slate-700 placeholder-slate-500 outline-none transition focus:border-brand focus:ring-4 focus:ring-blue-200"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <button type="submit" class="bg-brand h-14 w-full rounded-xl text-xl font-semibold text-white shadow-lg shadow-blue-500/30 transition hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200">
                            Iniciar Sesión
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </body>
</html>
