<section class="space-y-4" x-data="ofrecerManager({
    initialItems: @js($ofrecerItems),
    indexUrl: @js(route('configuracion.ofrecer.index')),
    storeUrl: @js(route('configuracion.ofrecer.store')),
    updateUrlTemplate: @js(route('configuracion.ofrecer.update', ['herramientaOfrecer' => '__ID__'])),
    activateUrlTemplate: @js(route('configuracion.ofrecer.activate', ['herramientaOfrecer' => '__ID__'])),
    deactivateUrlTemplate: @js(route('configuracion.ofrecer.deactivate', ['herramientaOfrecer' => '__ID__'])),
    destroyUrlTemplate: @js(route('configuracion.ofrecer.destroy', ['herramientaOfrecer' => '__ID__'])),
})">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-slate-900">Ofrecer</h2>
        <button type="button" @click="openCreateModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Agregar</button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Imagen</th>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="item in items" :key="item.id">
                    <tr>
                        <td class="px-4 py-3">
                            <template x-if="item.imagen">
                                <img :src="item.imagen_url" alt="Imagen ofrecer" class="h-16 w-24 rounded-lg bg-white object-contain ring-1 ring-slate-200">
                            </template>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800" x-text="item.titulo || 'Sin título'"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="item.descripcion || 'Sin descripción'"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="item.orden"></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="item.activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'" x-text="item.activo ? 'Activo' : 'Inactivo'"></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openEditModal(item)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Editar</button>
                                <button type="button" @click="toggleEstado(item)" class="rounded-lg border px-3 py-1.5 text-xs font-semibold" :class="item.activo ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'" x-text="item.activo ? 'Desactivar' : 'Activar'"></button>
                                <button type="button" @click="destroyItem(item)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4" @click.self="closeModal()">
        <form class="bg-white w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col rounded-2xl shadow-xl" @submit.prevent="saveItem()">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar elemento' : 'Nuevo elemento'"></h3>
            </div>

            <div class="flex-1 space-y-4 overflow-y-auto p-5">
                <div x-show="errorMessage" x-cloak class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="errorMessage"></div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Título</label>
                    <input type="text" x-model="form.titulo" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    <p class="mt-1 text-xs text-rose-600" x-text="fieldError('titulo')"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Descripción</label>
                    <textarea x-model="form.descripcion" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                    <p class="mt-1 text-xs text-rose-600" x-text="fieldError('descripcion')"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Imagen *</label>
                    <input type="file" accept="image/*" @change="onImageSelected($event)" :required="!editingId" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700">
                    <p class="mt-1 text-xs text-slate-500">Se mantiene la imagen completa y solo se redimensiona si supera 1200px de ancho.</p>
                    <p class="mt-1 text-xs text-rose-600" x-text="fieldError('imagen')"></p>

                    <img x-show="form.imagenPreview || form.imagenActual" x-cloak :src="form.imagenPreview || form.imagenActual" alt="Vista previa imagen" class="mt-3 h-40 w-full rounded-xl bg-white object-contain ring-1 ring-slate-200">
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                        <input type="number" min="0" x-model="form.orden" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    </div>
                    <label class="inline-flex items-center gap-2 pt-7 text-sm text-slate-700"><input type="checkbox" x-model="form.activo" class="rounded border-slate-300 text-blue-600"> Activo</label>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 bg-white px-5 py-4">
                <button type="button" @click="closeModal()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">Cancelar</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white" :disabled="loading">Guardar</button>
            </div>
        </form>
    </div>
</section>

<script>
function ofrecerManager({ initialItems, indexUrl, storeUrl, updateUrlTemplate, activateUrlTemplate, deactivateUrlTemplate, destroyUrlTemplate }) {
    return {
        items: initialItems ?? [],
        showModal: false,
        editingId: null,
        loading: false,
        errorMessage: '',
        validationErrors: {},
        form: { titulo: '', descripcion: '', imagenFile: null, imagenPreview: '', imagenActual: '', orden: 0, activo: true },
        openCreateModal() {
            this.editingId = null
            this.form = { titulo: '', descripcion: '', imagenFile: null, imagenPreview: '', imagenActual: '', orden: 0, activo: true }
            this.errorMessage = ''
            this.validationErrors = {}
            this.showModal = true
        },
        openEditModal(item) {
            this.editingId = item.id
            this.form = {
                titulo: item.titulo ?? '',
                descripcion: item.descripcion ?? '',
                imagenFile: null,
                imagenPreview: '',
                imagenActual: item.imagen_url ?? '',
                orden: item.orden ?? 0,
                activo: Boolean(item.activo),
            }
            this.errorMessage = ''
            this.validationErrors = {}
            this.showModal = true
        },
        closeModal() { this.showModal = false; this.editingId = null },
        fieldError(field) { return this.validationErrors[field]?.[0] || '' },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        onImageSelected(event) {
            const file = event?.target?.files?.[0] || null
            this.form.imagenFile = file
            if (!file) {
                this.form.imagenPreview = ''
                return
            }
            const reader = new FileReader()
            reader.onload = (e) => { this.form.imagenPreview = e.target?.result || '' }
            reader.readAsDataURL(file)
        },
        payload(editing) {
            const data = new FormData()
            data.append('titulo', (this.form.titulo || '').trim())
            data.append('descripcion', (this.form.descripcion || '').trim())
            data.append('orden', this.form.orden === '' ? 0 : Number(this.form.orden))
            data.append('activo', Boolean(this.form.activo) ? 1 : 0)
            if (this.form.imagenFile instanceof File) data.append('imagen', this.form.imagenFile)
            if (editing) data.append('_method', 'PATCH')
            return data
        },
        async refreshList() {
            const r = await fetch(indexUrl, { headers: { Accept: 'application/json' } })
            const j = await r.json()
            this.items = j.data || []
        },
        async saveItem() {
            this.loading = true
            this.errorMessage = ''
            this.validationErrors = {}
            const editing = Boolean(this.editingId)
            const endpoint = editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl

            try {
                const response = await fetch(endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' }, body: this.payload(editing) })
                if (!response.ok) {
                    const body = await response.json().catch(() => ({}))
                    this.validationErrors = body.errors || {}
                    this.errorMessage = body.message || 'No fue posible guardar el elemento.'
                    return
                }
                this.closeModal()
                await this.refreshList()
            } finally {
                this.loading = false
            }
        },
        async toggleEstado(item) {
            const url = item.activo ? deactivateUrlTemplate.replace('__ID__', item.id) : activateUrlTemplate.replace('__ID__', item.id)
            const response = await fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshList()
        },
        async destroyItem(item) {
            if (!confirm(`¿Eliminar "${item.titulo || 'este elemento'}"?`)) return
            const response = await fetch(destroyUrlTemplate.replace('__ID__', item.id), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshList()
        },
    }
}
</script>
