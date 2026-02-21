@if ($contactos->isEmpty())
    <p class="text-center text-sm text-slate-500">Sin contactos registrados</p>
@else
    <div class="space-y-3">
        @foreach ($contactos as $contacto)
            <article class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">

                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-900">{{ $contacto->nombre }}</p>
                            @if ($contacto->es_principal)
                                <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Principal</span>
                            @endif
                        </div>


                        @if ($contacto->cargo)
                            <p class="text-sm text-slate-600">{{ $contacto->cargo }}</p>
                        @endif
                    </div>

                    <button
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800"
                        title="Editar contacto"
                        aria-label="Editar contacto"
                        data-contacto-edit
                        data-contacto-id="{{ $contacto->id }}"
                        data-contacto-nombre="{{ $contacto->nombre }}"
                        data-contacto-cargo="{{ $contacto->cargo ?? '' }}"
                        data-contacto-telefono="{{ $contacto->telefono ?? '' }}"
                        data-contacto-email="{{ $contacto->email ?? '' }}"
                        data-contacto-principal="{{ $contacto->es_principal ? '1' : '0' }}"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.1 2.1 0 113 2.97L7.5 18.82l-4.5 1.18 1.18-4.5 12.682-12.013z" />
                        </svg>
                    </button>

                </div>

                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    @if ($contacto->telefono)
                        <p>Tel: {{ $contacto->telefono }}</p>
                    @endif

                    @if ($contacto->email)
                        <p>
                            Email:
                            <a href="mailto:{{ $contacto->email }}" class="text-blue-600 hover:underline">{{ $contacto->email }}</a>
                        </p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
