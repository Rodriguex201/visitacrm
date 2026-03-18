<section
    class="space-y-4"
    x-data="logoSidebarManager({ initialLogo: @js($logoSidebarActual ? asset($logoSidebarActual) : null) })"
>
    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->updateLogoSidebar->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->updateLogoSidebar->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-xl space-y-2">
                <h2 class="text-lg font-semibold text-slate-900">Logo del sistema</h2>
                <p class="text-sm text-slate-600">Sube un logo principal para el sidebar. Se recomienda usar PNG con transparencia. La imagen se ajusta automáticamente con <code>object-contain</code> para no romper el layout.</p>
                <ul class="list-inside list-disc space-y-1 text-sm text-slate-500">
                    <li>Formatos permitidos: PNG, JPG, JPEG y WEBP.</li>
                    <li>Tamaño máximo: 2MB.</li>
                    <li>Ideal para logos horizontales o transparentes.</li>
                </ul>

                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Logo sidebar</p>
                    <p class="mt-1 text-sm text-slate-600">Ruta actual del logo mostrado en el sidebar.</p>
                    <p class="mt-3 break-all rounded-lg border border-slate-200 bg-white px-3 py-2 font-mono text-xs text-slate-700">
                        {{ $logoSidebarActual ?: 'Sin logo configurado' }}
                    </p>
                </div>
            </div>

            <div class="flex w-full max-w-sm justify-center lg:justify-end">
                <div class="flex h-44 w-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" alt="Preview del logo" class="h-full w-full object-contain" />
                    </template>
                    <template x-if="!previewUrl">
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-700">Sin logo cargado</p>
                            <p class="mt-1 text-xs text-slate-500">Aquí verás la previsualización antes de guardar.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('configuracion.logo.update') }}?tab=logo" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="logo_sidebar" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">Seleccionar nuevo logo</label>
                <input
                    id="logo_sidebar"
                    type="file"
                    name="logo"
                    accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700"
                    @change="updatePreview($event)"
                    required
                >
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Guardar logo
                </button>
                <p class="text-xs text-slate-500">Si ya existe un logo anterior, se reemplazará automáticamente.</p>
            </div>
        </form>
    </div>
</section>

<script>
function logoSidebarManager({ initialLogo = null }) {
    return {
        previewUrl: initialLogo,
        updatePreview(event) {
            const [file] = event.target.files || []

            if (!file) {
                this.previewUrl = initialLogo
                return
            }

            this.previewUrl = URL.createObjectURL(file)
        },
    }
}
</script>
