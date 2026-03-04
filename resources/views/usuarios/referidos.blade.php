<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Empresas referidas</h1>
        </div>
    </x-slot>

    <section class="mx-auto w-full max-w-6xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">

        {{-- BARRA SUPERIOR COMPACTA --}}
        <div class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Referidos de usuario</p>
                <p class="truncate text-base font-bold text-slate-900">
                    {{ $user->name }}
                    <span class="font-medium text-slate-600">({{ $user->codigo ?: 'S/C' }})</span>
                </p>
            </div>

            <a href="{{ route('usuarios.index') }}"
               class="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver a usuarios
            </a>
        </div>

        {{-- TABLA DE REFERIDOS --}}

        <div class="rounded-xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm text-slate-700">
                    <thead class="bg-gray-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Empresa</th>
                            <th class="px-4 py-3 font-semibold">Ciudad</th>
                            <th class="px-4 py-3 font-semibold">Teléfono</th>
                            <th class="px-4 py-3 font-semibold">Sector</th>
                            <th class="px-4 py-3 font-semibold">Referida</th>
                            <th class="px-4 py-3 font-semibold">Valor total</th>
                            <th class="px-4 py-3 font-semibold">Detalle</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($empresasReferidas as $empresa)
                            <tr class="hover:bg-slate-50/70" x-data="{ openDetalle: false }" @keydown.escape.window="openDetalle = false">
                                <td class="px-4 py-3">{{ $empresa->nombre }}</td>
                                <td class="px-4 py-3">{{ $empresa->ciudad ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $empresa->telefono ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $empresa->sector?->nombre ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($empresa->referida_at)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    ${{ number_format($empresa->valor_total_referido, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('empresas.show', $empresa) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                            Ver
                                        </a>

                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                            @click="openDetalle = true"
                                            title="Ver detalle de valor"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.983 5.5c.422-1.297 2.612-1.297 3.034 0a1.75 1.75 0 002.622 1.032c1.103-.637 2.652.913 2.015 2.016a1.75 1.75 0 001.032 2.622c1.297.422 1.297 2.612 0 3.034a1.75 1.75 0 00-1.032 2.622c.637 1.103-.912 2.653-2.015 2.016a1.75 1.75 0 00-2.622 1.032c-.422 1.297-2.612 1.297-3.034 0a1.75 1.75 0 00-2.622-1.032c-1.103.637-2.652-.913-2.015-2.016a1.75 1.75 0 00-1.032-2.622c-1.297-.422-1.297-2.612 0-3.034a1.75 1.75 0 001.032-2.622c-.637-1.103.912-2.653 2.015-2.016A1.75 1.75 0 0011.983 5.5z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- MODAL --}}
                                    <div x-cloak x-show="openDetalle" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40" @click="openDetalle = false"></div>

                                    <div x-cloak x-show="openDetalle" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl" @click.stop>

                                            <div class="flex items-center justify-between border-b p-4">
                                                <h3 class="text-lg font-semibold text-slate-900">
                                                    Detalle de valor · {{ $empresa->nombre }}
                                                </h3>

                                                <button type="button" class="text-slate-400 hover:text-slate-900" @click="openDetalle = false">
                                                    ✕
                                                </button>
                                            </div>

                                            <div class="p-4">
                                                @if($empresa->opciones->isNotEmpty())
                                                    <table class="w-full text-sm">
                                                        <thead class="bg-slate-50 text-slate-600">
                                                            <tr>
                                                                <th class="px-3 py-2">Categoría</th>
                                                                <th class="px-3 py-2">Nombre</th>
                                                                <th class="px-3 py-2 text-right">Valor</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody class="divide-y">
                                                            @foreach($empresa->opciones as $opcion)
                                                                <tr>
                                                                    <td class="px-3 py-2">{{ $opcion->categoria }}</td>
                                                                    <td class="px-3 py-2">{{ $opcion->nombre }}</td>
                                                                    <td class="px-3 py-2 text-right">
                                                                        ${{ number_format($opcion->valorParaUsuario($user),0,',','.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>

                                                        <tfoot class="border-t">
                                                            <tr>
                                                                <td colspan="2" class="px-3 py-2 text-right font-semibold">Total</td>
                                                                <td class="px-3 py-2 text-right font-semibold">
                                                                    ${{ number_format($empresa->valor_total_referido,0,',','.') }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                @else
                                                    <p class="text-sm text-slate-500">
                                                        Esta empresa no tiene opciones valorables.
                                                    </p>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">
                                    No hay empresas referidas para este usuario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($empresasReferidas->isNotEmpty())
                        <tfoot class="border-t border-slate-200 bg-slate-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-semibold">Valor total</td>
                                <td class="px-4 py-3 font-bold">
                                    ${{ number_format($valorTotalPagina,0,',','.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3">
                {{ $empresasReferidas->links() }}
            </div>

        </div>
    </section>
</x-app-layout>
