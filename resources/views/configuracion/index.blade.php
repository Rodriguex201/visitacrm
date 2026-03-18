@extends('layouts.app')

@section('content')

<section class="space-y-5" x-data="configuracionPage({ esAdministracion: @js($esAdministracion), validarClaveUrl: @js($validarClaveUrl) })">

    <header>
        <h1 class="text-2xl font-bold text-slate-950">Configuración</h1>
        <p class="mt-1 text-sm text-slate-600">Gestión inicial de catálogos del sistema.</p>
    </header>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <nav class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
            <template x-for="tab in tabs" :key="tab.key">
                <button type="button"
                    class="rounded-lg px-3 py-2 text-sm font-semibold transition"
                    :class="activeTab === tab.key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'"
                    @click="handleTabClick(tab)"
                    x-text="tab.label">
                </button>
            </template>
        </nav>

        <div class="mt-4" x-show="activeTab === 'sectores'" x-cloak>
            @include('configuracion.partials.sectores')
        </div>

        @foreach ($categorias as $slug => $nombre)
            <div class="mt-4" x-show="activeTab === '{{ $slug }}'" x-cloak>
                @include('configuracion.partials.catalogo', [
                    'slug' => $slug,
                    'nombre' => $nombre,
                    'opciones' => $catalogoPorCategoria[$slug] ?? collect(),
                ])
            </div>
        @endforeach

        <div class="mt-4" x-show="activeTab === 'estados-colores'" x-cloak>
            @include('configuracion.partials.estado_referido_colores', [
                'estadosReferidoColores' => $estadosReferidoColores,
            ])
        </div>

        <div class="mt-4" x-show="activeTab === 'bancos'" x-cloak>
            @include('configuracion.partials.bancos', [
                'bancos' => $bancos,
            ])
        </div>

        @if ($esAdministracion)
            <div class="mt-4" x-show="activeTab === 'claves'" x-cloak>
                @include('configuracion.partials.claves', [
                    'claveAdmin' => $claveAdmin,
                ])
            </div>
        @endif
    </div>

    @if ($esAdministracion)
        <div
            x-show="showClaveModal"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="closeClaveModal()"
            x-cloak
        ></div>

        <div
            x-show="showClaveModal"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Ingrese clave de acceso</h2>
                    <p class="mt-1 text-sm text-slate-600">Debes validar la clave administrativa antes de acceder a la pestaña Claves.</p>
                </div>

                <form class="space-y-3" @submit.prevent="submitClaveAccess()">
                    <div>
                        <label for="clave_acceso_configuracion" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Clave de acceso</label>
                        <input
                            id="clave_acceso_configuracion"
                            type="password"
                            x-model="claveInput"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="••••••••"
                        >
                        <p class="mt-1 text-xs font-medium text-rose-600" x-show="claveError" x-text="claveError"></p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button type="button" @click="closeClaveModal()" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Acceder</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>

<script>

    function configuracionPage({ esAdministracion, validarClaveUrl }) {

        const unlockedStorageKey = 'configuracion-claves-unlocked'

        return {
            tabs: [
                { key: 'sectores', label: 'Sectores' },
                { key: 'estado-actual', label: 'Estado Actual' },
                { key: 'aplicativos', label: 'Aplicativos' },
                { key: 'procesos-electronicos', label: 'Procesos Electrónicos' },
                { key: 'equipos', label: 'Equipos' },
                { key: 'estados-colores', label: 'Estados (Colores)' },
                { key: 'bancos', label: 'Bancos' },
                ...(esAdministracion ? [{ key: 'claves', label: 'Claves', protected: true }] : []),
            ],
            activeTab: 'sectores',
            showClaveModal: false,
            claveInput: '',
            claveError: '',
            pendingProtectedTab: null,
            clavesUnlocked: false,
            init() {
                this.clavesUnlocked = esAdministracion && sessionStorage.getItem(unlockedStorageKey) === '1'
            },
            handleTabClick(tab) {
                if (tab.protected && !this.clavesUnlocked) {
                    this.pendingProtectedTab = tab.key
                    this.claveInput = ''
                    this.claveError = ''
                    this.showClaveModal = true
                    return
                }

                this.activeTab = tab.key
            },

            async submitClaveAccess() {
                this.claveError = ''

                const response = await fetch(validarClaveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({ clave: this.claveInput }),
                })

                if (!response.ok) {

                    this.claveError = 'Clave incorrecta'
                    return
                }

                this.clavesUnlocked = true
                sessionStorage.setItem(unlockedStorageKey, '1')
                this.activeTab = this.pendingProtectedTab || 'claves'
                this.closeClaveModal()
            },
            closeClaveModal() {
                this.showClaveModal = false
                this.pendingProtectedTab = null
                this.claveInput = ''
                this.claveError = ''

            },
        }
    }
</script>
@endsection
