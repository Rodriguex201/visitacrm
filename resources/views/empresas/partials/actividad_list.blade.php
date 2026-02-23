<div class="space-y-3" data-actividad-count="{{ $acciones->count() }}">
    @if ($acciones->isEmpty())
        <p class="text-sm text-slate-600">Sin actividad aún</p>
    @else
        @foreach ($acciones as $item)
            <div class="rounded-xl border border-slate-100 p-3">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700">•</span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">{{ $item->accion?->nombre ?? 'Acción' }}</p>
                        <p class="text-xs text-slate-500">Acción · {{ $item->created_at?->format('d/m/Y H:i') }}</p>
                        @if ($item->nota)
                            <p class="mt-1 text-sm text-slate-700">{{ $item->nota }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
