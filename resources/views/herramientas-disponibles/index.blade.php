@extends('layouts.app')

@section('content')
<section class="space-y-6">
    <header class="rounded-3xl border border-slate-200 bg-gradient-to-r from-sky-50 via-white to-indigo-50 p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-slate-950 md:text-3xl">Herramientas disponibles</h1>
        <p class="mt-1 text-sm text-slate-600 md:text-base">Accesos rápidos a tus herramientas del CRM.</p>
    </header>

    @if ($herramientas->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Aún no hay herramientas activas configuradas.
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($herramientas as $herramienta)
                <a
                    href="{{ $herramienta->url }}"
                    @if ($herramienta->abrir_en_nueva_pestana)
                        target="_blank" rel="noopener noreferrer"
                    @endif
                    class="group relative flex h-full flex-col overflow-hidden rounded-3xl p-6 shadow-sm ring-1 ring-black/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                    style="background: {{ $herramienta->color_fondo ?: '#F8FAFC' }}; color: {{ $herramienta->color_texto ?: '#0F172A' }}"
                >
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-white/20 to-transparent"></div>

                    <div class="relative flex items-start justify-between gap-4">
                        <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-white/65 ring-1 ring-black/10 backdrop-blur-sm">
                            <x-tool-icon
                                :name="$herramienta->icono"
                                size="28"
                                class="shrink-0"
                                style="color: {{ $herramienta->color_texto ?: '#0F172A' }}"
                            />
                        </div>

                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/55 text-current ring-1 ring-black/10 transition group-hover:translate-x-0.5">
                            <x-lucide-icon name="link" size="18" class="opacity-90" />
                        </span>
                    </div>

                    <div class="relative mt-5 space-y-2">
                        <h2 class="text-lg font-semibold leading-tight md:text-xl">{{ $herramienta->nombre }}</h2>
                        <p class="text-sm leading-relaxed opacity-90 md:text-[0.95rem]">
                            {{ $herramienta->descripcion ?: 'Ir a la herramienta' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection
