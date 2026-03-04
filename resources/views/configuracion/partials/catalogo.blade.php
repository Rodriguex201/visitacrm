<section class="space-y-4" x-data="catalogoManager({
    categoriaSlug: @js($slug),
    categoriaNombre: @js($nombre),
    initialOpciones: @js($opciones),
    indexUrl: @js(route('configuracion.catalogo.index', ['categoria' => $slug])),
    storeUrl: @js(route('configuracion.catalogo.store')),
    updateUrlTemplate: @js(route('configuracion.catalogo.update', ['catalogoOpcion' => '__ID__'])),
    destroyUrlTemplate: @js(route('configuracion.catalogo.destroy', ['catalogoOpcion' => '__ID__'])),
})">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-slate-900">{{ $nombre }}</h2>
        <button type="button" @click="openCreateModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Agregar</button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3" x-show="isCotizaciones">Valor</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Activo</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="opcion in opciones" :key="opcion.id">
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800" x-text="opcion.nombre"></td>
                        <td class="px-4 py-3 text-slate-600" x-show="isCotizaciones" x-text="opcion.valor ?? '—'"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="opcion.orden"></td>
                        <td class="px-4 py-3"><span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Activo</span></td>
                        <td class="px-4 py-3"><div class="flex justify-end gap-2">
                            <button type="button" @click="openEditModal(opcion)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Editar</button>
                            <button type="button" @click="destroyItem(opcion)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Desactivar</button>
                        </div></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4" @click.self="closeModal()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar opción' : 'Nueva opción'"></h3>
            <form class="mt-4 space-y-4" @submit.prevent="saveItem()">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                    <input type="text" x-model="form.nombre" maxlength="255" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div x-show="isCotizaciones">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Valor</label>
                    <input type="number" step="0.01" min="0" x-model="form.valor" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                    <input type="number" x-model="form.orden" min="0" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="closeModal()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white" :disabled="loading">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function catalogoManager({ categoriaSlug, initialOpciones, indexUrl, storeUrl, updateUrlTemplate, destroyUrlTemplate }) {
    return {
        categoriaSlug,
        isCotizaciones: categoriaSlug === 'cotizaciones',
        opciones: initialOpciones ?? [],
        showModal: false,
        editingId: null,
        loading: false,
        form: { nombre: '', valor: '', orden: 0 },
        openCreateModal() { this.editingId = null; this.form = { nombre: '', valor: '', orden: 0 }; this.showModal = true },
        openEditModal(opcion) { this.editingId = opcion.id; this.form = { nombre: opcion.nombre ?? '', valor: opcion.valor ?? '', orden: opcion.orden ?? 0 }; this.showModal = true },
        closeModal() { this.showModal = false },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        async refreshList() { const r = await fetch(indexUrl, { headers: { Accept: 'application/json' } }); const j = await r.json(); this.opciones = j.data || [] },
        payload() {
            return {
                categoria: this.categoriaSlug,
                nombre: (this.form.nombre || '').trim(),
                orden: this.form.orden === '' ? 0 : Number(this.form.orden),
                valor: this.isCotizaciones && this.form.valor !== '' ? Number(this.form.valor) : null,
            }
        },
        async saveItem() {
            this.loading = true
            const editing = Boolean(this.editingId)
            const response = await fetch(editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl, {
                method: editing ? 'PATCH' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' },
                body: JSON.stringify(this.payload()),
            })
            if (response.ok) { this.closeModal(); await this.refreshList() }
            this.loading = false
        },
        async destroyItem(opcion) {
            if (!confirm(`¿Desactivar "${opcion.nombre}"?`)) return
            const response = await fetch(destroyUrlTemplate.replace('__ID__', opcion.id), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshList()
        },
    }
}
</script>
