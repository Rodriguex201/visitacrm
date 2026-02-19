@extends('layouts.app')

@section('content')
    <section
        x-data="{
            openVisitModal: {{ $errors->any() ? 'true' : 'false' }},
            empresaQuery: @js(old('empresa_nombre', '')),
            empresaId: @js(old('empresa_id', '')),
            selectedEmpresa: @js(old('empresa_nombre', '')),
            empresaResults: [],
            empresaLoading: false,
            async searchEmpresa() {
                const query = (this.empresaQuery ?? '').trim()

                if (query.length === 0) {
                    this.empresaResults = []
                    this.empresaId = ''
                    this.selectedEmpresa = ''
                    return
                }

                this.empresaLoading = true

                try {
                    const response = await fetch(`/api/empresas?query=${encodeURIComponent(query)}`, {
                        headers: { Accept: 'application/json' },
                    })

                    if (!response.ok) {
                        this.empresaResults = []
                        return
                    }

                    this.empresaResults = await response.json()
                } catch (error) {
                    this.empresaResults = []
                } finally {
                    this.empresaLoading = false
                }
            },
            selectEmpresa(empresa) {
                this.empresaId = empresa.id
                this.selectedEmpresa = empresa.nombre
                this.empresaQuery = empresa.nombre
                this.empresaResults = []
            },
            closeVisitModal() {
                this.openVisitModal = false
                this.empresaResults = []
            },
        }"
        class="space-y-5"
    >
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold leading-tight text-slate-950">Dashboard</h1>
                <p class="mt-1 text-base text-slate-600">Resumen de actividad comercial</p>
            </div>

            <button type="button" @click="openVisitModal = true" class="hidden items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 md:inline-flex">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5M5.25 12h13.5"/>
                </svg>
                + Nueva Visita
            </button>

            <button type="button" @click="openVisitModal = true" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-lg font-semibold text-white shadow-sm transition hover:bg-blue-700 md:hidden">+</button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <article class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100 md:p-4"><p class="text-sm text-slate-600">Visitas hoy</p></article>
            <article class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100 md:p-4"><p class="text-sm text-slate-600">Esta semana</p></article>
            <article class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100 md:p-4"><p class="text-sm text-slate-600">En seguimiento</p></article>
            <article class="rounded-xl bg-white p-3.5 shadow-sm ring-1 ring-slate-100 md:p-4"><p class="text-sm text-slate-600">Empresas</p></article>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-3 md:gap-5">
            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-100 md:col-span-2">
                <h2 class="text-2xl font-bold text-slate-950">Próximas Visitas</h2>
                <div class="flex min-h-40 items-center justify-center py-6 text-center text-base text-slate-500">No hay visitas programadas</div>
            </section>
            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <h2 class="text-2xl font-bold text-slate-950">Visitas Recientes</h2>
                <p class="mt-3 text-sm text-slate-500">Sin visitas recientes</p>
            </section>
        </div>

        <template x-if="openVisitModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeVisitModal()">
                <div class="absolute inset-0 bg-slate-900/45"></div>
                <div class="relative z-10 w-full max-w-2xl rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-950">Nueva visita</h3>
                        <button type="button" @click="closeVisitModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">✕</button>
                    </div>

                    <form method="POST" action="{{ route('visitas.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="empresa_id" x-model="empresaId">

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Empresa</label>
                            <div class="relative flex items-center gap-2">
                                <input type="text" name="empresa_nombre" x-model="empresaQuery" class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" placeholder="Buscar por nombre, NIT o ciudad">
                                <button type="button" @click="searchEmpresa()" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" /></svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500" x-show="selectedEmpresa">Seleccionada: <span x-text="selectedEmpresa"></span></p>
                            <p class="mt-1 text-xs text-rose-600">@error('empresa_id') {{ $message }} @enderror</p>

                            <div x-show="empresaLoading" class="mt-2 text-xs text-slate-500">Buscando...</div>
                            <div x-show="empresaResults.length > 0" class="mt-2 max-h-48 overflow-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                                <template x-for="empresa in empresaResults" :key="empresa.id">
                                    <button type="button" @click="selectEmpresa(empresa)" class="flex w-full items-start justify-between gap-2 border-b border-slate-100 px-3 py-2 text-left text-sm last:border-b-0 hover:bg-slate-50">
                                        <span>
                                            <span class="block font-medium text-slate-800" x-text="empresa.nombre"></span>
                                            <span class="block text-xs text-slate-500" x-text="`NIT: ${empresa.nit ?? 'N/A'} · ${empresa.ciudad ?? ''}`"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Fecha y hora</label>
                                <input type="datetime-local" name="fecha_hora" value="{{ old('fecha_hora') }}" required class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <p class="mt-1 text-xs text-rose-600">@error('fecha_hora') {{ $message }} @enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                                <select name="estado" required class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                    <option value="programada" @selected(old('estado') === 'programada')>Programada</option>
                                    <option value="realizada" @selected(old('estado') === 'realizada')>Realizada</option>
                                    <option value="cancelada" @selected(old('estado') === 'cancelada')>Cancelada</option>
                                </select>
                                <p class="mt-1 text-xs text-rose-600">@error('estado') {{ $message }} @enderror</p>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Resultado</label>
                            <input type="text" name="resultado" value="{{ old('resultado') }}" class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <p class="mt-1 text-xs text-rose-600">@error('resultado') {{ $message }} @enderror</p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Notas</label>
                            <textarea name="notas" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">{{ old('notas') }}</textarea>
                            <p class="mt-1 text-xs text-rose-600">@error('notas') {{ $message }} @enderror</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Guardar visita</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </section>
@endsection
