<template x-if="openVisitModal">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeVisitModal()">
        <div class="absolute inset-0 bg-slate-900/45"></div>
        <div class="relative z-10 w-full max-w-2xl rounded-xl bg-white p-5 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-950">Nueva visita</h3>
                <button type="button" @click="closeVisitModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">✕</button>
            </div>

            <form class="space-y-4" @submit.prevent="submitVisitForm">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Empresa</label>
                    <div class="relative flex items-center gap-2">
                        <input
                            type="text"
                            x-model="empresaQuery"
                            :disabled="selectedEmpresaLocked"
                            class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 disabled:bg-slate-100 disabled:text-slate-500"
                            placeholder="Buscar por nombre o ciudad"
                        >
                        <button
                            type="button"
                            @click="searchEmpresa()"
                            :disabled="selectedEmpresaLocked"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" /></svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500" x-show="selectedEmpresa">Seleccionada: <span x-text="selectedEmpresa"></span></p>
                    <p class="mt-1 text-xs text-rose-600" x-text="visitErrors.empresa_id || ''"></p>

                    <div x-show="empresaLoading" class="mt-2 text-xs text-slate-500">Buscando...</div>
                    <div x-show="!selectedEmpresaLocked && empresaResults.length > 0" class="mt-2 max-h-48 overflow-auto rounded-lg border border-slate-200 bg-white shadow-sm">
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
                        <input type="datetime-local" x-model="visitForm.fecha_hora" required class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <p class="mt-1 text-xs text-rose-600" x-text="visitErrors.fecha_hora || ''"></p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Estado</label>
                        <select x-model="visitForm.estado" required class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="programada">Programada</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                        <p class="mt-1 text-xs text-rose-600" x-text="visitErrors.estado || ''"></p>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Resultado</label>
                    <input type="text" x-model="visitForm.resultado" class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <p class="mt-1 text-xs text-rose-600" x-text="visitErrors.resultado || ''"></p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Notas</label>
                    <textarea x-model="visitForm.notas" rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
                    <p class="mt-1 text-xs text-rose-600" x-text="visitErrors.notas || ''"></p>
                </div>

                <p class="text-sm text-rose-600" x-show="visitFormError" x-text="visitFormError"></p>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeVisitModal()" class="inline-flex h-10 items-center rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</button>
                    <button type="submit" :disabled="isVisitSubmitting" class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-60">
                        <span x-show="!isVisitSubmitting">Guardar visita</span>
                        <span x-show="isVisitSubmitting">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<div
    x-cloak
    x-show="visitToast"
    x-transition.opacity
    class="fixed bottom-4 right-4 z-[70] rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 shadow"
    x-text="visitToast"
></div>

<script>
    window.createNuevaVisitaModalState = function (config = {}) {
        return {
            openVisitModal: false,
            empresaQuery: '',
            empresaId: '',
            selectedEmpresa: '',
            selectedEmpresaLocked: false,
            empresaResults: [],
            empresaLoading: false,
            isVisitSubmitting: false,
            visitFormError: '',
            visitErrors: {},
            visitToast: '',
            visitForm: {
                fecha_hora: '',
                estado: 'programada',
                resultado: '',
                notas: '',
            },
            initNuevaVisitaModal() {
                window.addEventListener('open-nueva-visita-modal', (event) => {
                    this.openNuevaVisitaModal(event.detail || {});
                });
            },
            openNuevaVisitaModal({ empresa_id = null, empresa_label = '', lock_empresa = false } = {}) {
                this.openVisitModal = true;
                this.visitFormError = '';
                this.visitErrors = {};
                this.empresaResults = [];

                if (empresa_id) {
                    this.empresaId = String(empresa_id);
                    this.selectedEmpresa = empresa_label;
                    this.empresaQuery = empresa_label;
                    this.selectedEmpresaLocked = lock_empresa;
                    return;
                }

                this.empresaId = '';
                this.selectedEmpresa = '';
                this.empresaQuery = '';
                this.selectedEmpresaLocked = false;
            },
            closeVisitModal() {
                this.openVisitModal = false;
                this.empresaResults = [];
                this.visitErrors = {};
                this.visitFormError = '';
                this.isVisitSubmitting = false;
            },
            async searchEmpresa() {
                const query = (this.empresaQuery ?? '').trim();

                if (this.selectedEmpresaLocked) {
                    return;
                }

                if (query.length === 0) {
                    this.empresaResults = [];
                    this.empresaId = '';
                    this.selectedEmpresa = '';
                    return;
                }

                this.empresaLoading = true;

                try {
                    const response = await fetch(`/api/empresas?query=${encodeURIComponent(query)}`, {
                        headers: { Accept: 'application/json' },
                    });

                    if (!response.ok) {
                        this.empresaResults = [];
                        return;
                    }

                    this.empresaResults = await response.json();
                } catch (error) {
                    this.empresaResults = [];
                } finally {
                    this.empresaLoading = false;
                }
            },
            selectEmpresa(empresa) {
                this.empresaId = String(empresa.id);
                this.selectedEmpresa = empresa.nombre;
                this.empresaQuery = empresa.nombre;
                this.empresaResults = [];
            },
            resetVisitForm() {
                this.visitForm = {
                    fecha_hora: '',
                    estado: 'programada',
                    resultado: '',
                    notas: '',
                };

                if (!this.selectedEmpresaLocked) {
                    this.empresaId = '';
                    this.selectedEmpresa = '';
                    this.empresaQuery = '';
                }
            },
            async submitVisitForm() {
                this.isVisitSubmitting = true;
                this.visitErrors = {};
                this.visitFormError = '';

                try {
                    const response = await fetch(@js(route('visitas.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({
                            empresa_id: this.empresaId,
                            fecha_hora: this.visitForm.fecha_hora,
                            estado: this.visitForm.estado,
                            resultado: this.visitForm.resultado || null,
                            notas: this.visitForm.notas || null,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) {
                            this.visitErrors = Object.fromEntries(
                                Object.entries(data.errors || {}).map(([key, value]) => [key, value[0]])
                            );
                        }

                        this.visitFormError = data.message || 'No se pudo guardar la visita.';
                        return;
                    }

                    this.closeVisitModal();
                    this.resetVisitForm();
                    this.visitToast = data.message || 'Visita guardada correctamente.';
                    setTimeout(() => {
                        this.visitToast = '';
                    }, 2600);

                    if (typeof config.onSuccess === 'function') {
                        await config.onSuccess(data);
                    }
                } catch (error) {
                    this.visitFormError = 'No fue posible conectar con el servidor.';
                } finally {
                    this.isVisitSubmitting = false;
                }
            },
        };
    };

    window.openNuevaVisitaModal = function (payload = {}) {
        window.dispatchEvent(new CustomEvent('open-nueva-visita-modal', { detail: payload }));
    };
</script>
