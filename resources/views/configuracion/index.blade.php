@extends('layouts.app')

@section('content')
<section class="space-y-5" x-data="configuracionPage()">
    <header>
        <h1 class="text-2xl font-bold text-slate-950">Configuración</h1>
        <p class="mt-1 text-sm text-slate-600">Gestión inicial de catálogos del sistema.</p>
    </header>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <nav class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
            <template x-for="tab in tabs" :key="tab.key">
                <button type="button"
                    class="rounded-lg px-3 py-2 text-sm font-semibold transition"
                    :class="activeTab === tab.key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    @click="activeTab = tab.key"
                    x-text="tab.label">
                </button>
            </template>
        </nav>

        <div class="mt-4" x-show="activeTab === 'sectores'" x-cloak>
            @include('configuracion.partials.sectores')
        </div>

        @foreach ($categorias as $slug => $nombre)
            <div class="mt-4" x-show="activeTab === '{{ $slug }}'" x-cloak>
                @include('configuracion.partials.catalogo', [
                    'slug' => $slug,
                    'nombre' => $nombre,
                    'opciones' => $catalogoPorCategoria[$slug] ?? collect(),
                ])
            </div>
        @endforeach

        <div class="mt-4" x-show="activeTab === 'bancos'" x-cloak>
            @include('configuracion.partials.bancos', [
                'bancos' => $bancos,
            ])
        </div>
    </div>
</section>

<script>
    function configuracionPage() {
        return {
            tabs: [
                { key: 'sectores', label: 'Sectores' },
                { key: 'estado-actual', label: 'Estado Actual' },
                { key: 'aplicativos', label: 'Aplicativos' },
                { key: 'procesos-electronicos', label: 'Procesos Electrónicos' },
                { key: 'equipos', label: 'Equipos' },
                { key: 'como-llego', label: 'Como Llego' },
                { key: 'cotizaciones', label: 'Cotizaciones' },
                { key: 'bancos', label: 'Bancos' },
            ],
            activeTab: 'sectores',
        }
    }
</script>
@endsection
