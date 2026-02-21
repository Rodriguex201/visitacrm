@if ($contactos->isEmpty())
    <p class="text-center text-sm text-slate-500">Sin contactos registrados</p>
@else
    <div class="space-y-2" x-data="{ openId: null }">
        @foreach ($contactos as $contacto)
            <article class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                    <button
                        type="button"
                        class="flex min-w-0 flex-1 items-center gap-2 text-left"
                        :aria-expanded="openId === {{ $contacto->id }}"
                        @click="openId = (openId === {{ $contacto->id }} ? null : {{ $contacto->id }})"
                    >
                        <svg class="h-4 w-4 shrink-0 text-slate-400 transition" :class="openId === {{ $contacto->id }} ? 'rotate-90' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6.22 3.22a.75.75 0 011.06 0l6.25 6.25a.75.75 0 010 1.06l-6.25 6.25a.75.75 0 11-1.06-1.06L11.94 10 6.22 4.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                        </svg>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $contacto->nombre }}</p>
                                @if ($contacto->es_principal)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700">Principal</span>
                                @endif
                            </div>

                            @if ($contacto->cargo)
                                <p class="truncate text-xs text-slate-500">{{ $contacto->cargo }}</p>
                            @endif
                        </div>
                    </button>

                    <button
                        type="button"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                        title="Editar contacto"
                        aria-label="Editar contacto"
                        @click.stop="abrirModalEditarContacto({
                            id: '{{ $contacto->id }}',
                            nombre: @js($contacto->nombre),
                            cargo: @js($contacto->cargo),
                            telefono: @js($contacto->telefono),
                            email: @js($contacto->email),
                            es_principal: '{{ $contacto->es_principal ? '1' : '0' }}',
                        })"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 2.97L7.5 18.82l-4.5 1.18 1.18-4.5 12.682-12.013z" />
                        </svg>
                    </button>
                </div>

                <div x-cloak x-show="openId === {{ $contacto->id }}" x-transition class="border-t border-slate-100 bg-slate-50/70 px-4 py-3">
                    <div class="space-y-2 text-sm text-slate-600">
                        <p class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h3.214a2.25 2.25 0 012.121 1.497l1.02 3.059a2.25 2.25 0 01-.518 2.314l-1.44 1.44a12.042 12.042 0 005.853 5.853l1.44-1.44a2.25 2.25 0 012.314-.518l3.06 1.02a2.25 2.25 0 011.496 2.121V19.5a2.25 2.25 0 01-2.25 2.25h-.75C9.007 21.75 2.25 14.993 2.25 6.75z" />
                            </svg>
                            <span>Teléfono: {{ $contacto->telefono ?: 'Sin teléfono' }}</span>
                        </p>

                        <p class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9 6 9-6" />
                            </svg>
                            <span>
                                Email:
                                @if ($contacto->email)
                                    <a href="mailto:{{ $contacto->email }}" class="text-blue-600 hover:underline">{{ $contacto->email }}</a>
                                @else
                                    Sin email
                                @endif
                            </span>
                        </p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endif
