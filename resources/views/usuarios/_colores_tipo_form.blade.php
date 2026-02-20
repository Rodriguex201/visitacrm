@if ($errors->updateTipoUsuario->any())
    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->updateTipoUsuario->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-3 md:grid-cols-3">
    @foreach ($tiposLista as $tipo)
        <form method="POST" action="{{ route('usuarios.tipos.update', $tipo) }}" class="rounded-lg border border-slate-200 p-3">
            @csrf
            @method('PUT')

            <p class="text-sm font-semibold text-slate-800">{{ ucfirst($tipo->nombre) }}</p>

            <div class="mt-3 space-y-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Color de fondo</label>
                    <input type="color" name="bg_color" value="{{ old('bg_color', $tipo->bg_color ?? '#E5E7EB') }}" class="h-10 w-full cursor-pointer rounded-lg border border-slate-200 bg-white p-1">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Color de texto</label>
                    <input type="color" name="text_color" value="{{ old('text_color', $tipo->text_color ?? '#374151') }}" class="h-10 w-full cursor-pointer rounded-lg border border-slate-200 bg-white p-1">
                </div>

                <button type="submit" class="inline-flex h-9 w-full items-center justify-center rounded-lg bg-blue-600 text-xs font-semibold text-white transition hover:bg-blue-700">
                    Guardar colores
                </button>
            </div>
        </form>
    @endforeach
</div>
