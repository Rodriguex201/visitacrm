@extends('layouts.app')

@section('content')
    @php
        $valorVacio = '—';
        $tipoNombre = ucfirst((string) $usuario->tipo_usuario);
        $tipoColor = $tipos[$usuario->tipo_usuario] ?? null;
        $tipoBgColor = $tipoColor?->bg_color ?? '#E2E8F0';
        $tipoTextColor = $tipoColor?->text_color ?? '#334155';

        $campos = [
            'Código' => $usuario->codigo ?: $valorVacio,
            'Nombre' => $usuario->name ?: $valorVacio,
            'Teléfono' => $usuario->telefono ?: $valorVacio,
            'Dirección' => $usuario->direccion ?: $valorVacio,
            'Banco' => $usuario->banco?->nombre ?: $valorVacio,
            'Cuenta bancaria' => $usuario->cta_banco ?: $valorVacio,
            'Ciudad' => $usuario->ciudad ?: $valorVacio,
        ];
    @endphp

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Mi usuario</h1>
            <p class="mt-1 text-sm text-slate-600">Información de tu cuenta</p>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm md:p-6">
            <div class="mb-5 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" style="background-color: {{ $tipoBgColor }}; color: {{ $tipoTextColor }};">
                    {{ $tipoNombre ?: $valorVacio }}
                </span>

                @if ($usuario->tipo_usuario === 'administracion' && $usuario->usuarioDe)
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                        Usuario de: {{ $usuario->usuarioDe->codigo ?? $usuario->usuarioDe->name }}
                    </span>
                @endif

                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                    Referidos: {{ $usuario->empresas_referidas_count ?? 0 }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($campos as $label => $valor)
                    <article class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ filled($valor) ? $valor : $valorVacio }}</p>
                    </article>
                @endforeach

                <article class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo de usuario</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" style="background-color: {{ $tipoBgColor }}; color: {{ $tipoTextColor }};">
                            {{ $tipoNombre ?: $valorVacio }}
                        </span>
                    </div>
                </article>

                @if ($usuario->tipo_usuario === 'administracion')
                    <article class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario de</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ $usuario->usuarioDe?->codigo ? 'Usuario de: '.$usuario->usuarioDe->codigo : $valorVacio }}
                        </p>
                    </article>
                @endif

                <article class="rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cantidad de referidos</p>
                    <p class="mt-1 text-sm font-medium text-slate-900">{{ $usuario->empresas_referidas_count ?? 0 }}</p>
                </article>
            </div>
        </section>
    </div>
@endsection
