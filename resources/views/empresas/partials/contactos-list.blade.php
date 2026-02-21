@if ($contactos->isEmpty())
    <p class="text-center text-sm text-slate-500">Sin contactos registrados</p>
@else
    <div class="space-y-3">
        @foreach ($contactos as $contacto)
            <article class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900">{{ $contacto->nombre }}</p>
                        @if ($contacto->cargo)
                            <p class="text-sm text-slate-600">{{ $contacto->cargo }}</p>
                        @endif
                    </div>

                    @if ($contacto->es_principal)
                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700">Principal</span>
                    @endif
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
