@extends('layouts.app')

@section('content')
<section class="space-y-6">
    <header class="rounded-2xl border border-slate-200 bg-gradient-to-r from-sky-50 via-white to-indigo-50 p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-950">Herramientas disponibles</h1>
        <p class="mt-1 text-sm text-slate-600">Accede fácilmente a nuestras herramientas.</p>
    </header>

    @if ($herramientas->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Aún no hay herramientas activas configuradas.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($herramientas as $herramienta)
                <a
                    href="{{ $herramienta->url }}"
                    @if ($herramienta->abrir_en_nueva_pestana)
                        target="_blank" rel="noopener noreferrer"
                    @endif
                    class="group rounded-2xl p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-lg"
                    style="background: {{ $herramienta->color_fondo ?: '#F8FAFC' }}; color: {{ $herramienta->color_texto ?: '#0F172A' }}"
                >
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white/70 text-2xl ring-1 ring-black/5">
                        {{ $herramienta->icono ?: '🛠️' }}
                    </div>
                    <h2 class="text-xl font-bold" style="color: inherit;">{{ $herramienta->nombre }}</h2>
                    <p class="mt-2 text-sm opacity-90" style="color: inherit;">{{ $herramienta->descripcion ?: 'Ir a la herramienta' }}</p>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection
