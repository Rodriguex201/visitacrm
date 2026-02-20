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

            <button type="button" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <span class="text-base leading-none">+</span>
                Nueva Visita
            </button>
        </header>

        <article class="rounded-xl border border-slate-100 bg-white px-4 py-4 shadow-sm">

            <div class="space-y-3 text-slate-600">
                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                        <circle cx="12" cy="9.75" r="2.25" />
                    </svg>
                    {{ $empresa->direccion ?: 'Sin dirección' }}
                </p>

                <p class="inline-flex items-center gap-2 text-sm">
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

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h3.214a2.25 2.25 0 012.121 1.497l1.02 3.059a2.25 2.25 0 01-.518 2.314l-1.44 1.44a12.042 12.042 0 005.853 5.853l1.44-1.44a2.25 2.25 0 012.314-.518l3.06 1.02a2.25 2.25 0 011.496 2.121V19.5a2.25 2.25 0 01-2.25 2.25h-.75C9.007 21.75 2.25 14.993 2.25 6.75z" />
                    </svg>
                    {{ $empresa->telefono ?: 'Sin teléfono' }}
                </p>

                {{--
                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25h15m-13.5 0V6.75A2.25 2.25 0 018.25 4.5h7.5A2.25 2.25 0 0118 6.75v13.5m-9-11.25h6m-6 3h6m-6 3h4.5" />
                    </svg>
                    NIT: {{ $empresa->nit ?: 'Sin NIT' }}
                </p>
                --}}

                <p class="inline-flex items-center gap-2 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.03 6.75-11.25a6.75 6.75 0 10-13.5 0C5.25 14.97 12 21 12 21z" />
                        <circle cx="12" cy="9.75" r="2.25" />
                    </svg>
                    {{ $empresa->ciudad }}
                </p>
            </div>

        </article>

        <article class="space-y-3 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-950">Notas</h2>
            @if ($empresa->notas)
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $empresa->notas }}</p>
            @else
                <p class="text-sm text-slate-500">Sin notas</p>
            @endif
        </article>

        <article class="space-y-5 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Actividad (0)</h2>

                <div class="flex items-center gap-2 text-sm font-semibold">
                    <span class="rounded-xl bg-blue-600 px-4 py-2 text-white">Hoy</span>
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-800">7 días</span>
                    <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-800">Todo</span>
                </div>
            </div>

            <p class="text-sm text-slate-600">Sin actividad aún</p>
        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-xl font-semibold text-slate-950">Contactos</h2>

                <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900">
                    <span class="text-base leading-none">+</span>
                    Agregar
                </button>
            </div>

            <p class="text-center text-sm text-slate-500">Sin contactos registrados</p>
        </article>

        <article class="space-y-6 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
            @php
                $resultadoLabels = [
                    'venta_realizada' => 'Venta realizada',
                    'en_seguimiento' => 'En seguimiento',
                    'sin_interes' => 'Sin interés',
                    'no_disponible' => 'No disponible',
                ];

                $nivelInteresLabels = [
                    'alto' => 'Alto',
                    'medio' => 'Medio',
                    'bajo' => 'Bajo',
                    'sin_interes' => 'Sin interés',
                ];
            @endphp

            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-slate-950">Historial de Visitas ({{ $visitas->count() }})</h2>

                <div class="flex items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('empresas.show', ['empresa' => $empresa, 'range' => 'hoy']) }}" class="rounded-xl px-4 py-2 {{ $range === 'hoy' ? 'bg-blue-600 text-white' : 'border border-slate-200 bg-slate-50 text-slate-800' }}">Hoy</a>
                    <a href="{{ route('empresas.show', ['empresa' => $empresa, 'range' => '7d']) }}" class="rounded-xl px-4 py-2 {{ $range === '7d' ? 'bg-blue-600 text-white' : 'border border-slate-200 bg-slate-50 text-slate-800' }}">7 días</a>
                    <a href="{{ route('empresas.show', ['empresa' => $empresa, 'range' => 'todo']) }}" class="rounded-xl px-4 py-2 {{ $range === 'todo' ? 'bg-blue-600 text-white' : 'border border-slate-200 bg-slate-50 text-slate-800' }}">Todo</a>
                </div>
            </div>

            @if ($visitas->isEmpty())
                <p class="text-center text-sm text-slate-500">Sin visitas registradas</p>
            @else
                <div class="space-y-3">
                    @foreach ($visitas as $visita)
                        @php
                            $isProgramada = $visita->fecha_hora?->isFuture();
                            $canUpdateResultado = ! $isProgramada && empty($visita->resultado);
                            $estadoBadgeClass = match ($visita->estado) {
                                'realizada' => 'bg-emerald-100 text-emerald-700',
                                'cancelada' => 'bg-rose-100 text-rose-700',
                                default => 'bg-blue-100 text-blue-700',
                            };
                            $resultadoBadgeClass = match ($visita->resultado) {
                                'venta_realizada' => 'bg-emerald-100 text-emerald-700',
                                'en_seguimiento' => 'bg-amber-100 text-amber-700',
                                'sin_interes' => 'bg-rose-100 text-rose-700',
                                'no_disponible' => 'bg-slate-200 text-slate-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <article id="visita-item-{{ $visita->id }}" class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm" data-visita-id="{{ $visita->id }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $visita->fecha_hora?->format('d/m/Y H:i') }}</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoBadgeClass }}">{{ ucfirst($visita->estado) }}</span>
                                    @if ($isProgramada)
                                        <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">Programada</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 space-y-2">
                                <div id="resultado-container-{{ $visita->id }}">
                                    @if ($visita->resultado)
                                        <div class="flex flex-wrap items-center gap-2 text-sm">
                                            <span class="font-medium text-slate-700">Resultado:</span>
                                            <span id="resultado-badge-{{ $visita->id }}" class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $resultadoBadgeClass }}">{{ $resultadoLabels[$visita->resultado] ?? $visita->resultado }}</span>
                                            <span id="nivel-interes-text-{{ $visita->id }}" class="text-slate-600">
                                                @if ($visita->nivel_interes)
                                                    Nivel de interés: {{ $nivelInteresLabels[$visita->nivel_interes] ?? $visita->nivel_interes }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <button
                                    id="btn-actualizar-{{ $visita->id }}"
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    @click="abrirModal({ id: {{ $visita->id }} })"
                                    @if (! $canUpdateResultado) style="display: none;" @endif
                                >
                                    Actualizar resultado
                                </button>
                            </div>

                            @if ($visita->notas)
                                <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($visita->notas, 180) }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

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
    </section>


    <script>
        function historialVisitas() {
            return {
                showModal: false,
                isSubmitting: false,
                visitaId: null,
                formError: '',
                errors: {},
                form: {
                    resultado: '',
                    nivel_interes: '',
                },
                init() {
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
