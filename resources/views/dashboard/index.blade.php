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
                 Nueva Visita
            </button>

            <button type="button" @click="openVisitModal = true" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-lg font-semibold text-white shadow-sm transition hover:bg-blue-700 md:hidden">+</button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ session('status') }}</div>
        @endif

<div class="grid grid-cols-2 gap-3 md:grid-cols-4">

    {{-- Visitas hoy --}}
    <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 min-h-[92px]">
        <div class="flex items-center gap-4 h-full">
            <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3.75v3M15.75 3.75v3M4.5 9h15M5.25 6.75h13.5A.75.75 0 0119.5 7.5v11.25a.75.75 0 01-.75.75H5.25a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75z"/>
                </svg>
            </div>

            <div>
                <p class="text-2xl font-bold text-slate-950 leading-none">{{ $countHoy }}</p>
                <p class="mt-1 text-sm text-slate-600">Visitas hoy</p>
            </div>
        </div>
    </article>

    {{-- Esta semana --}}
    <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 min-h-[92px]">
        <div class="flex items-center gap-4 h-full">
            <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75L10.5 9.75l3.75 3.75 5.25-5.25M19.5 8.25h-4.5V3.75"/>
                </svg>
            </div>

            <div>
                <p class="text-2xl font-bold text-slate-950 leading-none">{{ $countSemana }}</p>
                <p class="mt-1 text-sm text-slate-600">Esta semana</p>
            </div>
        </div>
    </article>

    {{-- En seguimiento --}}
    <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 min-h-[92px]">
        <div class="flex items-center gap-4 h-full">
            <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4.5 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div>
                <p class="text-2xl font-bold text-slate-950 leading-none">{{ $enSeguimientoCount }}</p>
                <p class="mt-1 text-sm text-slate-600">En seguimiento</p>
            </div>
        </div>
    </article>

    {{-- Empresas --}}
    <article class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 min-h-[92px]">
        <div class="flex items-center gap-4 h-full">
            <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                </svg>
            </div>

            <div>
                <p class="text-2xl font-bold text-slate-950 leading-none">{{ $countEmpresas }}</p>
                <p class="mt-1 text-sm text-slate-600">Empresas</p>
            </div>
        </div>
    </article>

</div>


        <div class="grid grid-cols-1 gap-3 md:grid-cols-3 md:gap-5">
            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-100 md:col-span-2">

                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-slate-950">Próximas Visitas</h2>
                </div>

                @if ($proximasVisitas->isEmpty())
                    <div class="flex min-h-40 items-center justify-center py-6 text-center text-sm text-slate-500">No hay visitas programadas</div>
                @else
                    <div class="mt-3 space-y-2.5">
                        @foreach ($proximasVisitas as $visita)
                            @php
                                $badgeClass = match ($visita->estado) {
                                    'realizada' => 'bg-emerald-100 text-emerald-700',
                                    'cancelada' => 'bg-rose-100 text-rose-700',
                                    'programada' => 'bg-blue-100 text-blue-700',
                                    default => 'bg-gray-200 text-gray-700',
                                };
                                $badgeText = $visita->estado ? ucfirst($visita->estado) : 'No disponible';
                            @endphp
                            <article class="flex items-center justify-between gap-2 rounded-xl bg-gray-100 px-3 py-2.5 md:px-3.5">
                                <div>
                                    <p class="text-sm font-semibold leading-tight text-slate-950">{{ $visita->empresa?->nombre ?? 'Empresa no disponible' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-600">{{ $visita->fecha_hora?->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="inline-flex shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">{{ $badgeText }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                <h2 class="text-xl font-semibold text-slate-950">Visitas Recientes</h2>

                @if ($visitasRecientes->isEmpty())
                    <p class="mt-3 text-sm text-slate-500">Sin visitas recientes</p>
                @else
                    <div class="mt-3 space-y-2.5">
                        @foreach ($visitasRecientes as $visita)
                            @php
                                $badgeClass = $visita->resultado
                                    ? 'bg-green-600 text-white'
                                    : match ($visita->estado) {
                                        'realizada' => 'bg-emerald-100 text-emerald-700',
                                        'cancelada' => 'bg-rose-100 text-rose-700',
                                        'programada' => 'bg-blue-100 text-blue-700',
                                        default => 'bg-gray-200 text-gray-700',
                                    };

                                $badgeText = $visita->resultado ?: ($visita->estado ? ucfirst($visita->estado) : 'No disponible');
                            @endphp

                            <article class="flex items-center justify-between gap-2 rounded-xl bg-gray-100 px-3 py-2.5 md:px-3.5">
                                <div>
                                    <p class="text-sm font-semibold leading-tight text-slate-950">{{ $visita->empresa?->nombre ?? 'Empresa no disponible' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-600">{{ $visita->fecha_hora?->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="inline-flex shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">{{ $badgeText }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif

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
                                <input type="text" name="empresa_nombre" x-model="empresaQuery" class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20" placeholder="Buscar por nombre o ciudad">
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
                                            <span class="block text-xs text-slate-500" x-text="empresa.ciudad ?? ''"></span>
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
