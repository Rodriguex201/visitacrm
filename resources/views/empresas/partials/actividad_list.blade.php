@php
    $buildIcon = static function (string $icono, ?string $color = null): string {
        $stroke = $color ?: 'currentColor';
        $attrs = 'width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="' . e($stroke) . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

        $icons = [
            'phone' => '<svg ' . $attrs . '><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.35 1.78.68 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.25a2 2 0 0 1 2.11-.45c.84.33 1.72.56 2.62.68A2 2 0 0 1 22 16.92z"/></svg>',
            'share-2' => '<svg ' . $attrs . '><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
            'video' => '<svg ' . $attrs . '><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
            'map-pin' => '<svg ' . $attrs . '><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            'building-2' => '<svg ' . $attrs . '><path d="M6 22V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v18"/><path d="M6 12H4a1 1 0 0 0-1 1v9"/><path d="M18 9h2a1 1 0 0 1 1 1v12"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
            'user-x' => '<svg ' . $attrs . '><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>',
            'calendar' => '<svg ' . $attrs . '><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            'circle' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="10"/></svg>',
        ];

        return $icons[$icono] ?? $icons['circle'];
    };
@endphp

<div class="space-y-3" data-actividad-count="{{ $acciones->count() }}">
    @if ($acciones->isEmpty())
        <p class="text-sm text-slate-600">Sin actividad aún</p>
    @else
        @foreach ($acciones as $item)
            @php
                $notaInicial = (string) ($item->nota ?? '');
                $isEmpresaOpcion = isset($item->opcion_id);
            @endphp
            <div class="rounded-xl border border-slate-100 p-3" data-actividad-item="{{ (int) $item->id }}" data-accion-id="{{ (int) ($item->accion_id ?? 0) }}"
                x-data="{ editingNota:false, draftNota:@js($notaInicial), nota:@js($notaInicial), savingNota:false, notaFlash:'', notaFlashType:'ok', async guardarNota(url){ if(!url){ return; } this.savingNota=true; this.notaFlash=''; try{ const response = await fetch(url,{ method:'PATCH', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}', Accept:'application/json' }, body: JSON.stringify({ nota:this.draftNota }) }); const data = await response.json(); if(!response.ok){ throw new Error(data.message || 'No se pudo guardar la nota.'); } this.nota = data.nota || ''; this.draftNota = this.nota; this.editingNota = false; this.notaFlashType='ok'; this.notaFlash = data.message || 'Nota guardada'; } catch(error){ this.notaFlashType='error'; this.notaFlash = error.message || 'No se pudo guardar la nota.'; } finally { this.savingNota=false; setTimeout(()=>{ this.notaFlash=''; }, 2500); } } }">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span data-role="actividad-icon" class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-700">{!! $buildIcon($item->accion?->icono ?? 'circle', $item->accion?->color) !!}</span>

                        <div class="min-w-0">
                            <p data-role="actividad-nombre" class="text-sm font-semibold text-slate-900">{{ $item->accion?->nombre ?? 'Acción' }}</p>
                            <p class="text-xs text-slate-500">Acción · {{ $item->created_at?->format('d/m/Y H:i') }}</p>

                            <div class="mt-1">
                                <template x-if="!editingNota">
                                    <div>
                                        <p x-show="nota" class="text-xs text-slate-500" x-text="nota"></p>
                                        @if ($isEmpresaOpcion && (auth()->user()?->tipo_usuario ?? null) === 'administracion')
                                            <button x-show="!nota" type="button" @click="editingNota = true"
                                                class="text-xs font-semibold text-blue-600 hover:text-blue-700">Agregar nota</button>
                                        @endif
                                    </div>
                                </template>

                                @if ($isEmpresaOpcion && (auth()->user()?->tipo_usuario ?? null) === 'administracion')
                                    <template x-if="editingNota">
                                        <div class="space-y-2">
                                            <textarea x-model="draftNota" rows="2" maxlength="2000"
                                                class="w-full rounded-md border-slate-300 text-xs text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Escribe una nota..."></textarea>
                                            <div class="flex items-center gap-2">
                                                <button type="button" :disabled="savingNota"
                                                    @click="guardarNota('{{ route('empresa-opcion.nota', ['empresaOpcion' => $item->id]) }}')"
                                                    class="inline-flex items-center rounded-md bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                                                    Guardar
                                                </button>
                                                <button type="button" :disabled="savingNota"
                                                    @click="editingNota = false; draftNota = nota"
                                                    class="inline-flex items-center rounded-md border border-slate-200 px-2 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-60">
                                                    Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                @endif

                                <p x-show="notaFlash" x-text="notaFlash" class="mt-1 text-xs"
                                    :class="notaFlashType === 'ok' ? 'text-emerald-600' : 'text-rose-600'"></p>
                            </div>
                        </div>
                    </div>

                    @if ((auth()->user()?->tipo_usuario ?? null) === 'administracion')
                        <div class="flex items-center gap-2">
                            @if ($isEmpresaOpcion)
                                <button
                                    type="button"
                                    class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                    @click="editingNota = true"
                                    title="Editar nota"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.121 2.121 0 113 3L8.25 19.1l-4.5 1.5 1.5-4.5 11.612-11.613z" />
                                    </svg>
                                </button>
                            @endif

                            <button
                                type="button"
                                class="rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                                data-accion-edit="true"
                                data-edit-actividad="1"
                                data-empresa-accion-id="{{ (int) $item->id }}"
                                data-actividad-id="{{ (int) $item->id }}"
                                data-accion-id="{{ (int) ($item->accion_id ?? 0) }}"
                                data-nombre="{{ $item->accion?->nombre ?? 'Acción' }}"
                                title="Editar acción"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6M9 12h6M9 17h6" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                class="rounded-md p-1.5 text-rose-500 transition hover:bg-rose-50 hover:text-rose-700"
                                data-accion-delete="true"
                                data-empresa-accion-id="{{ (int) $item->id }}"
                                title="Eliminar acción"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a1 1 0 011-1h6a1 1 0 011 1v2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a1 1 0 01-1 1H7a1 1 0 01-1-1L5 6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
