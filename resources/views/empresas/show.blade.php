@extends('layouts.app')

@section('content')
    <section class="space-y-4 pb-24" x-data="historialVisitas()" x-init="init()">
        <header class="flex flex-wrap items-start justify-between gap-3 rounded-xl bg-transparent">
            <div class="flex min-w-0 items-start gap-3">
                <a href="{{ route('empresas.index') }}" class="mt-1 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-200/70 hover:text-slate-900" aria-label="Volver a empresas">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                    </svg>
                </a>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <h1 class="truncate text-xl font-bold md:text-2xl text-slate-950">{{ $empresa->nombre }}</h1>
                        <span class="text-sm text-slate-500">{{ $empresa->created_at?->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm text-slate-600">{{ $empresa->sector?->nombre ?: 'Sin sector' }}</p>
                </div>
            </div>

            <button
                type="button"
                @click="openNuevaVisitaModal({ empresa_id: {{ (int) $empresa->id }}, empresa_label: @js($empresa->nombre), lock_empresa: true })"
                class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                <span class="text-base leading-none">+</span>
                Nueva Visita
            </button>
        </header>


        <div class="grid gap-4 lg:grid-cols-2 lg:items-start">
            <article class="rounded-xl border border-slate-100 bg-white px-4 py-4 shadow-sm">
                <div class="space-y-3 text-slate-600">
                    <p class="flex items-center gap-2 text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                            <circle cx="12" cy="9.75" r="2.25" />
                        </svg>
                        {{ $empresa->direccion ?: 'Sin dirección' }}
                    </p>

                    <p class="flex items-center gap-2 text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9 6 9-6" />
                        </svg>
                        @if ($empresa->email)
                            <a href="mailto:{{ $empresa->email }}" class="text-blue-600 hover:underline">{{ $empresa->email }}</a>
                        @else
                            Sin email
                        @endif
                    </p>

                    <p class="flex items-center gap-2 text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h3.214a2.25 2.25 0 012.121 1.497l1.02 3.059a2.25 2.25 0 01-.518 2.314l-1.44 1.44a12.042 12.042 0 005.853 5.853l1.44-1.44a2.25 2.25 0 012.314-.518l3.06 1.02a2.25 2.25 0 011.496 2.121V19.5a2.25 2.25 0 01-2.25 2.25h-.75C9.007 21.75 2.25 14.993 2.25 6.75z" />
                        </svg>
                        {{ $empresa->telefono ?: 'Sin teléfono' }}
                    </p>

                    <p class="flex items-center gap-2 text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7.5h3a2.25 2.25 0 012.25 2.25v8.25A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V9.75A2.25 2.25 0 016 7.5h3m6 0V6A3 3 0 009 6v1.5m6 0h-6" />
                        </svg>
                        {{ $empresa->contacto_nombre ?: 'Sin contacto' }}
                    </p>

                    <p class="flex items-center gap-2 text-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                            <circle cx="12" cy="9.75" r="2.25" />
                        </svg>
                        {{ $empresa->ciudad }}
                    </p>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-semibold text-slate-700">Notas</h3>
                    @if ($empresa->notas)
                        <p class="mt-2 text-sm text-slate-600 whitespace-pre-line">{{ $empresa->notas }}</p>
                    @else
                        <p class="mt-2 text-sm text-slate-400 italic">Sin notas</p>
                    @endif
                </div>
            </article>

            <div class="space-y-4">
                <article class="space-y-3 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Gestion Inicial</h2>
                            <p class="text-xs text-slate-500">Selección múltiple</p>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                            @click="abrirPerfilComercialModal()"
                        >
                            Configurar
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-2">

                        <template x-for="chip in savedOptionChips().slice(0, 6)" :key="chip.id">
                            <span class="group relative inline-flex">
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-slate-700">
                                    <span class="text-[10px] opacity-70" x-text="chip.categoriaAbreviada"></span>
                                    <span class="text-xs" x-text="chip.nombre"></span>
                                </span>

                                <template x-if="chip.nota">
                                    <span
                                        class="absolute left-0 top-full z-50 mt-2 hidden w-64 rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-700 shadow-lg group-hover:block"
                                        x-text="chip.nota"
                                    ></span>
                                </template>
                            </span>
                        </template>

                        <template x-if="savedOptionChips().length > 6">
                            <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700" x-text="`+${savedOptionChips().length - 6}`"></span>
                        </template>

                        <template x-if="savedOptionChips().length === 0">

                            <span class="text-sm text-slate-500">Sin opciones seleccionadas</span>
                        </template>
                    </div>


                </article>

                @if ((auth()->user()?->tipo_usuario ?? null) === 'administracion')

                    <article class="space-y-3 rounded-xl border border-slate-100 bg-white p-5 shadow-sm" x-data="{ referidoModalOpen: @js($errors->hasAny(['referido_estado', 'referido_motivo_rechazo', 'comision_estado'])) }">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-950">Referido / Comisión</h2>
                                <p class="text-xs text-slate-500">Estado actual</p>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                @click="referidoModalOpen = true"
                            >
                                Opciones
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="referidoBadgeClass(referidoForm.referido_estado)" x-text="`Estado: ${referidoLabel(referidoForm.referido_estado)}`"></span>

                            <template x-if="referidoForm.referido_estado === 'aprobado'">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700" x-text="`Comisión: ${referidoForm.comision_valor ? referidoForm.comision_valor : 'Sin valor'}`"></span>
                            </template>

                            <template x-if="referidoForm.referido_estado === 'aprobado'">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700" x-text="`Comisión ${referidoForm.comision_estado === 'pagada' ? 'Pagada' : 'Pendiente'}`"></span>
                            </template>
                        </div>

                        <div class="space-y-1 text-xs text-slate-500">
                            <p x-show="referidoForm.referido_aprobado_at" x-text="`Aprobado: ${formatDateTime(referidoForm.referido_aprobado_at)}`"></p>
                            <p x-show="referidoForm.comision_pagada_at" x-text="`Comisión pagada: ${formatDateTime(referidoForm.comision_pagada_at)}`"></p>

                        </div>

                        <p x-show="referidoError" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="referidoError"></p>
                        <p x-show="referidoSuccess" x-transition.opacity class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700" x-text="referidoSuccess"></p>


                    <div
                        x-cloak
                        x-show="referidoModalOpen"
                        class="fixed inset-0 z-50 flex items-center justify-center px-4"
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="modal-title-referido"
                        @keydown.escape.window="referidoModalOpen = false"
                    >
                        <div class="absolute inset-0 bg-slate-900/40" @click="referidoModalOpen = false"></div>

                        <div class="relative z-10 w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 id="modal-title-referido" class="text-lg font-semibold text-slate-900">Referido / Comisión</h3>
                                <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" @click="referidoModalOpen = false" aria-label="Cerrar modal">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form class="space-y-4" @submit.prevent="guardarReferidoEstado()">
                                <div>
                                    <label for="referido_estado" class="mb-1 block text-sm font-medium text-slate-700">Estado del referido</label>
                                    <select id="referido_estado" x-model="referidoForm.referido_estado" name="referido_estado" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                    <p x-show="referidoErrors.referido_estado" class="mt-1 text-xs text-rose-600" x-text="referidoErrors.referido_estado"></p>
                                </div>

                                <div x-show="referidoForm.referido_estado === 'rechazado'">
                                    <label for="referido_motivo_rechazo" class="mb-1 block text-sm font-medium text-slate-700">Motivo de rechazo</label>
                                    <textarea id="referido_motivo_rechazo" x-model="referidoForm.referido_motivo_rechazo" name="referido_motivo_rechazo" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Indique el motivo" :required="referidoForm.referido_estado === 'rechazado'"></textarea>
                                    <p x-show="referidoErrors.referido_motivo_rechazo" class="mt-1 text-xs text-rose-600" x-text="referidoErrors.referido_motivo_rechazo"></p>
                                </div>

                                <div x-show="referidoForm.referido_estado === 'aprobado'">
                                    <label for="comision_valor" class="mb-1 block text-sm font-medium text-slate-700">Valor comisión (automático)</label>
                                    <input id="comision_valor" :value="formatCurrency(referidoForm.comision_valor)" type="text" readonly class="w-full rounded-lg border-slate-300 bg-slate-50 text-sm text-slate-600 focus:border-blue-500 focus:ring-blue-500" placeholder="$ 0">
                                    <p class="mt-1 text-xs text-slate-500">Se calcula al guardar: suma de opciones activas (excepto Cotizaciones y Como Llego).</p>
                                </div>

                                <div>
                                    <label for="comision_estado" class="mb-1 block text-sm font-medium text-slate-700">Estado de comisión</label>
                                    <select id="comision_estado" x-model="referidoForm.comision_estado" name="comision_estado" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="pagada">Pagada</option>
                                    </select>
                                    <p x-show="referidoErrors.comision_estado" class="mt-1 text-xs text-rose-600" x-text="referidoErrors.comision_estado"></p>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" :disabled="referidoSaving">
                                        <span x-show="!referidoSaving">Guardar estado</span>
                                        <span x-show="referidoSaving">Guardando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    </article>
                @endif
            </div>
        </div>

        {{--
        <article class="space-y-3 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Responsable</h2>
                <button
                    type="button"
                    @click="abrirModalUsuario()"
                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.121 2.121 0 113 3L8.25 19.1l-4.5 1.5 1.5-4.5 11.612-11.613z" />
                    </svg>
                    <span x-text="empresaUser ? 'Cambiar' : 'Vincular usuario'"></span>
                </button>
            </div>

            <p
                x-show="usuarioSuccess"
                x-transition.opacity
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700"
                x-text="usuarioSuccess"
            ></p>

            <template x-if="empresaUser && empresaReferidaAt">
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-600">
                    <span x-text="`🔁 Referido por: ${empresaUser?.codigo || 'S/C'}`"></span>
                </span>
            </template>

            <template x-if="empresaUser && !empresaReferidaAt">
                <p class="text-sm text-slate-600" x-text="`Responsable: ${empresaUser?.codigo || 'S/C'} - ${(empresaUser?.name || empresaUser?.nombre || 'Sin nombre').toUpperCase()} - ${empresaUser?.telefono || 'Sin teléfono'}`"></p>
            </template>
        </article>
        --}}

        <article class="space-y-4 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Acciones</h2>
                <a href="{{ route('acciones.manage') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Gestionar</a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @forelse($accionesCatalogo as $accion)
                    <button
                        type="button"
                        class="group relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                        @click="registrarAccion({{ (int) $accion->id }})"
                        :disabled="accionesGuardando"
                        title="{{ $accion->nombre }}"
                    >
                        <span class="sr-only">{{ $accion->nombre }}</span>
                        <span aria-hidden="true"><x-lucide-icon :name="$accion->icono" :color="$accion->color ?: null" /></span>
                        <span class="pointer-events-none absolute -top-9 left-1/2 hidden -translate-x-1/2 rounded-md bg-slate-900 px-2 py-1 text-xs font-medium text-white shadow-sm group-hover:block">{{ $accion->nombre }}</span>
                    </button>
                @empty
                    <p class="text-sm text-slate-500">No hay acciones activas.</p>
                @endforelse
            </div>
            <p x-show="accionesError" class="text-sm text-rose-600" x-text="accionesError"></p>
            <p x-show="accionSuccess" x-transition.opacity class="text-sm text-emerald-600" x-text="accionSuccess"></p>
        </article>

        <article class="space-y-5 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Actividad (<span x-text="actividadCount">{{ $acciones->count() }}</span>)</h2>

                <div class="flex items-center gap-2 text-sm font-semibold" data-target="actividad">
                    @foreach (['hoy' => 'Hoy', '7' => '7 días', 'todo' => 'Todo'] as $rangeValue => $rangeLabel)
                        <button
                            type="button"
                            data-target="actividad"
                            data-range="{{ $rangeValue }}"
                            @click="aplicarFiltro('actividad', '{{ $rangeValue }}')"
                            :disabled="loadingActividad"
                            :class="filterButtonClass('actividad', '{{ $rangeValue }}')"
                            class="rounded-xl border px-4 py-2 transition"
                        >
                            <span>{{ $rangeLabel }}</span>
                            <span x-show="loadingActividad && currentActRange === '{{ $rangeValue }}'" class="ml-1 inline-block h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="actividad-list">
                @include('empresas.partials.actividad_list', ['acciones' => $acciones, 'empresa' => $empresa])
            </div>
        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-xl font-semibold text-slate-950">Contactos</h2>

                <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900" @click="abrirModalContacto()">
                    <span class="text-base leading-none">+</span>
                    Agregar
                </button>
            </div>

            <div id="contactos-list">
                @include('empresas.partials.contactos-list', ['contactos' => $contactos])
            </div>

            <div
                x-cloak
                x-show="showContactoModal"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-contacto"
                @keydown.escape.window="cerrarModalContacto()"
            >
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarModalContacto()"></div>

                <div class="relative z-10 w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">

                        <h3 id="modal-title-contacto" class="text-lg font-semibold text-slate-900" x-text="isEditContacto ? 'Editar contacto' : 'Nuevo Contacto'"></h3>

                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="cerrarModalContacto()" aria-label="Cerrar modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="contactoFormError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="contactoFormError"></div>

                    <form class="space-y-4" @submit.prevent="guardarContacto()">
                        <div>
                            <label for="contacto_nombre" class="mb-1 block text-sm font-medium text-slate-700">Nombre*</label>
                            <input id="contacto_nombre" x-model="contactoForm.nombre" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <p x-show="contactoErrors.nombre" class="mt-1 text-xs text-rose-600" x-text="contactoErrors.nombre"></p>
                        </div>

                        <div>
                            <label for="contacto_cargo" class="mb-1 block text-sm font-medium text-slate-700">Cargo</label>
                            <input id="contacto_cargo" x-model="contactoForm.cargo" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <p x-show="contactoErrors.cargo" class="mt-1 text-xs text-rose-600" x-text="contactoErrors.cargo"></p>
                        </div>

                        <div>
                            <label for="contacto_telefono" class="mb-1 block text-sm font-medium text-slate-700">Teléfono</label>
                            <input id="contacto_telefono" x-model="contactoForm.telefono" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <p x-show="contactoErrors.telefono" class="mt-1 text-xs text-rose-600" x-text="contactoErrors.telefono"></p>
                        </div>

                        <div>
                            <label for="contacto_email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                            <input id="contacto_email" x-model="contactoForm.email" type="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <p x-show="contactoErrors.email" class="mt-1 text-xs text-rose-600" x-text="contactoErrors.email"></p>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input x-model="contactoForm.es_principal" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Contacto principal
                        </label>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cerrarModalContacto()">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="isSubmittingContacto">

                                <span x-show="!isSubmittingContacto" x-text="isEditContacto ? 'Guardar cambios' : 'Agregar Contacto'"></span>

                                <span x-show="isSubmittingContacto">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Historial de Visitas (<span x-text="visitasCount">{{ $visitas->count() }}</span>)</h2>

                <div class="flex items-center gap-2 text-sm font-semibold" data-target="visitas">
                    @foreach (['hoy' => 'Hoy', '7' => '7 días', 'todo' => 'Todo'] as $rangeValue => $rangeLabel)
                        <button
                            type="button"
                            data-target="visitas"
                            data-range="{{ $rangeValue }}"
                            @click="aplicarFiltro('visitas', '{{ $rangeValue }}')"
                            :disabled="loadingVisitas"
                            :class="filterButtonClass('visitas', '{{ $rangeValue }}')"
                            class="rounded-xl border px-4 py-2 transition"
                        >
                            <span>{{ $rangeLabel }}</span>
                            <span x-show="loadingVisitas && currentVisRange === '{{ $rangeValue }}'" class="ml-1 inline-block h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="visitas-list">
                @include('empresas.partials.visitas_list', ['visitas' => $visitas, 'empresa' => $empresa])
            </div>

            <div
                x-cloak
                x-show="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-resultado"
                @keydown.escape.window="cerrarModal()"
            >
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarModal()"></div>

                <div class="relative z-10 w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 id="modal-title-resultado" class="text-lg font-semibold text-slate-900">Actualizar resultado</h3>
                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="cerrarModal()" aria-label="Cerrar modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="formError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="formError"></div>

                    <form class="space-y-4" @submit.prevent="guardarResultado()">
                        <div>
                            <label for="resultado" class="mb-1 block text-sm font-medium text-slate-700">Resultado de la visita*</label>
                            <select id="resultado" x-model="form.resultado" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Selecciona resultado</option>
                                <option value="venta_realizada">Venta realizada</option>
                                <option value="en_seguimiento">En seguimiento</option>
                                <option value="sin_interes">Sin interés</option>
                                <option value="no_disponible">No disponible</option>
                            </select>
                            <p x-show="errors.resultado" class="mt-1 text-xs text-rose-600" x-text="errors.resultado"></p>
                        </div>

                        <div>
                            <label for="nivel_interes" class="mb-1 block text-sm font-medium text-slate-700">Nivel de interés</label>
                            <select
                                id="nivel_interes"
                                x-model="form.nivel_interes"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                :disabled="form.resultado === 'sin_interes'"
                            >
                                <option value="">Sin definir</option>
                                <option value="alto" :disabled="form.resultado === 'sin_interes'">Alto</option>
                                <option value="medio" :disabled="form.resultado === 'sin_interes'">Medio</option>
                                <option value="bajo" :disabled="form.resultado === 'sin_interes'">Bajo</option>
                                <option value="sin_interes" :disabled="['venta_realizada','en_seguimiento'].includes(form.resultado)">Sin interés</option>
                            </select>
                            <p x-show="errors.nivel_interes" class="mt-1 text-xs text-rose-600" x-text="errors.nivel_interes"></p>
                            <p class="mt-1 text-xs text-slate-500">Si seleccionas "Sin interés", se guardará automáticamente ese nivel.</p>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cerrarModal()">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="isSubmitting">
                                <span x-show="!isSubmitting">Guardar</span>
                                <span x-show="isSubmitting">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </article>

            <div x-cloak x-show="showUsuarioModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" @keydown.escape.window="cerrarModalUsuario()">
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarModalUsuario()"></div>

                <div class="relative z-10 w-full max-w-xl rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Vincular usuario</h3>
                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" @click="cerrarModalUsuario()">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="usuarioFormError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="usuarioFormError"></div>
                    <div x-show="usuarioSuccess" class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700" x-text="usuarioSuccess"></div>

                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" x-model="usuarioQuery" @input.debounce.300ms="buscarUsuarios()" placeholder="Buscar por código, nombre o teléfono" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                            <button type="button" @click="buscarUsuarios()" class="rounded-lg border border-slate-300 px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Buscar</button>
                        </div>

                        <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-200">
                            <template x-if="usuarioLoading">
                                <p class="px-3 py-2 text-sm text-slate-500">Buscando usuarios...</p>
                            </template>
                            <template x-if="!usuarioLoading && !usuariosResultados.length">
                                <p class="px-3 py-2 text-sm text-slate-500">Sin resultados</p>
                            </template>
                            <template x-for="usuario in usuariosResultados" :key="usuario.id">
                                <button type="button" @click="usuarioSeleccionadoId = usuario.id" class="flex w-full items-start justify-between border-b border-slate-100 px-3 py-2 text-left hover:bg-slate-50">
                                    <span class="text-sm text-slate-700" x-text="`${usuario.codigo || 'S/C'} - ${usuario.name || usuario.nombre || 'Sin nombre'}`"></span>
                                    <span class="text-xs text-slate-500" x-text="usuario.telefono || 'Sin teléfono'"></span>
                                </button>
                            </template>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="cerrarModalUsuario()">Cancelar</button>
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="guardarUsuarioAsignado(null)" :disabled="usuarioSaving">Quitar</button>
                            <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60" @click="guardarUsuarioAsignado(usuarioSeleccionadoId)" :disabled="usuarioSaving || !usuarioSeleccionadoId">
                                <span x-show="!usuarioSaving">Confirmar</span>
                                <span x-show="usuarioSaving">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div
                x-cloak

                x-show="modalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-perfil-comercial"
                @keydown.escape.window="cerrarPerfilComercialModal()"
            >
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarPerfilComercialModal()"></div>

                <div class="relative z-10 w-full max-w-4xl rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 id="modal-title-perfil-comercial" class="text-lg font-semibold text-slate-900">Gestion Inicial</h3>
                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" @click="cerrarPerfilComercialModal()" aria-label="Cerrar modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2 border-b border-slate-100 pb-3">
                        <template x-for="tab in modalTabs" :key="`tab-${tab}`">
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                                :class="activeTab === tab ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                @click="activeTab = tab"
                                x-text="tab"
                            ></button>
                        </template>
                    </div>

                    <template x-if="opcionesMensaje">
                        <div class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700" x-text="opcionesMensaje"></div>
                    </template>

                    <template x-if="activeTab !== 'Cotizaciones' && activeTab !== 'Como Llego'">
                        <div>
                            <div class="mb-3 flex flex-wrap gap-2" x-show="modalOpen">
                                <template x-for="chip in draftOptionChips().slice(0, 8)" :key="`draft-${chip.id}`">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-blue-700">
                                        <span class="text-[10px] opacity-70" x-text="chip.categoriaAbreviada"></span>
                                        <span class="text-xs" x-text="chip.nombre"></span>
                                    </span>
                                </template>

                                <template x-if="draftOptionChips().length > 8">
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700" x-text="`+${draftOptionChips().length - 8}`"></span>
                                </template>

                                <template x-if="draftOptionChips().length === 0">
                                    <span class="text-xs text-slate-500">Sin selección en borrador</span>
                                </template>
                            </div>

                            <div class="max-h-[55vh] overflow-y-auto rounded-lg border border-slate-100 bg-slate-50/40 p-4">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-slate-900" x-text="activeTab"></h4>
                                    <button
                                        type="button"
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100"
                                        title="Agregar opción"
                                        @click="abrirModalOpcion(activeTab)"
                                    >
                                        +
                                    </button>
                                </div>

                                <div class="space-y-1.5">
                                    <template x-for="opcion in opcionesPorCategoria[activeTab] || []" :key="`tab-option-${opcion.id}`">
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input
                                                type="checkbox"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                :value="opcion.id"
                                                :checked="isOpcionSeleccionada(opcion.id)"
                                                @change="toggleOpcion(opcion.id)"
                                            >
                                            <span x-text="opcion.nombre"></span>
                                        </label>
                                    </template>
                                </div>

                                <div class="mt-4 rounded-lg border border-slate-200 bg-white p-3">
                                    <div class="mb-1 flex items-center justify-between gap-2">
                                        <label class="text-sm font-medium text-slate-700" :for="`nota_categoria_${activeTab}`" x-text="`Notas de ${activeTab}`"></label>
                                    </div>
                                    <textarea
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                        rows="3"
                                        :id="`nota_categoria_${activeTab}`"
                                        placeholder="Escribe una nota para esta categoría"
                                        :value="notaCategoriaActual(activeTab)"
                                        @input="setNotaCategoria(activeTab, $event.target.value)"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeTab === 'Como Llego'">
                        <div class="rounded-lg border border-slate-100 bg-slate-50/40 p-4">
                            <div class="space-y-3">
                                <template x-for="opcion in comoLlegoOpciones" :key="`como-llego-${opcion.id}`">
                                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input
                                                type="checkbox"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                :checked="isComoLlegoSeleccionada(opcion.id)"
                                                @change="toggleComoLlego(opcion.id, $event.target.checked)"
                                            >
                                            <span x-text="opcion.nombre"></span>
                                        </label>

                                        <template x-if="opcion.requiere_texto">
                                            <input
                                                type="text"
                                                class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 disabled:bg-slate-100"
                                                :placeholder="placeholderComoLlego(opcion.nombre)"
                                                :disabled="!isComoLlegoSeleccionada(opcion.id)"
                                                :value="comoLlegoTexto(opcion.id)"
                                                @input="setComoLlegoTexto(opcion.id, $event.target.value)"
                                            >
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeTab === 'Cotizaciones'">
                        <div class="rounded-lg border border-slate-100 bg-slate-50/40 p-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input
                                    type="checkbox"
                                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    x-model="draftCotizacionEnviada"
                                    :disabled="opcionesSaving"
                                >
                                <span>Enviada</span>
                            </label>

                            <p class="mt-2 text-xs text-slate-600" x-show="savedCotizacionEnviada && cotizacionEnviadaAtFormateada(savedCotizacionEnviadaAt)">
                                Enviada el: <span class="font-semibold" x-text="cotizacionEnviadaAtFormateada(savedCotizacionEnviadaAt)"></span>
                            </p>

                            <div class="mt-3">
                                <label for="cotizacion_numero" class="mb-1 block text-sm font-medium text-slate-700">Número de cotización</label>
                                <input
                                    id="cotizacion_numero"
                                    type="text"
                                    placeholder="Ej: COT-000123"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    x-model="form.cotizacion_numero"
                                    :disabled="opcionesSaving"
                                >
                            </div>
                        </div>
                    </template>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cerrarPerfilComercialModal()">Cerrar</button>
                        <button
                            type="button"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
                            :disabled="opcionesSaving"
                            @click="guardarOpcionesEmpresa()"
                        >
                            <span x-show="!opcionesSaving">Guardar cambios</span>
                            <span x-show="opcionesSaving">Guardando...</span>
                        </button>
                    </div>
                </div>
            </div>


            <div
                x-cloak

                x-show="showCatalogoModal"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-catalogo"
                @keydown.escape.window="cerrarModalOpcion()"
            >
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarModalOpcion()"></div>

                <div class="relative z-10 w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 id="modal-title-catalogo" class="text-lg font-semibold text-slate-900">Agregar opción</h3>
                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" @click="cerrarModalOpcion()" aria-label="Cerrar modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="mb-2 text-sm text-slate-600">Categoría: <span class="font-semibold text-slate-900" x-text="newOptionCategoria"></span></p>

                    <div x-show="newOptionError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="newOptionError"></div>

                    <form class="space-y-4" @submit.prevent="guardarNuevaOpcion()">
                        <div>
                            <label for="nueva_opcion_nombre" class="mb-1 block text-sm font-medium text-slate-700">Nombre de la opción</label>
                            <input id="nueva_opcion_nombre" x-model="newOptionNombre" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="flex justify-end gap-2 pt-1">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cerrarModalOpcion()">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" :disabled="newOptionSaving">
                                <span x-show="!newOptionSaving">Guardar</span>
                                <span x-show="newOptionSaving">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <div
                x-cloak
                x-show="showAccionModal"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-accion"
                @keydown.escape.window="cerrarModalAccion()"
            >
                <div class="absolute inset-0 bg-slate-900/40" @click="cerrarModalAccion()"></div>

                <div class="relative z-10 w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 id="modal-title-accion" class="text-lg font-semibold text-slate-900">Editar acción</h3>
                        <button type="button" class="rounded-md p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" @click="cerrarModalAccion()" aria-label="Cerrar modal">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="accionModalError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="accionModalError"></div>

                    <form class="space-y-4" @submit.prevent="guardarEdicionAccion()">
                        <div>
                            <label for="editar_accion_id" class="mb-1 block text-sm font-medium text-slate-700">Acción</label>
                            <select id="editar_accion_id" x-model.number="accionForm.accion_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <template x-for="accion in accionesCatalogo" :key="accion.id">
                                    <option :value="accion.id" x-text="accion.nombre"></option>
                                </template>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-1">
                            <button type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cerrarModalAccion()">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" :disabled="accionModalSaving">
                                <span x-show="!accionModalSaving">Guardar</span>
                                <span x-show="accionModalSaving">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @include('visitas.partials.nueva_visita_modal')
    </section>



    <script>
        function historialVisitas() {
            return {
                ...createNuevaVisitaModalState({
                    onSuccess: async () => {
                        await this.aplicarFiltro('visitas', this.currentVisRange);
                    },
                }),
                showModal: false,
                showContactoModal: false,
                showUsuarioModal: false,
                isSubmitting: false,
                isSubmittingContacto: false,
                visitaId: null,
                formError: '',
                contactoFormError: '',
                usuarioFormError: '',
                errors: {},
                contactoErrors: {},
                empresaUser: @js($empresa->responsable ? ['id' => $empresa->responsable->id, 'codigo' => $empresa->responsable->codigo, 'name' => $empresa->responsable->name ?? $empresa->responsable->nombre, 'nombre' => $empresa->responsable->nombre ?? $empresa->responsable->name, 'telefono' => $empresa->responsable->telefono] : null),
                empresaReferidaAt: @js($empresa->referida_at?->toIso8601String()),
                usuarioSuccess: '',
                actividadPartialUrl: @js(route('empresas.actividad.partial', $empresa)),
                visitasPartialUrl: @js(route('empresas.visitas.partial', $empresa)),
                usuarioQuery: '',
                usuariosResultados: [],
                usuarioSeleccionadoId: null,
                usuarioLoading: false,
                usuarioSaving: false,
                categoriasOpciones: @js($categoriasOpciones),
                modalTabs: [...@js($categoriasOpciones), 'Como Llego', 'Cotizaciones'],
                opcionesPorCategoria: @js($catalogoOpcionesPayload),
                comoLlegoOpciones: @js($comoLlegoOpciones->map(fn ($opcion) => [
                    'id' => (int) $opcion->id,
                    'nombre' => $opcion->nombre,
                    'requiere_texto' => (bool) $opcion->requiere_texto,
                ])->values()),
                savedComoLlego: @js($comoLlegoSeleccionado),
                draftComoLlego: [],
                savedCategoriaNotas: @js($categoriaNotasPayload),
                draftCategoriaNotas: @js($categoriaNotasPayload),

                savedSelectedIds: @js($opcionesSeleccionadas),
                draftSelectedIds: [],
                modalOpen: false,
                activeTab: "Estado Actual",

                savedCotizacionEnviada: @js((bool) $empresa->cotizacion_enviada),
                savedCotizacionEnviadaAt: @js(optional($empresa->cotizacion_enviada_at)->toIso8601String()),
                draftCotizacionEnviada: @js((bool) $empresa->cotizacion_enviada),
                savedCotizacionNumero: @js($empresa->cotizacion_numero),

                opcionesSaving: false,
                opcionesMensaje: '',
                showCatalogoModal: false,
                newOptionCategoria: '',
                newOptionNombre: '',
                newOptionSaving: false,
                newOptionError: '',
                accionesGuardando: false,
                accionesError: '',
                accionSuccess: '',
                esAdministracion: @js((auth()->user()?->tipo_usuario ?? null) === 'administracion'),
                referidoUpdateUrl: @js(route('empresas.referido.update', $empresa)),
                referidoSaving: false,
                referidoError: '',
                referidoSuccess: '',
                referidoErrors: {},
                referidoForm: @js($referidoPayload),
                accionesCatalogo: @js($accionesCatalogo->map(fn ($accion) => ['id' => (int) $accion->id, 'nombre' => $accion->nombre])->values()),
                accionUpdateUrlTemplate: @js(route('empresas.acciones.update', ['empresa' => $empresa, 'empresaAccion' => '__ACCION__'])),
                accionDeleteUrlTemplate: @js(route('empresas.acciones.destroy', ['empresa' => $empresa, 'empresaAccion' => '__ACCION__'])),
                showAccionModal: false,
                accionModalSaving: false,
                accionModalError: '',
                accionForm: {
                    empresa_accion_id: null,
                    accion_id: null,
                },
                actividadActionsBound: false,
                currentActRange: @js($actRange),
                currentVisRange: @js($visRange),
                actividadCount: @js($acciones->count()),
                visitasCount: @js($visitas->count()),
                loadingActividad: false,
                loadingVisitas: false,

                form: {
                    resultado: '',
                    nivel_interes: '',
                    cotizacion_numero: @js($empresa->cotizacion_numero),
                },

                contactoEditId: null,
                isEditContacto: false,

                contactoForm: {
                    nombre: '',
                    cargo: '',
                    telefono: '',
                    email: '',
                    es_principal: false,
                },
                init() {
                    this.initNuevaVisitaModal();
                    this.bindContactoEditButtons();
                    this.bindVisitaActions();
                    this.bindActividadActions();

                    this.$watch('form.resultado', (value) => {
                        if (value === 'sin_interes') {
                            this.form.nivel_interes = 'sin_interes';
                        }

                        if (value === 'no_disponible') {
                            this.form.nivel_interes = '';
                        }

                        if (['venta_realizada', 'en_seguimiento'].includes(value) && this.form.nivel_interes === 'sin_interes') {
                            this.form.nivel_interes = '';
                        }
                    });
                },


                bindVisitaActions() {
                    this.$root.addEventListener('click', (event) => {
                        const trigger = event.target.closest('[data-open-resultado="true"]');
                        if (!trigger) {
                            return;
                        }

                        const visitaId = Number(trigger.dataset.visitaId || 0);
                        if (!visitaId) {
                            return;
                        }

                        this.abrirModal({ id: visitaId });
                    });
                },

                filterButtonClass(target, range) {
                    const isActive = target === 'actividad'
                        ? this.currentActRange === range
                        : this.currentVisRange === range;

                    if (isActive) {
                        return 'bg-blue-600 text-white border-blue-600';
                    }

                    return 'bg-white text-slate-800 border-slate-200';
                },

                async aplicarFiltro(target, range) {
                    const isActividad = target === 'actividad';
                    const loadingKey = isActividad ? 'loadingActividad' : 'loadingVisitas';

                    if (this[loadingKey]) {
                        return;
                    }

                    this[loadingKey] = true;

                    try {
                        const url = new URL(isActividad ? this.actividadPartialUrl : this.visitasPartialUrl, window.location.origin);
                        if (isActividad) {
                            url.searchParams.set('act_range', range);
                        } else {
                            url.searchParams.set('vis_range', range);
                        }

                        const response = await fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'text/html',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('No se pudo cargar el filtro');
                        }

                        const html = await response.text();
                        const containerId = isActividad ? 'actividad-list' : 'visitas-list';
                        const container = document.getElementById(containerId);

                        if (!container) {
                            return;
                        }

                        container.innerHTML = html;

                        if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                            window.Alpine.initTree(container);
                        }

                        if (isActividad) {
                            this.currentActRange = range;
                            this.actividadCount = Number(container.querySelector('[data-actividad-count]')?.dataset.actividadCount ?? this.actividadCount);
                            this.bindActividadActions();
                        } else {
                            this.currentVisRange = range;
                            this.visitasCount = Number(container.querySelector('[data-visitas-count]')?.dataset.visitasCount ?? this.visitasCount);
                        }

                        const pageUrl = new URL(window.location.href);
                        pageUrl.searchParams.set('act_range', this.currentActRange);
                        pageUrl.searchParams.set('vis_range', this.currentVisRange);
                        window.history.replaceState({}, '', pageUrl.toString());
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this[loadingKey] = false;
                    }
                },



                bindActividadActions() {
                    if (this.actividadActionsBound) {
                        return;
                    }

                    this.actividadActionsBound = true;

                    document.addEventListener('click', (event) => {
                        if (!this.esAdministracion) {
                            return;
                        }

                        const editTrigger = event.target.closest('[data-edit-actividad="1"], [data-accion-edit="true"]');
                        if (editTrigger) {
                            const empresaAccionId = Number(editTrigger.dataset.actividadId || editTrigger.dataset.empresaAccionId || 0);
                            const accionId = Number(editTrigger.dataset.accionId || 0);

                            if (empresaAccionId && accionId) {
                                this.abrirModalAccion(empresaAccionId, accionId);
                            }
                            return;
                        }

                        const deleteTrigger = event.target.closest('[data-accion-delete="true"]');
                        if (deleteTrigger) {
                            const empresaAccionId = Number(deleteTrigger.dataset.empresaAccionId || 0);
                            if (empresaAccionId) {
                                this.eliminarAccion(empresaAccionId);
                            }
                        }
                    });
                },

                abrirModalAccion(empresaAccionId, accionId) {
                    this.accionModalError = '';
                    this.accionForm.empresa_accion_id = empresaAccionId;
                    this.accionForm.accion_id = accionId;
                    this.showAccionModal = true;
                },

                cerrarModalAccion() {
                    this.showAccionModal = false;
                    this.accionModalSaving = false;
                    this.accionModalError = '';
                    this.accionForm.empresa_accion_id = null;
                    this.accionForm.accion_id = null;
                },

                buildAccionUrl(template, empresaAccionId) {
                    return template.replace('__ACCION__', String(empresaAccionId));
                },

                mostrarAccionSuccess(message) {
                    this.accionSuccess = message;
                    setTimeout(() => {
                        this.accionSuccess = '';
                    }, 2500);
                },

                async guardarEdicionAccion() {
                    if (!this.accionForm.empresa_accion_id || !this.accionForm.accion_id) {
                        return;
                    }

                    this.accionModalSaving = true;
                    this.accionModalError = '';

                    try {
                        const response = await fetch(this.buildAccionUrl(this.accionUpdateUrlTemplate, this.accionForm.empresa_accion_id), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify({ accion_id: this.accionForm.accion_id }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.accionModalError = data.message || 'No se pudo actualizar la acción.';
                            return;
                        }

                        const item = document.querySelector(`[data-actividad-item="${this.accionForm.empresa_accion_id}"]`);
                        if (item) {
                            item.dataset.accionId = String(data.empresa_accion?.accion_id || this.accionForm.accion_id);
                            const nameNode = item.querySelector('[data-role="actividad-nombre"]');
                            if (nameNode && data.accion?.nombre) {
                                nameNode.textContent = data.accion.nombre;
                            }
                            const iconNode = item.querySelector('[data-role="actividad-icon"]');
                            if (iconNode) {
                                iconNode.innerHTML = this.iconoSvg(data.accion?.icono || 'circle', data.accion?.color || '');
                            }
                            const editButton = item.querySelector('[data-accion-edit="true"]');
                            if (editButton) {
                                editButton.dataset.accionId = String(data.empresa_accion?.accion_id || this.accionForm.accion_id);
                            }
                        }

                        this.cerrarModalAccion();
                        this.mostrarAccionSuccess('Acción actualizada');
                    } catch (error) {
                        this.accionModalError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.accionModalSaving = false;
                    }
                },

                async eliminarAccion(empresaAccionId) {
                    if (!confirm('¿Eliminar este registro de actividad?')) {
                        return;
                    }

                    this.accionesError = '';

                    try {
                        const response = await fetch(this.buildAccionUrl(this.accionDeleteUrlTemplate, empresaAccionId), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.accionesError = data.message || 'No se pudo eliminar la acción.';
                            return;
                        }

                        const item = document.querySelector(`[data-actividad-item="${empresaAccionId}"]`);
                        if (item) {
                            item.remove();
                        }

                        const listContainer = document.getElementById('actividad-list');
                        const countNode = listContainer?.querySelector('[data-actividad-count]');
                        if (countNode) {
                            const currentCount = Number(countNode.dataset.actividadCount || 0);
                            const nextCount = Math.max(currentCount - 1, 0);
                            countNode.dataset.actividadCount = String(nextCount);
                            this.actividadCount = nextCount;

                            if (nextCount === 0) {
                                countNode.innerHTML = '<p class="text-sm text-slate-600">Sin actividad aún</p>';
                            }
                        }

                        this.mostrarAccionSuccess('Acción eliminada');
                    } catch (error) {
                        this.accionesError = 'No fue posible conectar con el servidor.';
                    }
                },

                iconoSvg(icono, color = '') {
                    const stroke = color || 'currentColor';
                    const attrs = `width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"`;
                    const icons = {
                        'phone': `<svg ${attrs}><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.78.68 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.25a2 2 0 0 1 2.11-.45c.84.33 1.72.56 2.62.68A2 2 0 0 1 22 16.92z"/></svg>`,
                        'share-2': `<svg ${attrs}><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>`,
                        'video': `<svg ${attrs}><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>`,
                        'map-pin': `<svg ${attrs}><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
                        'building-2': `<svg ${attrs}><path d="M6 22V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v18"/><path d="M6 12H4a1 1 0 0 0-1 1v9"/><path d="M18 9h2a1 1 0 0 1 1 1v12"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>`,
                        'user-x': `<svg ${attrs}><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>`,
                        'calendar': `<svg ${attrs}><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,
                        'circle': `<svg ${attrs}><circle cx="12" cy="12" r="10"/></svg>`,
                    };

                    return icons[icono] || icons.circle;
                },
                async registrarAccion(accionId) {
                    this.accionesGuardando = true;
                    this.accionesError = '';

                    try {
                        const response = await fetch(`{{ route('empresas.acciones.store', $empresa) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify({ accion_id: accionId }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.accionesError = data.message || 'No se pudo registrar la acción.';
                            return;
                        }

                        await this.aplicarFiltro('actividad', this.currentActRange);
                    } catch (error) {
                        this.accionesError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.accionesGuardando = false;
                    }
                },

                abrirModal(visita) {
                    this.visitaId = visita.id;
                    this.form.resultado = '';
                    this.form.nivel_interes = '';
                    this.formError = '';
                    this.errors = {};
                    this.showModal = true;
                },
                cerrarModal() {
                    this.showModal = false;
                    this.isSubmitting = false;
                },
                abrirModalUsuario() {
                    this.showUsuarioModal = true;
                    this.usuarioFormError = '';
                    this.usuarioSeleccionadoId = this.empresaUser ? this.empresaUser.id : null;
                    this.buscarUsuarios();
                },
                cerrarModalUsuario() {
                    this.showUsuarioModal = false;
                    this.usuarioQuery = '';
                    this.usuarioSeleccionadoId = null;
                    this.usuariosResultados = [];
                },
                async buscarUsuarios() {
                    this.usuarioLoading = true;
                    this.usuarioFormError = '';

                    try {
                        const response = await fetch(`{{ route('usuarios.buscar') }}?query=${encodeURIComponent(this.usuarioQuery || '')}`, {
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) {
                            this.usuarioFormError = 'No se pudo buscar usuarios.';
                            this.usuariosResultados = [];
                            return;
                        }

                        this.usuariosResultados = await response.json();
                    } catch (error) {
                        this.usuarioFormError = 'No fue posible conectar con el servidor.';
                        this.usuariosResultados = [];
                    } finally {
                        this.usuarioLoading = false;
                    }
                },
                async guardarUsuarioAsignado(userId) {
                    this.usuarioSaving = true;
                    this.usuarioFormError = '';

                    try {
                        const response = await fetch(`{{ route('empresas.asignar-usuario', $empresa) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify({ responsable_user_id: userId }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.usuarioFormError = data.message || 'Validación inválida al asignar usuario.';
                                return;
                            }

                            this.usuarioFormError = data.message || 'No se pudo asignar el usuario.';
                            return;
                        }

                        this.empresaUser = data.empresa?.responsable ?? null;
                        this.empresaReferidaAt = data.empresa?.referida_at ?? null;
                        this.usuarioSuccess = data.message || 'Usuario actualizado correctamente.';
                        setTimeout(() => {
                            this.usuarioSuccess = '';
                        }, 3000);
                        this.cerrarModalUsuario();
                    } catch (error) {
                        this.usuarioFormError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.usuarioSaving = false;
                    }
                },
                abrirModalContacto() {
                    this.contactoForm = {
                        nombre: '',
                        cargo: '',
                        telefono: '',
                        email: '',
                        es_principal: false,
                    };

                    this.contactoEditId = null;
                    this.isEditContacto = false;

                    this.contactoErrors = {};
                    this.contactoFormError = '';
                    this.showContactoModal = true;
                },

                abrirModalEditarContacto(contacto) {
                    this.contactoEditId = contacto.id;
                    this.isEditContacto = true;
                    this.contactoForm = {
                        nombre: contacto.nombre ?? '',
                        cargo: contacto.cargo ?? '',
                        telefono: contacto.telefono ?? '',
                        email: contacto.email ?? '',
                        es_principal: Boolean(Number(contacto.es_principal ?? 0)),
                    };
                    this.contactoErrors = {};
                    this.contactoFormError = '';
                    this.showContactoModal = true;
                },

                cerrarModalContacto() {
                    this.showContactoModal = false;
                    this.isSubmittingContacto = false;
                    this.contactoEditId = null;
                    this.isEditContacto = false;

                },
                async guardarContacto() {
                    this.isSubmittingContacto = true;
                    this.contactoErrors = {};
                    this.contactoFormError = '';

                    try {
                        const formData = new FormData();
                        formData.append('nombre', this.contactoForm.nombre ?? '');
                        formData.append('cargo', this.contactoForm.cargo ?? '');
                        formData.append('telefono', this.contactoForm.telefono ?? '');
                        formData.append('email', this.contactoForm.email ?? '');
                        formData.append('es_principal', this.contactoForm.es_principal ? '1' : '0');


                        if (this.isEditContacto && this.contactoEditId) {
                            formData.append('_method', 'PATCH');
                        }

                        const endpoint = this.isEditContacto && this.contactoEditId
                            ? `{{ route('empresas.contactos.update', ['empresa' => $empresa, 'contacto' => '__CONTACTO__']) }}`.replace('__CONTACTO__', this.contactoEditId)
                            : `{{ route('empresas.contactos.store', $empresa) }}`;

                        const response = await fetch(endpoint, {

                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: formData,
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.contactoErrors = Object.fromEntries(
                                    Object.entries(data.errors || {}).map(([key, value]) => [key, value[0]])
                                );

                                this.contactoFormError = data.message || (this.isEditContacto ? 'No se pudo actualizar el contacto.' : 'No se pudo agregar el contacto.');

                                return;
                            }

                            this.contactoFormError = data.message || 'Ocurrió un error inesperado.';
                            return;
                        }

                        const contenedor = document.getElementById('contactos-list');
                        if (contenedor && data.contactos_html) {
                            contenedor.innerHTML = data.contactos_html;
                        } else {
                            this.contactoFormError = 'No se pudo actualizar la lista de contactos en pantalla.';
                            return;
                        }

                        this.cerrarModalContacto();
                    } catch (error) {
                        this.contactoFormError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.isSubmittingContacto = false;
                    }
                },

                abrirPerfilComercialModal() {
                    this.activeTab = this.modalTabs[0] || 'Estado Actual';
                    this.opcionesMensaje = '';
                    this.draftSelectedIds = [...this.savedSelectedIds];
                    this.draftCotizacionEnviada = this.savedCotizacionEnviada;
                    this.draftComoLlego = this.savedComoLlego.map((item) => ({ ...item }));
                    this.draftCategoriaNotas = { ...this.savedCategoriaNotas };
                    this.form.cotizacion_numero = this.savedCotizacionNumero;
                    this.modalOpen = true;
                },
                cerrarPerfilComercialModal() {
                    this.draftSelectedIds = [...this.savedSelectedIds];
                    this.draftCotizacionEnviada = this.savedCotizacionEnviada;
                    this.draftComoLlego = this.savedComoLlego.map((item) => ({ ...item }));
                    this.draftCategoriaNotas = { ...this.savedCategoriaNotas };
                    this.form.cotizacion_numero = this.savedCotizacionNumero;
                    this.modalOpen = false;
                },
                notaCategoriaActual(categoria) {
                    return this.draftCategoriaNotas?.[categoria] || '';
                },
                setNotaCategoria(categoria, valor) {
                    this.draftCategoriaNotas = {
                        ...this.draftCategoriaNotas,
                        [categoria]: valor,
                    };
                },

                categoryAbbreviation(categoria) {
                    const aliases = {
                        'Estado Actual': 'Estado:',
                        'Aplicativos': 'App:',
                        'Procesos Electrónicos': 'Electrónico:',
                        'Equipos': 'Equipo:',
                    };

                    return aliases[categoria] || `${categoria}:`;
                },
                optionChipsFromIds(ids) {
                    const selectedSet = new Set((ids || []).map((id) => Number(id)));


                    return this.categoriasOpciones
                        .flatMap((categoria) => (this.opcionesPorCategoria[categoria] || []).map((opcion) => ({
                            id: Number(opcion.id),
                            nombre: opcion.nombre,
                            categoria,
                            categoriaAbreviada: this.categoryAbbreviation(categoria),
                            nota: (this.savedCategoriaNotas?.[categoria] || '').trim(),
                        })))
                        .filter((opcion) => selectedSet.has(opcion.id));
                },
                savedOptionChips() {
                    return this.optionChipsFromIds(this.savedSelectedIds);
                },
                draftOptionChips() {
                    return this.optionChipsFromIds(this.draftSelectedIds);
                },
                savedOptionNames() {
                    return this.optionNamesFromIds(this.savedSelectedIds);
                },
                draftOptionNames() {
                    return this.optionNamesFromIds(this.draftSelectedIds);
                },
                isOpcionSeleccionada(id) {
                    return this.draftSelectedIds.includes(Number(id));

                },
                toggleOpcion(id) {
                    const normalizedId = Number(id);
                    if (this.isOpcionSeleccionada(normalizedId)) {

                        this.draftSelectedIds = this.draftSelectedIds.filter((item) => Number(item) !== normalizedId);
                        return;
                    }

                    this.draftSelectedIds.push(normalizedId);

                },
                isComoLlegoSeleccionada(opcionId) {
                    const normalizedId = Number(opcionId);
                    return this.draftComoLlego.some((item) => Number(item.opcion_id) === normalizedId);
                },
                toggleComoLlego(opcionId, checked) {
                    const normalizedId = Number(opcionId);
                    const existe = this.isComoLlegoSeleccionada(normalizedId);

                    if (!checked) {
                        this.draftComoLlego = this.draftComoLlego
                            .filter((item) => Number(item.opcion_id) !== normalizedId)
                            .map((item) => ({ ...item }));
                        return;
                    }

                    if (existe) {
                        return;
                    }

                    this.draftComoLlego = [
                        ...this.draftComoLlego,
                        { opcion_id: normalizedId, texto: null },
                    ];
                },
                comoLlegoTexto(opcionId) {
                    const item = this.draftComoLlego.find((entry) => Number(entry.opcion_id) === Number(opcionId));
                    return item?.texto || '';
                },
                setComoLlegoTexto(opcionId, texto) {
                    const normalizedId = Number(opcionId);
                    this.draftComoLlego = this.draftComoLlego.map((item) => {
                        if (Number(item.opcion_id) !== normalizedId) {
                            return item;
                        }

                        return {
                            ...item,
                            texto,
                        };
                    });
                },
                placeholderComoLlego(nombre) {
                    const placeholders = {
                        'Referido': '¿Quién lo refirió?',
                        'Redes Sociales': '¿Cuál red?',
                        'Internet': '¿Dónde?',
                    };

                    return placeholders[nombre] || 'Escribe un detalle';
                },
                abrirModalOpcion(categoria) {
                    this.newOptionCategoria = categoria;
                    this.newOptionNombre = '';
                    this.newOptionError = '';
                    this.showCatalogoModal = true;
                },
                cerrarModalOpcion() {
                    this.showCatalogoModal = false;
                    this.newOptionSaving = false;
                    this.newOptionError = '';
                },
                async guardarNuevaOpcion() {
                    if (!this.newOptionCategoria) {
                        return;
                    }

                    this.newOptionSaving = true;
                    this.newOptionError = '';

                    try {
                        const response = await fetch(`{{ route('catalogo-opciones.store') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify({
                                categoria: this.newOptionCategoria,
                                nombre: this.newOptionNombre,
                            }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.newOptionError = data.message || 'No se pudo agregar la opción.';
                            return;
                        }

                        if (!Array.isArray(this.opcionesPorCategoria[data.categoria])) {
                            this.opcionesPorCategoria[data.categoria] = [];
                        }

                        this.opcionesPorCategoria[data.categoria].push({ id: Number(data.id), nombre: data.nombre });
                        this.opcionesPorCategoria[data.categoria] = [...this.opcionesPorCategoria[data.categoria]];

                        if (!this.isOpcionSeleccionada(Number(data.id))) {
                            this.draftSelectedIds.push(Number(data.id));

                        }

                        this.opcionesMensaje = 'Opción agregada correctamente.';
                        this.cerrarModalOpcion();
                    } catch (error) {
                        this.newOptionError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.newOptionSaving = false;
                    }
                },
                referidoLabel(estado) {
                    const labels = {
                        pendiente: 'Pendiente',
                        aprobado: 'Aprobado',
                        rechazado: 'Rechazado',
                    };

                    return labels[estado] || 'Pendiente';
                },
                referidoBadgeClass(estado) {
                    const clases = {
                        pendiente: 'bg-amber-100 text-amber-800',
                        aprobado: 'bg-emerald-100 text-emerald-800',
                        rechazado: 'bg-rose-100 text-rose-800',
                    };

                    return clases[estado] || clases.pendiente;
                },
                formatDateTime(valor) {
                    if (!valor) {
                        return '';
                    }

                    const fecha = new Date(valor);
                    if (Number.isNaN(fecha.getTime())) {
                        return '';
                    }

                    return new Intl.DateTimeFormat('es-CO', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    }).format(fecha).replace(',', '');
                },
                async guardarReferidoEstado() {
                    this.referidoSaving = true;
                    this.referidoError = '';
                    this.referidoSuccess = '';
                    this.referidoErrors = {};

                    const payload = {
                        referido_estado: this.referidoForm.referido_estado,
                        referido_motivo_rechazo: this.referidoForm.referido_estado === 'rechazado'
                            ? (this.referidoForm.referido_motivo_rechazo || '').trim()
                            : null,
                        comision_estado: this.referidoForm.comision_estado || 'pendiente',
                    };

                    try {
                        const response = await fetch(this.referidoUpdateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.referidoErrors = Object.fromEntries(
                                    Object.entries(data.errors || {}).map(([key, value]) => [key, value[0]])
                                );
                            }

                            this.referidoError = data.message || 'No se pudo actualizar el estado del referido.';
                            return;
                        }

                        this.referidoForm = {
                            ...this.referidoForm,
                            ...(data.data || {}),
                            comision_valor: data.data?.comision_valor ?? null,
                        };
                        this.referidoSuccess = 'Estado actualizado correctamente.';
                    } catch (error) {
                        this.referidoError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.referidoSaving = false;
                    }
                },
                cotizacionEnviadaAtFormateada(valor = null) {
                    if (!valor) {
                        return '';
                    }

                    const fecha = new Date(valor);
                    if (Number.isNaN(fecha.getTime())) {
                        return '';
                    }

                    const formato = new Intl.DateTimeFormat('es-CO', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                    });

                    return formato.format(fecha).replace(',', '');
                },
                async guardarOpcionesEmpresa() {
                    this.opcionesSaving = true;
                    this.opcionesMensaje = '';

                    try {
                        const response = await fetch(`{{ route('empresas.opciones.update', $empresa) }}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },

                            body: JSON.stringify({
                                opciones: this.draftSelectedIds,
                                cotizacion_enviada: this.draftCotizacionEnviada ? 1 : 0,
                                cotizacion_numero: this.form.cotizacion_numero || null,
                                categoria_notas: this.draftCategoriaNotas,
                                como_llego: this.draftComoLlego
                                    .filter((item) => Number(item.opcion_id) > 0)
                                    .map((item) => ({
                                        opcion_id: Number(item.opcion_id),
                                        texto: item.texto || null,
                                    })),
                            }),

                        });

                        const data = await response.json();

                        if (!response.ok) {
                            this.opcionesMensaje = data.message || 'No se pudieron guardar las opciones.';
                            return;
                        }

                        this.savedSelectedIds = (data.opciones || []).map((id) => Number(id));
                        this.draftSelectedIds = [...this.savedSelectedIds];
                        this.savedComoLlego = Array.isArray(data.como_llego) ? data.como_llego : [];
                        this.draftComoLlego = this.savedComoLlego.map((item) => ({ ...item }));
                        this.savedCategoriaNotas = {
                            ...this.savedCategoriaNotas,
                            ...(data.categoria_notas || {}),
                        };
                        this.draftCategoriaNotas = { ...this.savedCategoriaNotas };
                        this.savedCotizacionEnviada = Boolean(data.empresa?.cotizacion_enviada);
                        this.savedCotizacionEnviadaAt = data.empresa?.cotizacion_enviada_at || null;
                        this.savedCotizacionNumero = data.empresa?.cotizacion_numero || null;
                        this.draftCotizacionEnviada = this.savedCotizacionEnviada;
                        this.form.cotizacion_numero = this.savedCotizacionNumero;

                        this.opcionesMensaje = data.message || 'Cambios guardados correctamente.';
                        this.modalOpen = false;
                    } catch (error) {
                        this.opcionesMensaje = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.opcionesSaving = false;
                    }
                },
                async guardarResultado() {
                    if (!this.visitaId) {
                        return;
                    }

                    this.isSubmitting = true;
                    this.formError = '';
                    this.errors = {};

                    try {
                        const response = await fetch(`/visitas/${this.visitaId}/resultado`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                Accept: 'application/json',
                            },
                            body: JSON.stringify(this.form),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.errors = Object.fromEntries(
                                    Object.entries(data.errors || {}).map(([key, value]) => [key, value[0]])
                                );
                                this.formError = data.message || 'No se pudo actualizar el resultado.';
                                return;
                            }

                            this.formError = data.message || 'Ocurrió un error inesperado.';
                            return;
                        }

                        this.actualizarItemVisita(data.visita);
                        this.cerrarModal();
                    } catch (error) {
                        this.formError = 'No fue posible conectar con el servidor.';
                    } finally {
                        this.isSubmitting = false;
                    }
                },
                actualizarItemVisita(visita) {
                    const btn = document.getElementById(`btn-actualizar-${visita.id}`);
                    if (btn) {
                        btn.style.display = 'none';
                    }

                    const container = document.getElementById(`resultado-container-${visita.id}`);
                    if (!container) {
                        return;
                    }

                    const nivelTexto = visita.nivel_interes_label ? `Nivel de interés: ${visita.nivel_interes_label}` : '';
                    container.innerHTML = `
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <span class="font-medium text-slate-700">Resultado:</span>
                            <span id="resultado-badge-${visita.id}" class="rounded-full px-2.5 py-0.5 text-xs font-semibold ${visita.resultado_badge_class}">${visita.resultado_label}</span>
                            <span id="nivel-interes-text-${visita.id}" class="text-slate-600">${nivelTexto}</span>
                        </div>
                    `;
                },
            };
        }
    </script>
@endsection
