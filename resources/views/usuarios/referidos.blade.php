<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Empresas referidas</h1>
                <p class="text-sm text-slate-600">
                    {{ $user->codigo }} - {{ $user->name }}
                </p>
            </div>
            <a href="{{ route('usuarios.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Volver a usuarios
            </a>
        </div>
    </x-slot>

    <section class="mx-auto w-full max-w-6xl space-y-4 px-4 py-6 sm:px-6 lg:px-8">
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
                                <td class="px-4 py-3 font-semibold text-slate-900">${{ number_format($empresa->valor_total_referido, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('empresas.show', $empresa) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Ver</a>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900"

                                            @click="openDetalle = true"

                                            title="Ver detalle de valor"
                                            aria-label="Ver detalle de valor"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.983 5.5c.422-1.297 2.612-1.297 3.034 0a1.75 1.75 0 002.622 1.032c1.103-.637 2.652.913 2.015 2.016a1.75 1.75 0 001.032 2.622c1.297.422 1.297 2.612 0 3.034a1.75 1.75 0 00-1.032 2.622c.637 1.103-.912 2.653-2.015 2.016a1.75 1.75 0 00-2.622 1.032c-.422 1.297-2.612 1.297-3.034 0a1.75 1.75 0 00-2.622-1.032c-1.103.637-2.652-.913-2.015-2.016a1.75 1.75 0 00-1.032-2.622c-1.297-.422-1.297-2.612 0-3.034a1.75 1.75 0 001.032-2.622c-.637-1.103.912-2.653 2.015-2.016A1.75 1.75 0 0011.983 5.5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    </div>


                                    <div x-cloak x-show="openDetalle" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/40" @click="openDetalle = false"></div>

                                    <div
                                        x-cloak
                                        x-show="openDetalle"
                                        x-transition
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                        role="dialog"
                                        aria-modal="true"
                                        aria-labelledby="detalle-opciones-title-{{ $empresa->id }}"
                                    >
                                        <div class="w-full max-w-2xl rounded-lg bg-white shadow-xl" @click.stop>
                                            <div class="flex items-center justify-between rounded-t border-b p-4 md:p-5">
                                                <h3 id="detalle-opciones-title-{{ $empresa->id }}" class="text-lg font-semibold text-slate-900">Detalle de valor · {{ $empresa->nombre }}</h3>
                                                <button type="button" class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg text-sm text-slate-400 hover:bg-slate-100 hover:text-slate-900" @click="openDetalle = false">
                                                    <span class="sr-only">Cerrar modal</span>
                                                    <svg class="h-3 w-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="space-y-4 p-4 md:p-5">
                                                @if($empresa->opciones->isNotEmpty())
                                                    <div class="overflow-x-auto">
                                                        <table class="w-full text-left text-sm text-slate-700">
                                                            <thead class="bg-slate-50 text-slate-600">
                                                                <tr>
                                                                    <th class="px-3 py-2 font-semibold">Categoría</th>
                                                                    <th class="px-3 py-2 font-semibold">Nombre</th>
                                                                    <th class="px-3 py-2 font-semibold text-right">Valor</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-slate-100">
                                                                @foreach($empresa->opciones as $opcion)
                                                                    <tr>
                                                                        <td class="px-3 py-2">{{ $opcion->categoria }}</td>
                                                                        <td class="px-3 py-2">{{ $opcion->nombre }}</td>
                                                                        <td class="px-3 py-2 text-right">${{ number_format((float) $opcion->valor, 0, ',', '.') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="border-t border-slate-200">
                                                                <tr>
                                                                    <td colspan="2" class="px-3 py-2 text-right font-semibold text-slate-900">Total</td>
                                                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">${{ number_format($empresa->valor_total_referido, 0, ',', '.') }}</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-slate-500">Esta empresa no tiene opciones valorables.</p>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">No hay empresas referidas para este usuario.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $empresasReferidas->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
