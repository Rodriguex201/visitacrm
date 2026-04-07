@extends('layouts.app')

@section('content')
<section class="space-y-6">
    <header class="rounded-3xl border border-slate-200 bg-gradient-to-r from-sky-50 via-white to-indigo-50 p-6 shadow-sm md:p-8">
        <h1 class="text-2xl font-bold text-slate-950 md:text-3xl">Qué puedo ofrecer</h1>
        <p class="mt-1 text-sm text-slate-600 md:text-base">Galería de soluciones y servicios disponibles para mostrar a tus clientes.</p>
    </header>

    @if ($ofrecerItems->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Aún no hay elementos activos para ofrecer.
        </div>
    @else
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @foreach ($ofrecerItems as $item)
                <article class="group overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-black/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                    @if($item->imagen)
                        <img src="{{ asset('storage/' . $item->imagen) }}" class="h-48 w-full rounded-xl object-cover">
                    @endif
                    <div class="space-y-2 p-4">
                        @if ($item->titulo)
                            <h3 class="text-base font-semibold text-slate-900">{{ $item->titulo }}</h3>
                        @endif
                        @if ($item->descripcion)
                            <p class="text-sm text-slate-600">{{ $item->descripcion }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
@endsection
