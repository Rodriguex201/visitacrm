<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Empresas
            </h2>
            <a href="{{ route('empresas.create') }}"
               class="px-4 py-2 bg-black text-white rounded">
                Nueva Empresa
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border text-left">Nombre</th>
                            <th class="p-2 border text-left">NIT</th>
                            <th class="p-2 border text-left">Ciudad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $e)
                            <tr>
                                <td class="p-2 border">{{ $e->nombre }}</td>
                                <td class="p-2 border">{{ $e->nit }}</td>
                                <td class="p-2 border">{{ $e->ciudad }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-500">
                                    No hay empresas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
