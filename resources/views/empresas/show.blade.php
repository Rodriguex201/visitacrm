@extends('layouts.app')

@section('content')
    <section class="space-y-4">
        <header class="flex flex-wrap items-start justify-between gap-3 rounded-xl bg-transparent">
            <div class="flex min-w-0 items-start gap-3">
                <a href="{{ route('empresas.index') }}" class="mt-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-900" aria-label="Volver a empresas">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                    </svg>
                </a>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">

                        <h1 class="truncate text-xl font-bold md:text-2xl text-slate-950">{{ $empresa['nombre'] }}</h1>
                        <span class="text-sm text-slate-500">{{ $empresa['fecha'] }}</span>
                    </div>
                    <p class="text-sm text-slate-600">{{ $empresa['sector'] ?: 'Sin sector' }}</p>

                </div>
            </div>

            <button type="button" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <span class="text-base leading-none">+</span>
                Nueva Visita
            </button>
        </header>

        <article class="rounded-xl border border-slate-100 bg-white px-4 py-4 shadow-sm">
            <p class="inline-flex items-center gap-2 text-slate-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                    <circle cx="12" cy="9.75" r="2.25" />
                </svg>
                {{ $empresa['ciudad'] }}
            </p>
        </article>

        <article class="space-y-5 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">

                <h2 class="text-xl font-semibold text-slate-950">Actividad (0)</h2>

                <div class="flex items-center gap-2 text-sm font-semibold">
                    <span class="rounded-xl bg-blue-600 px-4 py-2 text-white">Hoy</span>
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-800">7 días</span>
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-800">Todo</span>
                </div>
            </div>

            <p class="text-sm text-slate-600">Sin actividad aún</p>

        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">

                <h2 class="text-xl font-semibold text-slate-950">Contactos</h2>

                <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900">
                    <span class="text-base leading-none">+</span>
                    Agregar
                </button>
            </div>

            <p class="text-center text-sm text-slate-500">Sin contactos registrados</p>
        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-950">Historial de Visitas</h2>
            <p class="text-center text-sm text-slate-500">Sin visitas registradas</p>

        </article>
    </section>
@endsection
