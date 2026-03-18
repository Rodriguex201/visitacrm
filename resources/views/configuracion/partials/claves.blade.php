<section class="space-y-4" x-data="clavesManager({
    initialClaves: @js($clavesSistema),
    updateUrlTemplate: @js($actualizarClaveUrlTemplate),
})">
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Gestión de claves del sistema</h2>
                <p class="mt-1 text-sm text-slate-600">Centraliza las claves críticas configuradas para el sistema sin exponerlas automáticamente al ingresar.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Uso administrativo</span>
        </div>

        <div class="mt-4 grid gap-4">
            <template x-for="item in claves" :key="item.id">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500" x-text="item.nombre"></p>
                            <p class="mt-1 text-sm text-slate-500" x-show="item.descripcion" x-text="item.descripcion"></p>

                            <template x-if="editingId !== item.id">
                                <div class="mt-3 inline-flex min-h-11 min-w-[12rem] items-center rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 font-mono text-sm font-semibold tracking-[0.3em] text-slate-700">
                                    <span x-text="item.visible ? item.valor : maskedValue(item.valor)"></span>
                                </div>
                            </template>

                            <template x-if="editingId === item.id">
                                <form class="mt-3 space-y-3" @submit.prevent="saveItem()">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Editar clave</label>
                                        <input
                                            :type="editVisible ? 'text' : 'password'"
                                            x-model="editValue"
                                            required
                                            class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                            placeholder="••••••••"
                                        >
                                        <p class="mt-1 text-xs font-medium text-rose-600" x-show="errorMessage" x-text="errorMessage"></p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <button type="button" @click="editVisible = !editVisible" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                            <span x-html="eyeIcon(editVisible)"></span>
                                            <span x-text="editVisible ? 'Ocultar' : 'Mostrar'"></span>
                                        </button>
                                        <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60" :disabled="loading">
                                            <span x-text="loading ? 'Guardando...' : 'Guardar'"></span>
                                        </button>
                                        <button type="button" @click="cancelEdit()" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</button>
                                    </div>
                                </form>
                            </template>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                @click="toggleVisibility(item.id)"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                :aria-label="item.visible ? 'Ocultar clave' : 'Mostrar clave'"
                            >
                                <span x-html="eyeIcon(item.visible)"></span>
                                <span x-text="item.visible ? 'Ocultar' : 'Mostrar'"></span>
                            </button>

                            <button
                                type="button"
                                @click="startEdit(item)"
                                class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                <span x-html="editIcon()"></span>
                                <span>Editar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</section>

<script>
function clavesManager({ initialClaves, updateUrlTemplate }) {
    return {
        claves: (initialClaves ?? []).map((item) => ({ ...item, visible: false })),
        updateUrlTemplate,
        editingId: null,
        editValue: '',
        editVisible: false,
        loading: false,
        errorMessage: '',
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        maskedValue(value) {
            return value && String(value).length > 0 ? '*'.repeat(Math.max(8, String(value).length)) : '********'
        },
        toggleVisibility(id) {
            this.claves = this.claves.map((item) => item.id === id ? { ...item, visible: !item.visible } : item)
        },
        startEdit(item) {
            this.editingId = item.id
            this.editValue = item.valor ?? ''
            this.editVisible = false
            this.errorMessage = ''
        },
        cancelEdit() {
            this.editingId = null
            this.editValue = ''
            this.editVisible = false
            this.errorMessage = ''
            this.loading = false
        },
        async saveItem() {
            if (!this.editingId) return

            this.loading = true
            this.errorMessage = ''

            const response = await fetch(this.updateUrlTemplate.replace('__ID__', this.editingId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken(),
                    Accept: 'application/json',
                },
                body: JSON.stringify({ valor: this.editValue }),
            })

            if (!response.ok) {
                this.errorMessage = 'No fue posible guardar la clave.'
                this.loading = false
                return
            }

            const payload = await response.json()
            const updatedItem = payload.data

            this.claves = this.claves.map((item) => item.id === updatedItem.id
                ? { ...item, ...updatedItem, visible: false }
                : item)

            this.cancelEdit()
        },
        eyeIcon(visible) {
            return visible
                ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c1.887 0 3.678-.497 5.23-1.376M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>`
        },
        editIcon() {
            return `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM19.5 7.125 16.875 4.5" /></svg>`
        },
    }
}
</script>
