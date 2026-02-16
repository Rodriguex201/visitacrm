@extends('layouts.app')

@section('content')
    <section
        x-data="{
            openModal: {{ $errors->any() ? 'true' : 'false' }},
            openEdit: {{ old('modal_mode') === 'edit' ? 'true' : 'false' }},
            editId: {{ old('empresa_id') ? (int) old('empresa_id') : 'null' }},
            updateRouteTemplate: @js(route('empresas.update', ['empresa' => '__ID__'])),
            formAction: '{{ old('modal_mode') === 'edit' && old('empresa_id') ? route('empresas.update', ['empresa' => old('empresa_id')]) : route('empresas.store') }}',
            form: {
                nombre: @js(old('nombre', '')),
                nit: @js(old('nit', '')),
                ciudad: @js(old('ciudad', '')),
                direccion: @js(old('direccion', '')),
                telefono: @js(old('telefono', '')),
                email: @js(old('email', '')),
            },
            emptyForm() {
                return {
                    nombre: '',
                    nit: '',
                    ciudad: '',
                    direccion: '',
                    telefono: '',
                    email: '',
                }
            },
            openCreateModal() {
                this.openEdit = false
                this.editId = null
                this.form = this.emptyForm()
                this.formAction = '{{ route('empresas.store') }}'
                this.openModal = true
            },
            openEditModal(empresa) {
                this.openEdit = true
                this.editId = empresa.id
                this.form = {
                    nombre: empresa.nombre ?? '',
                    nit: empresa.nit ?? '',
                    ciudad: empresa.ciudad ?? '',
                    direccion: empresa.direccion ?? '',
                    telefono: empresa.telefono ?? '',
                    email: empresa.email ?? '',
                }
                this.formAction = this.updateRouteTemplate.replace('__ID__', empresa.id)
                this.openModal = true
            },
            closeModal() {
                this.openModal = false
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

        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                </svg>
            </span>
            <input
                type="text"
                placeholder="Buscar por nombre, NIT o ciudad..."
                class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div class="space-y-3 pb-24">
            @forelse ($empresas as $empresa)
                <article
                    x-data="{ empresa: @js([
                        'id' => $empresa->id,
                        'nombre' => $empresa->nombre,
                        'nit' => $empresa->nit,
                        'ciudad' => $empresa->ciudad,
                        'direccion' => $empresa->direccion,
                        'telefono' => $empresa->telefono,
                        'email' => $empresa->email,
                    ]) }"
                    @click="window.location.href='{{ route('empresas.show', $empresa) }}'"
                    class="group flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-slate-50 hover:shadow-md"
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
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                                        <circle cx="12" cy="9.75" r="2.25" />
                                    </svg>
                                    {{ $empresa->ciudad }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25v13.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V5.25z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 4.5h6" />
                                    </svg>
                                    {{ $empresa->nit ?: 'Sin NIT' }}
                                </span>
                            </div>
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

                    <div>
                        <label class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
                        <input x-model="form.nombre" name="nombre" type="text" placeholder="Nombre de la empresa" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block font-semibold text-slate-700">NIT</label>
                            <input x-model="form.nit" name="nit" type="text" placeholder="NIT" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block font-semibold text-slate-700">Ciudad *</label>
                            <input x-model="form.ciudad" name="ciudad" type="text" placeholder="Ciudad" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block font-semibold text-slate-700">Dirección</label>
                        <input x-model="form.direccion" name="direccion" type="text" placeholder="Dirección" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
                            <input x-model="form.telefono" name="telefono" type="text" placeholder="Teléfono" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block font-semibold text-slate-700">Email</label>
                            <input x-model="form.email" name="email" type="email" placeholder="Email" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>

                    <button type="submit" class="mt-1 inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700" x-text="openEdit ? 'Guardar cambios' : 'Crear Empresa'"></button>
                </form>
            </div>
        </div>
    </section>
@endsection
