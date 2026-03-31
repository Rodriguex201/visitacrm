<section class="space-y-4" x-data="herramientasManager({
    initialHerramientas: @js($herramientas),
    indexUrl: @js(route('configuracion.herramientas.index')),
    storeUrl: @js(route('configuracion.herramientas.store')),
    updateUrlTemplate: @js(route('configuracion.herramientas.update', ['herramientaDisponible' => '__ID__'])),
    activateUrlTemplate: @js(route('configuracion.herramientas.activate', ['herramientaDisponible' => '__ID__'])),
    deactivateUrlTemplate: @js(route('configuracion.herramientas.deactivate', ['herramientaDisponible' => '__ID__'])),
    destroyUrlTemplate: @js(route('configuracion.herramientas.destroy', ['herramientaDisponible' => '__ID__'])),
})">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-semibold text-slate-900">Herramientas</h2>
        <button type="button" @click="openCreateModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Agregar</button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">URL</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="herramienta in herramientas" :key="herramienta.id">
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800" x-text="herramienta.nombre"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="herramienta.descripcion || '—'"></td>
                        <td class="px-4 py-3 text-slate-600"><a :href="herramienta.url" class="text-blue-600 underline" target="_blank" rel="noopener noreferrer">Abrir</a></td>
                        <td class="px-4 py-3 text-slate-600" x-text="herramienta.orden"></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="herramienta.activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'" x-text="herramienta.activo ? 'Activo' : 'Inactivo'"></span>
                        </td>
                        <td class="px-4 py-3"><div class="flex justify-end gap-2">
                            <button type="button" @click="openEditModal(herramienta)" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Editar</button>
                            <button type="button" @click="toggleEstado(herramienta)" class="rounded-lg border px-3 py-1.5 text-xs font-semibold" :class="herramienta.activo ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'" x-text="herramienta.activo ? 'Desactivar' : 'Activar'"></button>
                            <button type="button" @click="destroyItem(herramienta)" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Eliminar</button>
                        </div></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4" @click.self="closeModal()">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar herramienta' : 'Nueva herramienta'"></h3>
            <form class="mt-4 space-y-4" @submit.prevent="saveItem()">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                        <input type="text" x-model="form.nombre" maxlength="255" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Slug (opcional)</label>
                        <input type="text" x-model="form.slug" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Descripción</label>
                    <input type="text" x-model="form.descripcion" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">URL</label>
                    <input type="url" x-model="form.url" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="https://...">
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ícono</label>
                        <input type="text" x-model="form.icono" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="🚀 o clase de ícono">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                        <input type="number" min="0" x-model="form.orden" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color de fondo</label>
                        <input type="color" x-model="form.color_fondo" class="h-10 w-full rounded-lg border border-slate-300 px-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color de texto</label>
                        <input type="color" x-model="form.color_texto" class="h-10 w-full rounded-lg border border-slate-300 px-2 text-sm">
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" x-model="form.activo" class="rounded border-slate-300 text-blue-600"> Activo</label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" x-model="form.abrir_en_nueva_pestana" class="rounded border-slate-300 text-blue-600"> Abrir en nueva pestaña</label>
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
function herramientasManager({ initialHerramientas, indexUrl, storeUrl, updateUrlTemplate, activateUrlTemplate, deactivateUrlTemplate, destroyUrlTemplate }) {
    return {
        herramientas: initialHerramientas ?? [],
        showModal: false,
        editingId: null,
        loading: false,
        form: { nombre: '', slug: '', descripcion: '', url: '', icono: '', color_fondo: '#F8FAFC', color_texto: '#0F172A', orden: 0, activo: true, abrir_en_nueva_pestana: true },
        openCreateModal() {
            this.editingId = null
            this.form = { nombre: '', slug: '', descripcion: '', url: '', icono: '', color_fondo: '#F8FAFC', color_texto: '#0F172A', orden: 0, activo: true, abrir_en_nueva_pestana: true }
            this.showModal = true
        },
        openEditModal(herramienta) {
            this.editingId = herramienta.id
            this.form = {
                nombre: herramienta.nombre ?? '',
                slug: herramienta.slug ?? '',
                descripcion: herramienta.descripcion ?? '',
                url: herramienta.url ?? '',
                icono: herramienta.icono ?? '',
                color_fondo: herramienta.color_fondo ?? '#F8FAFC',
                color_texto: herramienta.color_texto ?? '#0F172A',
                orden: herramienta.orden ?? 0,
                activo: Boolean(herramienta.activo),
                abrir_en_nueva_pestana: Boolean(herramienta.abrir_en_nueva_pestana),
            }
            this.showModal = true
        },
        closeModal() { this.showModal = false },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        payload() {
            return {
                nombre: (this.form.nombre || '').trim(),
                slug: (this.form.slug || '').trim() || null,
                descripcion: (this.form.descripcion || '').trim() || null,
                url: (this.form.url || '').trim(),
                icono: (this.form.icono || '').trim() || null,
                color_fondo: this.form.color_fondo || null,
                color_texto: this.form.color_texto || null,
                orden: this.form.orden === '' ? 0 : Number(this.form.orden),
                activo: Boolean(this.form.activo),
                abrir_en_nueva_pestana: Boolean(this.form.abrir_en_nueva_pestana),
            }
        },
        async refreshList() {
            const r = await fetch(indexUrl, { headers: { Accept: 'application/json' } })
            const j = await r.json()
            this.herramientas = j.data || []
        },
        async saveItem() {
            this.loading = true
            const editing = Boolean(this.editingId)
            const response = await fetch(editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl, {
                method: editing ? 'PATCH' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' },
                body: JSON.stringify(this.payload()),
            })
            if (response.ok) {
                this.closeModal()
                await this.refreshList()
            }
            this.loading = false
        },
        async toggleEstado(herramienta) {
            const url = herramienta.activo ? deactivateUrlTemplate.replace('__ID__', herramienta.id) : activateUrlTemplate.replace('__ID__', herramienta.id)
            const response = await fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshList()
        },
        async destroyItem(herramienta) {
            if (!confirm(`¿Eliminar "${herramienta.nombre}"?`)) return
            const response = await fetch(destroyUrlTemplate.replace('__ID__', herramienta.id), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' } })
            if (response.ok) await this.refreshList()
        },
    }
}
</script>
