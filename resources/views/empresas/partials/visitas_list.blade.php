@php
    use App\Support\VisitaCatalogos;

    $soloLectura = $soloLectura ?? ((auth()->user()?->tipo_usuario ?? null) !== 'administracion');

@endphp

<div class="space-y-3" data-visitas-count="{{ $visitas->count() }}">
    @if ($visitas->isEmpty())
        <p class="text-center text-sm text-slate-500">Sin visitas registradas</p>
    @else
        @foreach ($visitas as $visita)
            @php
                $isProgramada = $visita->fecha_hora?->isFuture();
                $canUpdateResultado = ! $soloLectura && ! $isProgramada && empty($visita->resultado);
                $estadoBadgeClass = VisitaCatalogos::estadoBadgeClass($visita->estado);
                $resultadoBadgeClass = VisitaCatalogos::resultadoBadgeClass($visita->resultado);
            @endphp

            <article id="visita-item-{{ $visita->id }}" class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm" data-visita-id="{{ $visita->id }}">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-slate-900">{{ $visita->fecha_hora?->format('d/m/Y H:i') }}</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoBadgeClass }}">{{ VisitaCatalogos::estadoLabel($visita->estado) ?? $visita->estado }}</span>
                        @if ($isProgramada)
                            <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">Programada</span>
                        @endif
                    </div>
                </div>

                <div class="mt-3 space-y-2">
                    <div id="resultado-container-{{ $visita->id }}">
                        @if ($visita->resultado)
                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="font-medium text-slate-700">Resultado:</span>
                                <span id="resultado-badge-{{ $visita->id }}" class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $resultadoBadgeClass }}">{{ VisitaCatalogos::resultadoLabel($visita->resultado) ?? $visita->resultado }}</span>
                                <span id="nivel-interes-text-{{ $visita->id }}" class="text-slate-600">
                                    @if ($visita->nivel_interes)
                                        Nivel de interés: {{ VisitaCatalogos::nivelInteresLabel($visita->nivel_interes) ?? $visita->nivel_interes }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <button
                        id="btn-actualizar-{{ $visita->id }}"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        data-open-resultado="true"
                        data-visita-id="{{ $visita->id }}"
                        @if (! $canUpdateResultado) style="display: none;" @endif
                    >
                        Actualizar resultado
                    </button>
                </div>

                @if ($visita->notas)
                    <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($visita->notas, 180) }}</p>
                @endif
            </article>
        @endforeach
    @endif
</div>

