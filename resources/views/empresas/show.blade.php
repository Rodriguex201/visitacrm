@extends('layouts.app')

@section('content')
    <section class="space-y-4 pb-24">
        <header class="flex flex-wrap items-start justify-between gap-3 rounded-xl bg-transparent">
            <div class="flex min-w-0 items-start gap-3">
                <a href="{{ route('empresas.index') }}" class="mt-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-900" aria-label="Volver a empresas">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                    </svg>
                </a>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <h1 class="truncate text-xl font-bold md:text-2xl text-slate-950">{{ $empresa->nombre }}</h1>
                        <span class="text-sm text-slate-500">{{ $empresa->created_at?->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm text-slate-600">Sin sector</p>
                </div>
            </div>

            <button type="button" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <span class="text-base leading-none">+</span>
                Nueva Visita
            </button>
        </header>

        <article class="rounded-xl border border-slate-100 bg-white px-4 py-4 shadow-sm">
            <div class="space-y-3 text-slate-600">
                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                        <circle cx="12" cy="9.75" r="2.25" />
                    </svg>
                    {{ $empresa->direccion ?: 'Sin dirección' }}
                </p>

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9 6 9-6" />
                    </svg>
                    @if ($empresa->email)
                        <a href="mailto:{{ $empresa->email }}" class="text-blue-600 hover:underline">{{ $empresa->email }}</a>
                    @else
                        Sin email
                    @endif
                </p>

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h3.214a2.25 2.25 0 012.121 1.497l1.02 3.059a2.25 2.25 0 01-.518 2.314l-1.44 1.44a12.042 12.042 0 005.853 5.853l1.44-1.44a2.25 2.25 0 012.314-.518l3.06 1.02a2.25 2.25 0 011.496 2.121V19.5a2.25 2.25 0 01-2.25 2.25h-.75C9.007 21.75 2.25 14.993 2.25 6.75z" />
                    </svg>
                    {{ $empresa->telefono ?: 'Sin teléfono' }}
                </p>

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5" />
                    </svg>
                    NIT: {{ $empresa->nit ?: 'Sin NIT' }}
                </p>

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                        <circle cx="12" cy="9.75" r="2.25" />
                    </svg>
                    {{ $empresa->ciudad }}
                </p>
            </div>
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
