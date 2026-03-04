@if ($errors->updateEstadoReferidoColor->any())
    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->updateEstadoReferidoColor->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-3 md:grid-cols-3">
    @foreach ($estadosReferidoColores as $estado => $config)
        <form method="POST" action="{{ route('configuracion.estado-referido-colores.update') }}" class="rounded-lg border border-slate-200 p-3">
            @csrf

            <input type="hidden" name="estado" value="{{ $estado }}">

            <p class="text-sm font-semibold text-slate-800">{{ $config['label'] }}</p>

            <div class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                 style="background-color: {{ $config['bg_color'] }}; color: {{ $config['text_color'] }};">
                Estado: {{ $config['label'] }}
            </div>

            <div class="mt-3 space-y-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Color de fondo</label>
                    <input type="color" name="bg_color" value="{{ old('estado') === $estado ? old('bg_color', $config['bg_color']) : $config['bg_color'] }}" class="h-10 w-full cursor-pointer rounded-lg border border-slate-200 bg-white p-1" required>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Color de texto</label>
                    <input type="color" name="text_color" value="{{ old('estado') === $estado ? old('text_color', $config['text_color']) : $config['text_color'] }}" class="h-10 w-full cursor-pointer rounded-lg border border-slate-200 bg-white p-1" required>
                </div>

                <button type="submit" class="inline-flex h-9 w-full items-center justify-center rounded-lg bg-blue-600 text-xs font-semibold text-white transition hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>
    @endforeach
</div>
