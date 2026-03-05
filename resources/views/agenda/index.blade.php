@extends('layouts.app')

@section('content')
    <section class="space-y-5" x-data>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold leading-tight text-slate-950">Agenda</h1>
                <p class="mt-1 text-base text-slate-600">Calendario de visitas (Mes/Semana/Día)</p>
            </div>
            <button id="btn-nueva-visita" type="button" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <span class="text-lg leading-none">+</span>
                Nueva visita
            </button>
        </div>

        <div class="grid grid-cols-1 gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-100 md:grid-cols-3">
            <div>
                <label for="filtro-empresa" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Empresa</label>
                <input id="filtro-empresa" type="text" placeholder="Buscar empresa..." class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="filtro-estado" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</label>
                <select id="filtro-estado" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="programada">Programada</option>
                    <option value="en_seguimiento">En seguimiento</option>
                    <option value="realizada">Realizada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            @if ($isAdmin)
                <div>
                    <label for="filtro-responsable" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Responsable</label>
                    <select id="filtro-responsable" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($responsables as $responsable)
                            <option value="{{ $responsable->id }}">{{ $responsable->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="rounded-xl bg-white p-3 shadow-sm ring-1 ring-slate-100 md:p-5">
            <div id="agenda-calendar"></div>
        </div>
    </section>

    <div id="agenda-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
        <div class="w-full max-w-xl rounded-xl bg-white p-5 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 id="modal-title" class="text-lg font-semibold text-slate-900">Nueva visita</h2>
                <button type="button" id="modal-close" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800">✕</button>
            </div>

            <form id="agenda-form" class="space-y-3">
                <input id="visita-id" type="hidden">
                <div>
                    <label for="empresa_id" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Empresa</label>
                    <select id="empresa_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Selecciona...</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label for="fecha_hora" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha y hora</label>
                        <input id="fecha_hora" type="datetime-local" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="estado" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</label>
                        <select id="estado" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="programada">Programada</option>
                            <option value="en_seguimiento">En seguimiento</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="duracion_min" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Duración (minutos)</label>
                    <input id="duracion_min" type="number" min="1" max="1440" value="60" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="notas" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notas</label>
                    <textarea id="notas" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex items-center justify-between gap-3 pt-2">
                    <button type="button" id="btn-eliminar" class="hidden rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Eliminar</button>
                    <div class="ml-auto flex items-center gap-2">
                        <button type="button" id="btn-cancelar" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const modal = document.getElementById('agenda-modal');
            const form = document.getElementById('agenda-form');
            const modalTitle = document.getElementById('modal-title');
            const btnEliminar = document.getElementById('btn-eliminar');
            const visitaIdInput = document.getElementById('visita-id');
            const empresaInput = document.getElementById('empresa_id');
            const fechaHoraInput = document.getElementById('fecha_hora');
            const estadoInput = document.getElementById('estado');
            const notasInput = document.getElementById('notas');
            const duracionInput = document.getElementById('duracion_min');
            const filtroEmpresa = document.getElementById('filtro-empresa');
            const filtroEstado = document.getElementById('filtro-estado');
            const filtroResponsable = document.getElementById('filtro-responsable');

            const estadoColor = {
                programada: { backgroundColor: '#DBEAFE', borderColor: '#93C5FD', textColor: '#1E3A8A' },
                en_seguimiento: { backgroundColor: '#FEF3C7', borderColor: '#FCD34D', textColor: '#78350F' },
                realizada: { backgroundColor: '#DCFCE7', borderColor: '#86EFAC', textColor: '#14532D' },
                cancelada: { backgroundColor: '#FEE2E2', borderColor: '#FCA5A5', textColor: '#7F1D1D' },
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const toLocalDatetimeValue = (date) => {
                const d = new Date(date);
                const offset = d.getTimezoneOffset();
                const local = new Date(d.getTime() - offset * 60000);
                return local.toISOString().slice(0, 16);
            };

            const resetForm = () => {
                form.reset();
                visitaIdInput.value = '';
                estadoInput.value = 'programada';
                duracionInput.value = '60';
                btnEliminar.classList.add('hidden');
                modalTitle.textContent = 'Nueva visita';
            };

            const calendar = new FullCalendar.Calendar(document.getElementById('agenda-calendar'), {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                selectable: true,
                editable: true,
                eventResizableFromStart: true,
                events(fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                    });

                    if (filtroEmpresa.value.trim()) params.set('empresa', filtroEmpresa.value.trim());
                    if (filtroEstado.value) params.set('estado', filtroEstado.value);
                    if (filtroResponsable && filtroResponsable.value) params.set('responsable_id', filtroResponsable.value);

                    fetch(`{{ route('agenda.events') }}?${params.toString()}`)
                        .then((r) => r.json())
                        .then(successCallback)
                        .catch(failureCallback);
                },
                eventDidMount(info) {
                    const estado = info.event.extendedProps.estado;
                    const color = estadoColor[estado] || estadoColor.programada;
                    info.el.style.backgroundColor = color.backgroundColor;
                    info.el.style.borderColor = color.borderColor;
                    info.el.style.color = color.textColor;
                },
                dateClick(info) {
                    resetForm();
                    fechaHoraInput.value = toLocalDatetimeValue(info.date);
                    openModal();
                },
                eventClick(info) {
                    const event = info.event;
                    visitaIdInput.value = event.id;
                    empresaInput.value = event.extendedProps.empresa_id || '';
                    fechaHoraInput.value = toLocalDatetimeValue(event.start);
                    estadoInput.value = event.extendedProps.estado || 'programada';
                    notasInput.value = event.extendedProps.notas || '';
                    duracionInput.value = event.extendedProps.duracion_min || 60;
                    modalTitle.textContent = 'Editar visita';
                    btnEliminar.classList.remove('hidden');
                    openModal();
                },
                eventDrop(info) {
                    moveEvent(info.event, info.revert);
                },
                eventResize(info) {
                    moveEvent(info.event, info.revert);
                },
            });

            const moveEvent = (event, revert) => {
                const start = event.start;
                const end = event.end || new Date(start.getTime() + 60 * 60000);
                const duracionMin = Math.max(1, Math.round((end.getTime() - start.getTime()) / 60000));

                fetch(`{{ url('/agenda') }}/${event.id}/move`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        fecha_hora: start.toISOString(),
                        duracion_min: duracionMin,
                    }),
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('No se pudo mover la visita.');
                        }
                        calendar.refetchEvents();
                    })
                    .catch(() => {
                        revert();
                        alert('No se pudo reprogramar la visita.');
                    });
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                const visitaId = visitaIdInput.value;
                const method = visitaId ? 'PUT' : 'POST';
                const url = visitaId ? `{{ url('/agenda') }}/${visitaId}` : `{{ route('agenda.store') }}`;

                fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        empresa_id: empresaInput.value,
                        fecha_hora: new Date(fechaHoraInput.value).toISOString(),
                        estado: estadoInput.value,
                        notas: notasInput.value || null,
                        duracion_min: duracionInput.value || 60,
                    }),
                })
                    .then(async (response) => {
                        if (!response.ok) {
                            const error = await response.json().catch(() => ({}));
                            throw new Error(error.message || 'No se pudo guardar la visita.');
                        }

                        closeModal();
                        calendar.refetchEvents();
                    })
                    .catch((error) => {
                        alert(error.message);
                    });
            });

            btnEliminar.addEventListener('click', () => {
                const visitaId = visitaIdInput.value;

                if (!visitaId || !confirm('¿Eliminar esta visita?')) {
                    return;
                }

                fetch(`{{ url('/agenda') }}/${visitaId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('No se pudo eliminar la visita.');
                        }

                        closeModal();
                        calendar.refetchEvents();
                    })
                    .catch((error) => {
                        alert(error.message);
                    });
            });

            ['input', 'change'].forEach((eventName) => {
                filtroEmpresa.addEventListener(eventName, () => calendar.refetchEvents());
                filtroEstado.addEventListener(eventName, () => calendar.refetchEvents());
                if (filtroResponsable) {
                    filtroResponsable.addEventListener(eventName, () => calendar.refetchEvents());
                }
            });

            document.getElementById('btn-nueva-visita').addEventListener('click', () => {
                resetForm();
                fechaHoraInput.value = toLocalDatetimeValue(new Date());
                openModal();
            });

            document.getElementById('modal-close').addEventListener('click', closeModal);
            document.getElementById('btn-cancelar').addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });

            calendar.render();
        });
    </script>
@endsection
