<section class="space-y-4" x-data="sectoresManager({
    initialSectores: @js($sectores),
    indexUrl: @js(route('sectores.index')),
    storeUrl: @js(route('sectores.store')),
    updateUrlTemplate: @js(route('sectores.update', ['sector' => '__ID__'])),
    destroyUrlTemplate: @js(route('sectores.destroy', ['sector' => '__ID__'])),
})">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-slate-900">Sectores</h2>
        <button type="button" @click="openCreateModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
            + Nuevo sector
        </button>
    </div>

    <template x-if="flashMessage">
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700" x-text="flashMessage"></div>
    </template>

    <template x-if="errorMessage">
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="errorMessage"></div>
    </template>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="sector in sectores" :key="sector.id">
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800" x-text="sector.nombre"></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold"
                                :class="sector.activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                x-text="sector.activo ? 'Activo' : 'Inactivo'">
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600" x-text="sector.orden ?? '—'"></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openEditModal(sector)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Editar
                                </button>
                                <button type="button" @click="destroySector(sector)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="sectores.length === 0">
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No hay sectores registrados.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4" @click.self="closeModal()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar sector' : 'Nuevo sector'"></h3>

            <form class="mt-4 space-y-4" @submit.prevent="saveSector()">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                    <input type="text" x-model="form.nombre" maxlength="255" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                    <input type="number" x-model="form.orden" min="1" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>

                <label x-show="editingId" class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" x-model="form.activo" class="rounded border-slate-300 text-blue-600">
                    Activo
                </label>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700" :disabled="loading">
                        <span x-text="loading ? 'Guardando...' : 'Guardar'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function sectoresManager({ initialSectores, indexUrl, storeUrl, updateUrlTemplate, destroyUrlTemplate }) {
        return {
            sectores: initialSectores ?? [],
            showModal: false,
            editingId: null,
            loading: false,
            flashMessage: '',
            errorMessage: '',
            form: {
                nombre: '',
                orden: '',
                activo: true,
            },
            openCreateModal() {
                this.editingId = null
                this.form = { nombre: '', orden: '', activo: true }
                this.showModal = true
            },
            openEditModal(sector) {
                this.editingId = sector.id
                this.form = {
                    nombre: sector.nombre ?? '',
                    orden: sector.orden ?? '',
                    activo: Boolean(sector.activo),
                }
                this.showModal = true
            },
            closeModal() {
                this.showModal = false
            },
            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            normalizedPayload() {
                return {
                    nombre: (this.form.nombre || '').trim(),
                    orden: this.form.orden === '' ? null : Number(this.form.orden),
                    activo: this.form.activo ? 1 : 0,
                }
            },
            async refreshSectores() {
                const response = await fetch(indexUrl, { headers: { Accept: 'application/json' } })
                const result = await response.json()
                this.sectores = result.data || []
            },
            async saveSector() {
                this.loading = true
                this.errorMessage = ''

                const editing = Boolean(this.editingId)
                const url = editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl
                const payload = this.normalizedPayload()

                const response = await fetch(url, {
                    method: editing ? 'PATCH' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                })

                const result = await response.json()

                if (!response.ok) {
                    this.errorMessage = result?.message || 'No fue posible guardar el sector.'
                    this.loading = false
                    return
                }

                this.flashMessage = result.message || 'Sector guardado correctamente.'
                this.closeModal()
                await this.refreshSectores()
                this.loading = false
            },
            async destroySector(sector) {
                if (!confirm(`¿Eliminar el sector "${sector.nombre}"?`)) {
                    return
                }

                this.errorMessage = ''

                const response = await fetch(destroyUrlTemplate.replace('__ID__', sector.id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Accept': 'application/json',
                    },
                })

                const result = await response.json()

                if (!response.ok) {
                    this.errorMessage = result?.message || 'No fue posible eliminar el sector.'
                    return
                }

                this.flashMessage = result.message || 'Sector eliminado correctamente.'
                await this.refreshSectores()
            },
        }
    }
</script>
