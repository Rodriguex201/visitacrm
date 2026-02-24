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
                            <th class="px-4 py-3 font-semibold">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($empresasReferidas as $empresa)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3">{{ $empresa->nombre }}</td>
                                <td class="px-4 py-3">{{ $empresa->ciudad ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $empresa->telefono ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $empresa->sector?->nombre ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($empresa->referida_at)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('empresas.show', $empresa) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">No hay empresas referidas para este usuario.</td>
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
