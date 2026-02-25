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
                </div>
            </div>
        </header>

        <article class="rounded-xl border border-slate-100 bg-white px-4 py-4 shadow-sm">
            <div class="space-y-3 text-slate-600">
                <p class="text-sm"><span class="font-semibold text-slate-800">Ciudad:</span> {{ $empresa->ciudad ?: 'Sin ciudad' }}</p>
                <p class="text-sm"><span class="font-semibold text-slate-800">Sector:</span> {{ $empresa->sector?->nombre ?: 'Sin sector' }}</p>
                <p class="text-sm"><span class="font-semibold text-slate-800">Teléfono:</span> {{ $empresa->telefono ?: 'Sin teléfono' }}</p>
                <p class="text-sm">
                    <span class="font-semibold text-slate-800">Email:</span>
                    @if ($empresa->email)
                        <a href="mailto:{{ $empresa->email }}" class="text-blue-600 hover:underline">{{ $empresa->email }}</a>
                    @else
                        Sin email
                    @endif
                </p>
                <p class="text-sm"><span class="font-semibold text-slate-800">Dirección:</span> {{ $empresa->direccion ?: 'Sin dirección' }}</p>
            </div>
        </article>
    </section>
@endsection
