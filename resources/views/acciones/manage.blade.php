@extends('layouts.app')

@section('content')
<section class="space-y-4 pb-24" x-data="{ showCreateAccion: {{ $errors->any() ? 'true' : 'false' }} }">
    <header class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Gestionar Acciones</h1>
            <p class="text-sm text-slate-500">Edita nombre, icono, color, estado y orden del catálogo.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="showCreateAccion = !showCreateAccion" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">+ Agregar acción</button>
            <a href="{{ url()->previous() }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">Volver</a>
        </div>
    </header>

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <article x-cloak x-show="showCreateAccion" x-transition class="rounded-xl border border-slate-100 bg-white p-5 shadow-sm" x-data="accionEditor({
        icono: @js(old('icono', $defaultIcono)),
        color: @js(old('color', '#000000')),
        iconos: @js($iconosPermitidos),
    })">
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="text-base font-semibold text-slate-900">Nueva acción</h2>
        </div>

        <form method="POST" action="{{ route('acciones.store') }}" class="grid gap-3 md:grid-cols-6">
            @csrf

            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Icono</label>
                <select name="icono" x-model="icono" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($iconosPermitidos as $icono)
                        <option value="{{ $icono }}" @selected(old('icono', $defaultIcono) === $icono)>{{ $icono }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" name="color" x-model="color" value="{{ old('color', '#000000') }}" class="h-10 w-14 cursor-pointer rounded border border-slate-300 bg-white p-1">
                    <input type="text" x-model="color" readonly class="h-10 w-24 rounded-lg border-slate-300 bg-slate-50 px-2 text-xs text-slate-600">
                </div>
            </div>

            <div class="md:col-span-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                <input type="number" min="1" name="orden" value="{{ old('orden', $nextOrden) }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-3 flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', 1)) class="rounded border-slate-300 text-blue-600">
                    Activo
                </label>
            </div>

            <div class="md:col-span-2 flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-700" x-html="iconoSvg(icono, color)"></span>
                <span class="text-xs font-medium">Preview</span>
            </div>

            <div class="md:col-span-1 flex justify-end">
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Guardar</button>
            </div>
        </form>
    </article>

    <article class="space-y-3 rounded-xl border border-slate-100 bg-white p-5 shadow-sm">
        @foreach($acciones as $accion)
            <div
                class="rounded-xl border border-slate-100 p-4"
                x-data="accionEditor({

                    icono: @js(old('icono', $accion->icono)),
                    color: @js($accion->color ?: '#2563EB'),

                    iconos: @js($iconosPermitidos),
                })"
            >
                <div class="grid gap-4 lg:grid-cols-6">
                    <div class="lg:col-span-5">
                        <form method="POST" action="{{ route('acciones.update', $accion) }}" class="grid gap-3 md:grid-cols-6">
                            @csrf
                            @method('PATCH')

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $accion->nombre) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Icono</label>
                                <select name="icono" x-model="icono" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    @foreach ($iconosPermitidos as $icono)
                                        <option value="{{ $icono }}" @selected(old('icono', $accion->icono) === $icono)>{{ $icono }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Color</label>

                                <div class="flex items-center gap-2">
                                    <input type="color" name="color" x-model="color" value="{{ $accion->color ?: '#2563EB' }}" class="h-10 w-14 cursor-pointer rounded border border-slate-300 bg-white p-1">
                                    <input type="text" x-model="color" readonly class="h-10 w-24 rounded-lg border-slate-300 bg-slate-50 px-2 text-xs text-slate-600">
                                </div>

                            </div>

                            <div class="md:col-span-1">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Orden</label>
                                <input type="number" min="1" name="orden" value="{{ old('orden', (int) $accion->orden) }}" required class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-3 flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="hidden" name="activo" value="0">
                                    <input type="checkbox" name="activo" value="1" @checked($accion->activo) class="rounded border-slate-300 text-blue-600">
                                    Activo
                                </label>
                            </div>

                            <div class="md:col-span-3 flex justify-end">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Guardar</button>
                            </div>
                        </form>
                    </div>

                    <div class="lg:col-span-1 flex flex-col items-end justify-between gap-3">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-slate-700">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-700" x-html="iconoSvg(icono, color)"></span>
                                <span class="text-xs font-medium">Preview</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <x-lucide-icon :name="$accion->icono" :color="$accion->color ?: null" />
                                <span>Icono actual guardado</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('acciones.destroy', $accion) }}" onsubmit="return confirm('¿Eliminar esta acción? Si está en uso se desactivará.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </article>
</section>

<script>
    function accionEditor({ icono, color, iconos }) {
        return {
            icono,
            color,
            iconos,
            iconoSvg(iconName, iconColor = '') {
                const stroke = iconColor || 'currentColor';
                const attrs = `width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${stroke}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"`;
                const icons = {
                    'phone': `<svg ${attrs}><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.78.68 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.25a2 2 0 0 1 2.11-.45c.84.33 1.72.56 2.62.68A2 2 0 0 1 22 16.92z"/></svg>`,
                    'globe': `<svg ${attrs}><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>`,
                    'video': `<svg ${attrs}><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>`,
                    'users': `<svg ${attrs}><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
                    'building-2': `<svg ${attrs}><path d="M6 22V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v18"/><path d="M6 12H4a1 1 0 0 0-1 1v9"/><path d="M18 9h2a1 1 0 0 1 1 1v12"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>`,
                    'user-minus': `<svg ${attrs}><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="8"/></svg>`,
                    'calendar': `<svg ${attrs}><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,
                    'mail': `<svg ${attrs}><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>`,
                    'message-circle': `<svg ${attrs}><path d="M21 11.5a8.5 8.5 0 1 1-4.3-7.4L21 3v8.5z"/></svg>`,
                    'file-text': `<svg ${attrs}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>`,
                    'check': `<svg ${attrs}><polyline points="20 6 9 17 4 12"/></svg>`,
                    'x': `<svg ${attrs}><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
                    'shopping-bag': `<svg ${attrs}><path d="M6 2l1.5 4h9L18 2"/><path d="M3 6h18l-1.5 14h-15z"/></svg>`,
                    'clipboard': `<svg ${attrs}><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M9 4H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2"/></svg>`,
                    'map-pin': `<svg ${attrs}><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
                };

                return icons[iconName] || `<svg ${attrs}><circle cx="12" cy="12" r="10"/></svg>`;
            }
        }
    }
</script>
@endsection
