@extends('layouts.app')

@section('content')
    <section class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold leading-tight text-slate-950">Dashboard</h1>
                <p class="mt-1 text-2xl text-slate-600">Resumen de actividad comercial</p>
            </div>

            <button type="button" class="hidden items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 md:inline-flex">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5M5.25 12h13.5"/>
                </svg>
                Nueva Visita
            </button>

            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-xl font-semibold text-white shadow-sm transition hover:bg-blue-700 md:hidden">
                +
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 md:p-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v3M15.75 3.75v3M4.5 9h15M5.25 6.75h13.5A.75.75 0 0119.5 7.5v11.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75z"/>
                    </svg>
                </div>
                <p class="text-4xl font-bold leading-none text-slate-950">0</p>
                <p class="mt-2 text-lg text-slate-600">Visitas hoy</p>
            </article>

            <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 md:p-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75L10.5 9.75l3.75 3.75 5.25-5.25M19.5 8.25h-4.5V3.75"/>
                    </svg>
                </div>
                <p class="text-4xl font-bold leading-none text-slate-950">0</p>
                <p class="mt-2 text-lg text-slate-600">Esta semana</p>
            </article>

            <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 md:p-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4.5 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-4xl font-bold leading-none text-slate-950">0</p>
                <p class="mt-2 text-lg text-slate-600">En seguimiento</p>
            </article>

            <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 md:p-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                    </svg>
                </div>
                <p class="text-4xl font-bold leading-none text-slate-950">5</p>
                <p class="mt-2 text-lg text-slate-600">Empresas</p>
            </article>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-5">
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100 md:col-span-2">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-3xl font-bold text-slate-950">Próximas Visitas</h2>
                    <a href="#" class="inline-flex items-center gap-1 text-lg font-semibold text-blue-600 hover:text-blue-700">
                        Ver todas
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/>
                        </svg>
                    </a>
                </div>

                <div class="flex min-h-48 items-center justify-center py-8 text-center text-xl text-slate-500">
                    No hay visitas programadas
                </div>
            </section>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h2 class="text-3xl font-bold text-slate-950">Visitas Recientes</h2>

                <div class="mt-4 space-y-3">
                    @php
                        $recentVisits = [
                            ['company' => 'Mundial armenia', 'date' => '21 ene 2026', 'status' => 'No disponible', 'status_class' => 'bg-gray-200 text-gray-700'],
                            ['company' => 'NANCY ASOCIADOS', 'date' => '19 ene 2026', 'status' => 'No disponible', 'status_class' => 'bg-gray-200 text-gray-700'],
                            ['company' => 'NANCY ASOCIADOS', 'date' => '19 ene 2026', 'status' => 'Venta realizada', 'status_class' => 'bg-green-600 text-white'],
                            ['company' => 'NANCY ASOCIADOS', 'date' => '18 ene 2026', 'status' => 'No disponible', 'status_class' => 'bg-gray-200 text-gray-700'],
                            ['company' => 'NANCY ASOCIADOS', 'date' => '17 ene 2026', 'status' => 'No disponible', 'status_class' => 'bg-gray-200 text-gray-700'],
                        ];
                    @endphp

                    @foreach ($recentVisits as $visit)
                        <article class="flex items-center justify-between gap-3 rounded-xl bg-gray-100 px-3 py-3 md:px-4">
                            <div>
                                <p class="text-2xl font-semibold leading-tight text-slate-950">{{ $visit['company'] }}</p>
                                <p class="mt-1 text-lg text-slate-600">{{ $visit['date'] }}</p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $visit['status_class'] }}">
                                {{ $visit['status'] }}
                            </span>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </section>
@endsection
