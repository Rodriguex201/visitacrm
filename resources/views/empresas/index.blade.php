@extends('layouts.app')

@section('content')
    @php($esAdministracion = (auth()->user()?->tipo_usuario ?? null) === 'administracion')

    <section
        x-data="{
            openModal: {{ $errors->any() ? 'true' : 'false' }},
            openEdit: {{ old('modal_mode') === 'edit' ? 'true' : 'false' }},
            editId: {{ old('empresa_id') ? (int) old('empresa_id') : 'null' }},
            updateRouteTemplate: @js(route('empresas.update', ['empresa' => '__ID__'])),
            showRouteTemplate: @js(route('empresas.show', ['empresa' => '__ID__'])),
            destroyRouteTemplate: @js(route('empresas.destroy', ['empresa' => '__ID__'])),
            formAction: '{{ old('modal_mode') === 'edit' && old('empresa_id') ? route('empresas.update', ['empresa' => old('empresa_id')]) : route('empresas.store') }}',
            contextMenuOpen: false,
            contextMenuX: 0,
            contextMenuY: 0,
            contextMenuEmpresa: null,
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
            openContextMenu(event, empresa) {
                const menuWidth = 224
                const menuHeight = 220
                const padding = 12

                this.contextMenuEmpresa = empresa
                this.contextMenuX = Math.min(event.clientX, window.innerWidth - menuWidth - padding)
                this.contextMenuY = Math.min(event.clientY, window.innerHeight - menuHeight - padding)
                this.contextMenuOpen = true
            },
            closeContextMenu() {
                this.contextMenuOpen = false
                this.contextMenuEmpresa = null
            },
            openEditFromMenu() {
                if (!this.contextMenuEmpresa) {
                    return
                }

                this.openEditModal(this.contextMenuEmpresa)
                this.closeContextMenu()
            },
            openNotesFromMenu() {
                if (!this.contextMenuEmpresa) {
                    return
                }

                this.openNotesModal(this.contextMenuEmpresa)
                this.closeContextMenu()
            },
            openShowFromMenu() {
                if (!this.contextMenuEmpresa) {
                    return
                }

                window.location.href = this.showRouteTemplate.replace('__ID__', this.contextMenuEmpresa.id)
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
        @click="closeContextMenu()"
        @keydown.escape.window="closeContextMenu()"
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
                <div class="relative md:col-span-5">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Buscar por nombre o ciudad..."
                        class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <input
                    type="date"
                    name="desde"
                    value="{{ $desdeInput }}"
                    class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:col-span-2"
                >

                <input
                    type="date"
                    name="hasta"
                    value="{{ $hastaInput }}"
                    class="h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:col-span-2"
                >

                <button
                    type="submit"
                    class="inline-flex h-10 items-center justify-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 md:col-span-2"
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('empresas.index') }}"
                    class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 md:col-span-1"
                >
                    Limpiar
                </a>
            </div>
        </form>

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

        <div class="overflow-x-auto pb-24">
            <div class="min-w-[1480px] space-y-3">
                @forelse ($empresas as $empresa)
                    <article
                        x-data="{ empresa: @js([
                            'id' => $empresa->id,
                            'nombre' => $empresa->nombre,
                            'ciudad' => $empresa->ciudad,
                            'contacto_nombre' => $empresa->contacto_nombre,
                            'direccion' => $empresa->direccion,
                            'telefono' => $empresa->telefono,
                            'email' => $empresa->email,
                            'sector_id' => $empresa->sector_id,
                            'notas' => $empresa->notas,
                        ]) }"
                        @click="window.location.href='{{ route('empresas.show', $empresa) }}'"
                        @contextmenu.prevent.stop="openContextMenu($event, empresa)"
                        class="group flex min-w-[1480px] cursor-pointer items-center gap-3 rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-50 hover:shadow-md"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-colors duration-200 group-hover:bg-blue-200/80">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5"/>
                            </svg>
                        </div>

                        <div class="flex min-w-0 flex-1 flex-nowrap items-start gap-4 overflow-x-visible">
                            <div class="flex w-[160px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Fecha de registro</span>
                                <span class="text-sm text-slate-700">{{ optional($empresa->created_at)->format('d/m/Y') }}</span>
                            </div>

                            <div class="flex w-[190px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Código</span>
                                @if ($empresa->referida_at)
                                    <span class="truncate text-sm text-slate-700">Referido por: {{ $empresa->creador?->codigo ?: 'S/C' }}</span>
                                @else
                                    <span class="truncate text-sm text-slate-700">{{ $empresa->creador?->codigo ?: 'S/C' }}</span>
                                @endif
                            </div>

                            <div class="flex w-[220px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Empresa</span>
                                <span class="truncate text-sm font-semibold text-slate-900">{{ $empresa->nombre }}</span>
                            </div>

                            <div class="flex w-[150px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Teléfono</span>
                                <span class="truncate text-sm text-slate-700">{{ $empresa->telefono ?: '—' }}</span>
                            </div>

                            <div class="flex w-[170px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Sector</span>
                                <span class="truncate text-sm text-slate-700">{{ $empresa->sector?->nombre ?: 'Sin sector' }}</span>
                            </div>

                            <div class="flex w-[170px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Ciudad</span>
                                <span class="truncate text-sm text-slate-700">{{ $empresa->ciudad ?: '—' }}</span>
                            </div>

                            <div class="flex w-[120px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Aprobado</span>
                                <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Pendiente</span>
                            </div>

                            <div class="flex w-[120px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Rechazado</span>
                                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">—</span>
                            </div>

                            <div class="flex w-[120px] shrink-0 flex-col">
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Comisión</span>
                                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">—</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="rounded-xl border border-slate-100 bg-white p-4 text-sm text-slate-600 shadow-sm">
                        No hay empresas registradas
                    </article>
                @endforelse
            </div>
        </div>

        <div>
            {{ $empresas->links() }}
        </div>

        <div
            x-show="contextMenuOpen"
            x-transition
            x-cloak
            class="fixed z-[70] w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl"
            :style="`left: ${contextMenuX}px; top: ${contextMenuY}px;`"
            @click.stop
        >
            <button
                type="button"
                @click="openEditFromMenu()"
                class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100"
            >
                Editar
            </button>

            <button
                type="button"
                @click="openNotesFromMenu()"
                class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm transition"
                :class="contextMenuEmpresa?.notas ? 'text-slate-700 hover:bg-slate-100' : 'cursor-not-allowed text-slate-300'"
                :disabled="!contextMenuEmpresa?.notas"
            >
                Notas
            </button>

            @if ($esAdministracion)
                <form
                    method="POST"
                    :action="destroyRouteTemplate.replace('__ID__', contextMenuEmpresa?.id ?? '')"
                    @submit="closeContextMenu()"
                    onsubmit="return confirm('¿Seguro que deseas eliminar esta empresa?')"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50"
                    >
                        Eliminar
                    </button>
                </form>
            @endif

            <button
                type="button"
                @click="openShowFromMenu()"
                class="flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100"
            >
                Ver / Abrir
            </button>
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

        <div
            x-show="openModal"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="closeModal()"
            x-cloak
        ></div>

        <div
            x-show="openModal"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900" x-text="openEdit ? 'Editar Empresa' : 'Nueva Empresa'"></h2>
                    <button type="button" @click="closeModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                        <ul class="list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form :action="formAction" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <input type="hidden" name="modal_mode" :value="openEdit ? 'edit' : 'create'">
                    <input type="hidden" name="empresa_id" :value="editId">
                    <template x-if="openEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    @if ($esAdministracion)
                        @include('empresas.partials.modal_form_full')
                    @else
                        @include('empresas.partials.modal_form_basic')
                    @endif

                    <button type="submit" class="mt-1 inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700" x-text="openEdit ? 'Guardar cambios' : 'Crear Empresa'"></button>
                </form>
            </div>
        </div>
    </section>
@endsection
