<section class="space-y-4" x-data="bancosManager({
    initialBancos: @js($bancos),
    indexUrl: @js(route('configuracion.bancos.index')),
    storeUrl: @js(route('configuracion.bancos.store')),
    updateUrlTemplate: @js(route('configuracion.bancos.update', ['banco' => '__ID__'])),
    destroyUrlTemplate: @js(route('configuracion.bancos.destroy', ['banco' => '__ID__'])),
})">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-slate-900">Bancos</h2>
        <button type="button" @click="openCreateModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            + Agregar
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Activo</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="banco in bancos" :key="banco.id">
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800" x-text="banco.nombre"></td>
                        <td class="px-4 py-3">
                            <span x-show="banco.activo" class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Activo</span>
                            <span x-show="!banco.activo" class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">Inactivo</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openEditModal(banco)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Editar</button>
                                <button type="button" @click="destroyBanco(banco)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700" x-text="banco.can_delete ? 'Eliminar' : 'Desactivar'"></button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4" @click.self="closeModal()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar banco' : 'Nuevo banco'"></h3>
            <form class="mt-4 space-y-4" @submit.prevent="saveBanco()">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                    <input type="text" x-model="form.nombre" maxlength="120" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
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
function bancosManager({ initialBancos, indexUrl, storeUrl, updateUrlTemplate, destroyUrlTemplate }) {
    return {
        bancos: initialBancos ?? [],
        showModal: false,
        editingId: null,
        loading: false,
        form: { nombre: '' },
        openCreateModal() { this.editingId = null; this.form = { nombre: '' }; this.showModal = true },
        openEditModal(banco) { this.editingId = banco.id; this.form = { nombre: banco.nombre ?? '' }; this.showModal = true },
        closeModal() { this.showModal = false },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        async refreshBancos() { const r = await fetch(indexUrl, { headers: { Accept: 'application/json' } }); const j = await r.json(); this.bancos = j.data || [] },
        async saveBanco() {
            this.loading = true
            const editing = Boolean(this.editingId)
            const response = await fetch(editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl, {
                method: editing ? 'PATCH' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' },
                body: JSON.stringify({ nombre: (this.form.nombre || '').trim() }),
            })
            if (response.ok) { this.closeModal(); await this.refreshBancos() }
            this.loading = false
        },
        async destroyBanco(banco) {
            const accion = banco.can_delete ? 'eliminar' : 'desactivar'
            if (!confirm(`¿Deseas ${accion} el banco "${banco.nombre}"?`)) return
            const response = await fetch(destroyUrlTemplate.replace('__ID__', banco.id), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshBancos()
        },
    }
}
</script>
