@extends('layouts.app')

@section('content')
<section class="space-y-4 pb-24" x-data="accionesCatalogo()">
    <header class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Gestionar Acciones</h1>
            <p class="text-sm text-slate-500">Activa/desactiva y cambia el orden del catálogo.</p>
        </div>
        <a href="{{ url()->previous() }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">Volver</a>
    </header>

    <article class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm space-y-3">
        @foreach($acciones as $accion)
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3 last:border-0" x-data="{activo: {{ $accion->activo ? 'true':'false' }}, orden: {{ (int)$accion->orden }}, saving:false}">
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900">{{ $accion->nombre }}</p>
                    <p class="text-xs text-slate-500">Icono: {{ $accion->icono }} · Color: {{ $accion->color ?: 'default' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" min="0" x-model="orden" class="w-20 rounded-lg border-slate-300 text-sm">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" x-model="activo" class="rounded border-slate-300 text-blue-600">
                        Activo
                    </label>
                    <button class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white" :disabled="saving" @click="saving=true;guardarAccion({{ $accion->id }}, activo, orden).finally(()=>saving=false)">Guardar</button>
                </div>
            </div>
        @endforeach
    </article>
</section>

<script>
function accionesCatalogo(){
    return {
        async guardarAccion(id, activo, orden){
            await fetch(`{{ route('acciones.update', ['accion' => '__ID__']) }}`.replace('__ID__', id), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({activo: activo ? 1 : 0, orden: Number(orden)})
            });
        }
    }
}
</script>
@endsection
