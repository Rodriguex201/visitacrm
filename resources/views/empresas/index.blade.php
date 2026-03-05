@extends('layouts.app')

@section('content')
    @php($esAdministracion = (auth()->user()?->tipo_usuario ?? null) === 'administracion')

    <section
        x-data="{
            openModal: {{ $errors->any() ? 'true' : 'false' }},
            openEdit: {{ old('modal_mode') === 'edit' ? 'true' : 'false' }},
            editId: {{ old('empresa_id') ? (int) old('empresa_id') : 'null' }},
            updateRouteTemplate: @js(route('empresas.update', ['empresa' => '__ID__'])),
            formAction: '{{ old('modal_mode') === 'edit' && old('empresa_id') ? route('empresas.update', ['empresa' => old('empresa_id')]) : route('empresas.store') }}',
            form: {
                nombre: @js(old('nombre', '')),
                // nit: @js(old('nit', '')),
                ciudad: @js(old('ciudad', '')),
                ciudad_codigo: @js(old('ciudad_codigo', '')),
                contacto_nombre: @js(old('contacto_nombre', '')),
                direccion: @js(old('direccion', '')),
                telefono: @js(old('telefono', '')),
                email: @js(old('email', '')),
                sector_id: @js(old('sector_id', '')),
                notas: @js(old('notas', '')),
            },
            notesModalOpen: false,
            notesModalCompanyName: '',
            notesModalContent: '',
            cityResults: [],
            cityLoading: false,
            emptyForm() {
                return {
                    nombre: '',
                    // nit: '',
                    ciudad: '',
                    ciudad_codigo: '',
                    contacto_nombre: '',
                    direccion: '',
                    telefono: '',
                    email: '',
                    sector_id: '',
                    notas: '',
                }
            },
            openCreateModal() {
                this.openEdit = false
                this.editId = null
                this.form = this.emptyForm()
                this.formAction = '{{ route('empresas.store') }}'
                this.cityResults = []
                this.cityLoading = false
                this.openModal = true
            },
            openEditModal(empresa) {
                this.openEdit = true
                this.editId = empresa.id
                this.form = {
                    nombre: empresa.nombre ?? '',
                    // nit: empresa.nit ?? '',
                    ciudad: empresa.ciudad ?? '',
                    ciudad_codigo: '',
                    contacto_nombre: empresa.contacto_nombre ?? '',
                    direccion: empresa.direccion ?? '',
                    telefono: empresa.telefono ?? '',
                    email: empresa.email ?? '',
                    sector_id: empresa.sector_id ?? '',
                    notas: empresa.notas ?? '',
                }
                this.formAction = this.updateRouteTemplate.replace('__ID__', empresa.id)
                this.cityResults = []
                this.cityLoading = false
                this.openModal = true
            },
            closeModal() {
                this.openModal = false
                this.cityResults = []
                this.cityLoading = false
            },
            openNotesModal(empresa) {
                if (!empresa.notas) {
                    return
                }

                this.notesModalCompanyName = empresa.nombre ?? ''
                this.notesModalContent = empresa.notas ?? ''
                this.notesModalOpen = true
            },
            closeNotesModal() {
                this.notesModalOpen = false
                this.notesModalCompanyName = ''
                this.notesModalContent = ''
            },
            async searchCity() {
                const query = (this.form.ciudad ?? '').trim()

                if (query.length === 0) {
                    this.cityResults = []
                    return
                }

                this.cityLoading = true

                try {
                    const response = await fetch(`/api/ciudades?query=${encodeURIComponent(query)}`, {
                        headers: {
                            Accept: 'application/json',
                        },
                    })

                    if (!response.ok) {
                        this.cityResults = []
                        return
                    }

                    this.cityResults = await response.json()
                } catch (error) {
                    this.cityResults = []
                } finally {
                    this.cityLoading = false
                }
            },
            selectCity(city) {
                this.form.ciudad = city.citynomb ?? ''
                this.form.ciudad_codigo = city.citycodigo ?? ''
                this.cityResults = []
            },
        }"
        class="space-y-4"
    >
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-950">Empresas</h1>
                <p class="mt-1 text-sm text-slate-600">Gestiona tus clientes</p>
            </div>

            <button
                type="button"
                @click="openCreateModal()"
                class="inline-flex h-10 items-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                + Nueva
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif


        <form method="GET" action="{{ route('empresas.index') }}" class="space-y-2">
                    <div class="grid gap-2 md:grid-cols-12">
                        <div class="relative md:col-span-3">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="q"
                                value="{{ $filters['q'] ?? '' }}"
                                placeholder="Buscar por nombre o ciudad..."
                                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <input
                            type="date"
                            name="desde"
                            value="{{ $filters['desde'] ?? '' }}"
                            class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:col-span-2"
                        >

                        <input
                            type="date"
                            name="hasta"
                            value="{{ $filters['hasta'] ?? '' }}"
                            class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:col-span-2"
                        >

                        <select

                            name="estado"

                            class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:col-span-2"
                        >
                            <option value="">Estado (Todos)</option>
                            <option value="pendiente" @selected(($filters['estado'] ?? '') === 'pendiente')>Pendiente</option>
                            <option value="aprobado" @selected(($filters['estado'] ?? '') === 'aprobado')>Aprobado</option>
                            <option value="rechazado" @selected(($filters['estado'] ?? '') === 'rechazado')>Rechazado</option>
                        </select>

                        <button
                            type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 md:col-span-1"
                        >
                            Filtrar
                        </button>

                        <a
                            href="{{ route('empresas.filters.clear') }}"
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 md:col-span-1"
                        >
                            Limpiar
                        </a>
                    </div>
                </form>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                        style="background-color:#FEF3C7; color:#92400E;">
                        <span class="h-2 w-2 rounded-full" style="background-color:#92400E;"></span>
                        Pendiente
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                        style="background-color:#DCFCE7; color:#166534;">
                        <span class="h-2 w-2 rounded-full" style="background-color:#166534;"></span>
                        Aprobado
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold"
                        style="background-color:#FEE2E2; color:#991B1B;">
                        <span class="h-2 w-2 rounded-full" style="background-color:#991B1B;"></span>
                        Rechazado
                    </span>
                </div>

        <p class="text-xs text-slate-500">
            @if ($usaRangoPersonalizado)
                Mostrando empresas
                @if ($desde)
                    desde {{ $desde->format('d/m/Y') }}
                @endif
                @if ($hasta)
                    hasta {{ $hasta->format('d/m/Y') }}
                @endif
            @else
                Mostrando empresas del mes actual
            @endif
        </p>

        <div class="grid grid-cols-1 gap-6 space-y-4 lg:grid-cols-3 lg:items-start lg:space-y-0">
            <div class="space-y-4 lg:col-span-2">
                <div class="space-y-3 pb-24">
                    @forelse ($empresas as $empresa)
                        @php($estadoRef = $empresa->referido_estado ?? 'pendiente')
                        @php($estadoRefColor = $referidoEstadoColors[$estadoRef] ?? $referidoEstadoColors['pendiente'])
                        @php($estadoRefStyle = 'border-color: ' . $estadoRefColor['bg_color'] . '; background-color: ' . $estadoRefColor['bg_color'] . ';')
                        @php($estadoRefBadgeStyle = 'background-color: ' . $estadoRefColor['bg_color'] . '; color: ' . $estadoRefColor['text_color'] . ';')
                        <article
                            x-data="{ empresa: @js([
                                'id' => $empresa->id,
                                'nombre' => $empresa->nombre,
                                // 'nit' => $empresa->nit,
                                'ciudad' => $empresa->ciudad,
                                'contacto_nombre' => $empresa->contacto_nombre,
                                'direccion' => $empresa->direccion,
                                'telefono' => $empresa->telefono,
                                'email' => $empresa->email,
                                'sector_id' => $empresa->sector_id,
                                'notas' => $empresa->notas,
                            ]) }"
                            @click="window.location.href='{{ route('empresas.show', $empresa) }}'"
                            class="group flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-50 hover:shadow-md"
                            style="{{ $estadoRefStyle }}"
                        >
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-colors duration-200 group-hover:bg-blue-200/80">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-base font-semibold text-slate-950">{{ $empresa->nombre }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                                       <span class="inline-flex items-center gap-1">
                                             {{ optional($empresa->created_at)->format('d/m/Y') }}
                                        </span>
                                    <span class="inline-flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                                                <circle cx="12" cy="9.75" r="2.25" />
                                            </svg>
                                            {{ $empresa->ciudad }}
                                        </span>
                                        {{--
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25v13.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V5.25z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 4.5h6" />
                                            </svg>
                                            {{ $empresa->nit ?: 'Sin NIT' }}
                                        </span>
                                        --}}
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5" />
                                            </svg>
                                            {{ $empresa->sector?->nombre ?: 'Sin sector' }}
                                        </span>

                                    </div>

                                    @if ($empresa->contacto_nombre)
                                        <p class="mt-1 flex items-center gap-1 text-sm text-slate-500">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a8.966 8.966 0 0114.998 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.499-1.632z" />
                                            </svg>
                                            <span>Contacto: {{ $empresa->contacto_nombre }}</span>
                                        </p>
                                    @endif

                                    @if ($esAdministracion)
                                        @if ($empresa->referida_at)
                                            <span class="mt-1 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-600">
                                                🔁 Referido por: {{ $empresa->responsable?->codigo ?? 'S/C' }}
                                            </span>
                                            <div class="mt-1 flex flex-wrap items-center gap-1">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium" style="{{ $estadoRefBadgeStyle }}">Estado: {{ ucfirst($estadoRef) }}</span>
                                                @if ($empresa->referido_estado === 'aprobado')
                                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700">
                                                        Comisión: $ {{ number_format((float) ($empresa->comision_valor ?? 0), 0, ',', '.') }}

                                                    </span>
                                                @endif
                                                @if (($empresa->referido_estado === 'aprobado') && !is_null($empresa->referido_aprobado_at))
                                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                                        Aprobado: {{ optional($empresa->referido_aprobado_at)->format('d/m/Y H:i') }}

                                                    </span>
                                                @endif
                                            </div>
                                        @elseif (($empresa->creador?->tipo_usuario === 'administracion') && $empresa->creador?->codigo)
                                            <span class="mt-1 inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-600">
                                                🔁 Referido por: {{ $empresa->creador->codigo }}
                                            </span>
                                            <div class="mt-1 flex flex-wrap items-center gap-1">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium" style="{{ $estadoRefBadgeStyle }}">Estado: {{ ucfirst($estadoRef) }}</span>
                                                @if ($empresa->referido_estado === 'aprobado')
                                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700">
                                                        Comisión: $ {{ number_format((float) ($empresa->comision_valor ?? 0), 0, ',', '.') }}

                                                    </span>
                                                @endif
                                                @if (($empresa->referido_estado === 'aprobado') && !is_null($empresa->referido_aprobado_at))
                                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                                        Aprobado: {{ optional($empresa->referido_aprobado_at)->format('d/m/Y H:i') }}

                                                    </span>
                                                @endif
                                            </div>
                                        @elseif ($empresa->responsable_user_id)
                                            <p class="mt-1 truncate text-xs text-slate-500">
                                                Responsable: {{ $empresa->responsable?->codigo ?: 'S/C' }} - {{ strtoupper($empresa->responsable?->name ?? $empresa->responsable?->nombre ?? 'Sin nombre') }} - {{ $empresa->responsable?->telefono ?: 'Sin teléfono' }}
                                            </p>
                                        @endif
                                    @endif

                                    @if (! $esAdministracion && ($empresa->referido_estado === 'aprobado'))
                                        <div class="mt-1 flex flex-wrap items-center gap-1">
                                            <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-medium text-sky-700">
                                                Comisión: $ {{ number_format((float) ($empresa->comision_valor ?? 0), 0, ',', '.') }}
                                            </span>

                                            @if (!is_null($empresa->referido_aprobado_at))
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                                    Aprobado: {{ optional($empresa->referido_aprobado_at)->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif (! $esAdministracion && ($empresa->referido_estado === 'aprobado') && !is_null($empresa->referido_aprobado_at))
                                        <div class="mt-1 flex flex-wrap items-center gap-1">
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700">
                                                Aprobado: {{ optional($empresa->referido_aprobado_at)->format('d/m/Y H:i') }}
                                            </span>


                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-1">
                                <button
                                    type="button"
                                    @click.stop="openEditModal(empresa)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.121 2.121 0 113 3L8.25 19.1l-4.5 1.5 1.5-4.5 11.612-11.613z" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    @click.stop="openNotesModal(empresa)"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg transition {{ $empresa->notas ? 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' : 'cursor-not-allowed text-slate-300' }}"
                                    @if (!$empresa->notas) disabled @endif
                                    title="{{ $empresa->notas ? 'Ver notas' : 'Sin notas' }}"
                                    aria-label="{{ $empresa->notas ? 'Ver notas de ' . $empresa->nombre : 'Sin notas para ' . $empresa->nombre }}"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h9A2.25 2.25 0 0118.75 6v12A2.25 2.25 0 0116.5 20.25h-9A2.25 2.25 0 015.25 18V6A2.25 2.25 0 017.5 3.75z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25h7.5m-7.5 3h7.5m-7.5 3h4.5" />
                                    </svg>
                                </button>

                                @if ($esAdministracion)
                                    <form
                                        method="POST"
                                        action="{{ route('empresas.destroy', $empresa) }}"
                                        @click.stop
                                        onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-rose-500 transition hover:bg-rose-50 hover:text-rose-700"
                                            aria-label="Eliminar {{ $empresa->nombre }}"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7.5h12m-9.75 0V6A1.5 1.5 0 019.75 4.5h4.5A1.5 1.5 0 0115.75 6v1.5m-8.25 0V18A1.5 1.5 0 009 19.5h6A1.5 1.5 0 0016.5 18V7.5m-6 3v6m3-6v6" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif


                                <a
                                    href="{{ route('empresas.show', $empresa) }}"
                                    @click.stop
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                    aria-label="Ver detalle de {{ $empresa->nombre }}"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @empty
                        <article class="rounded-xl border border-slate-100 bg-white p-4 text-sm text-slate-600 shadow-sm">
                            No hay empresas registradas
                        </article>
                    @endforelse
                </div>

                <div>
                    {{ $empresas->links() }}
                </div>
            </div>

            <div class="lg:col-span-1">

                    <aside class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:sticky lg:top-4">
                        <h2 class="text-base font-semibold text-slate-900">Resumen</h2>
                        <dl class="mt-4 space-y-3">
                            <div class="rounded-lg bg-slate-50 p-3">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Comisión total</dt>
                                <dd class="mt-1 text-lg font-semibold text-slate-900">

                                    $ {{ number_format((float) ($totalComision ?? 0), 0, ',', '.') }}

                                </dd>
                            </div>
                            <div class="rounded-lg bg-slate-50 p-3">
                                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Total empresas</dt>

                                <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $totalEmpresas ?? 0 }}</dd>
                            </div>
                        </dl>
                    </aside>
                </div>

        </div>

        <div
            x-show="notesModalOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="closeNotesModal()"
            x-cloak
        ></div>

        <div
            x-show="notesModalOpen"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900" x-text="`Notas de ${notesModalCompanyName}`"></h2>
                    <button type="button" @click="closeNotesModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm whitespace-pre-line text-slate-700" x-text="notesModalContent"></div>

                <button type="button" @click="closeNotesModal()" class="mt-4 inline-flex h-10 w-full items-center justify-center rounded-lg bg-slate-900 text-sm font-semibold text-white transition hover:bg-slate-700">Cerrar</button>
            </div>
        </div>

        @include('empresas.partials.modal_empresa')
    </section>
@endsection
