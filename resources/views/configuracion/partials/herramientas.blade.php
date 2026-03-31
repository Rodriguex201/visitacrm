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
                    <th class="px-4 py-3">URL</th>
                    <th class="px-4 py-3">Ícono</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Colores</th>
                    <th class="px-4 py-3">Pestaña</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="herramienta in herramientas" :key="herramienta.id">
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800" x-text="herramienta.nombre"></p>
                            <p class="text-xs text-slate-500" x-text="herramienta.descripcion || 'Sin descripción'"></p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <a :href="herramienta.url" class="text-blue-600 underline" target="_blank" rel="noopener noreferrer">Abrir</a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700" x-text="herramienta.icono || 'fallback'"></span>
                        </td>
                        <td class="px-4 py-3 text-slate-600" x-text="herramienta.orden"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span class="h-6 w-6 rounded-full border border-slate-200" :style="`background:${herramienta.color_fondo || '#F8FAFC'}`" title="Color fondo"></span>
                                <span class="h-6 w-6 rounded-full border border-slate-200" :style="`background:${herramienta.color_texto || '#0F172A'}`" title="Color texto"></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600" x-text="herramienta.abrir_en_nueva_pestana ? 'Nueva' : 'Misma'"></td>
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
        <div class="w-full max-w-3xl rounded-2xl bg-white p-5 shadow-xl">
            <h3 class="text-lg font-semibold text-slate-900" x-text="editingId ? 'Editar herramienta' : 'Nueva herramienta'"></h3>
            <form class="mt-4 space-y-4" @submit.prevent="saveItem()">

                <div x-show="errorMessage" x-cloak class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="errorMessage"></div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre *</label>
                        <input type="text" x-model="form.nombre" maxlength="255" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="Ej: Portal de soporte">
                        <p class="mt-1 text-xs text-rose-600" x-text="fieldError('nombre')"></p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                        <input type="number" min="0" x-model="form.orden" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="0">
                        <p class="mt-1 text-xs text-slate-500">Se muestra de menor a mayor.</p>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Descripción</label>
                    <input type="text" x-model="form.descripcion" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="Ej: Acceso rápido a tickets y ayuda técnica">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">URL *</label>
                    <input type="url" x-model="form.url" required class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="https://soporte.tudominio.com">
                    <p class="mt-1 text-xs text-rose-600" x-text="fieldError('url')"></p>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Ícono</label>
                    <input type="text" x-model="form.icono" maxlength="255" class="h-10 w-full rounded-lg border border-slate-300 px-3 text-sm" placeholder="Ej: whatsapp, globe, web, link, support, catalogo">
                    <p class="mt-1 text-xs text-slate-500">Valores sugeridos: <span class="font-medium">whatsapp</span>, <span class="font-medium">globe</span>, <span class="font-medium">web</span>, <span class="font-medium">link</span>, <span class="font-medium">support</span>, <span class="font-medium">catalogo</span>.</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <template x-for="item in quickIcons" :key="item">
                            <button type="button" class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100" @click="form.icono = item" x-text="item"></button>
                        </template>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color de fondo</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.color_fondo" class="h-10 w-14 rounded-lg border border-slate-300 px-1">
                            <input type="text" x-model="form.color_fondo" maxlength="7" class="h-10 flex-1 rounded-lg border border-slate-300 px-3 text-sm" placeholder="#F8FAFC">
                        </div>
                        <p class="mt-1 text-xs text-rose-600" x-text="fieldError('color_fondo')"></p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color de texto</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.color_texto" class="h-10 w-14 rounded-lg border border-slate-300 px-1">
                            <input type="text" x-model="form.color_texto" maxlength="7" class="h-10 flex-1 rounded-lg border border-slate-300 px-3 text-sm" placeholder="#0F172A">
                        </div>
                        <p class="mt-1 text-xs text-rose-600" x-text="fieldError('color_texto')"></p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Vista previa rápida</p>
                    <div class="rounded-2xl p-4 shadow-sm ring-1 ring-black/5" :style="`background:${form.color_fondo || '#F8FAFC'}; color:${form.color_texto || '#0F172A'}`">
                        <p class="text-sm font-semibold" x-text="form.nombre || 'Nombre de herramienta'"></p>
                        <p class="text-xs opacity-80" x-text="form.descripcion || 'Descripción de la herramienta'"></p>
                        <p class="mt-2 text-[11px] opacity-70" x-text="`Icono: ${form.icono || 'fallback'}`"></p>
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
        quickIcons: ['whatsapp', 'globe', 'web', 'link', 'support', 'catalogo'],
        errorMessage: '',
        validationErrors: {},
        form: { nombre: '', descripcion: '', url: '', icono: '', color_fondo: '#F8FAFC', color_texto: '#0F172A', orden: 0, activo: true, abrir_en_nueva_pestana: true },
        openCreateModal() {
            this.editingId = null
            this.form = { nombre: '', descripcion: '', url: '', icono: '', color_fondo: '#F8FAFC', color_texto: '#0F172A', orden: 0, activo: true, abrir_en_nueva_pestana: true }
            this.errorMessage = ''
            this.validationErrors = {}
            this.showModal = true
        },
        openEditModal(herramienta) {
            this.editingId = herramienta.id
            this.form = {
                nombre: herramienta.nombre ?? '',
                descripcion: herramienta.descripcion ?? '',
                url: herramienta.url ?? '',
                icono: herramienta.icono ?? '',
                color_fondo: herramienta.color_fondo ?? '#F8FAFC',
                color_texto: herramienta.color_texto ?? '#0F172A',
                orden: herramienta.orden ?? 0,
                activo: Boolean(herramienta.activo),
                abrir_en_nueva_pestana: Boolean(herramienta.abrir_en_nueva_pestana),
            }
            this.errorMessage = ''
            this.validationErrors = {}
            this.showModal = true
        },
        closeModal() { this.showModal = false; this.editingId = null },
        fieldError(field) { return this.validationErrors[field]?.[0] || '' },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        payload() {
            return {
                nombre: (this.form.nombre || '').trim(),
                descripcion: (this.form.descripcion || '').trim() || null,
                url: (this.form.url || '').trim(),
                icono: (this.form.icono || '').trim().toLowerCase() || null,
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
            this.errorMessage = ''
            this.validationErrors = {}

            const editing = Boolean(this.editingId)
            const endpoint = editing ? updateUrlTemplate.replace('__ID__', this.editingId) : storeUrl
            const payload = this.payload()

            if (editing) {
                payload._method = 'PATCH'
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' },
                    body: JSON.stringify(payload),
                })

                if (!response.ok) {
                    const body = await response.json().catch(() => ({}))
                    this.validationErrors = body.errors || {}
                    this.errorMessage = body.message || 'No fue posible guardar la herramienta.'
                    return
                }

                this.closeModal()
                await this.refreshList()
            } catch (_) {
                this.errorMessage = 'Ocurrió un error de red al intentar guardar.'
            } finally {
                this.loading = false
            }
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
