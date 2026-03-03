@extends('layouts.app')

@section('content')
<section class="space-y-5" x-data="configuracionPage()">
    <header>
        <h1 class="text-2xl font-bold text-slate-950">Configuración</h1>
        <p class="mt-1 text-sm text-slate-600">Gestiona catálogos y parámetros del sistema.</p>
    </header>

    <div class="border-b border-slate-200">
        <nav class="-mb-px flex items-center gap-6">
            <button type="button" class="border-b-2 px-1 pb-3 text-sm font-semibold"
                :class="activeTab === 'sectores' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                @click="activeTab = 'sectores'">
                Sectores
            </button>
        </nav>
    </div>

    <div x-show="activeTab === 'sectores'" x-cloak>
        @include('configuracion.partials.sectores')
    </div>
</section>

<script>
    function configuracionPage() {
        return {
            activeTab: 'sectores',
        }
    }
</script>
@endsection
