@extends('layouts.app')

@section('content')
    @php
        $usuarios = [
            [
                'fecha_ingreso' => '20/01/2026',
                'nombre' => 'Ana María López',
                'telefono' => '3104567890',
                'correo' => 'ana.lopez@visitacrm.com',
                'clave' => 'Ana12345',
                'tipo_usuario' => 'freelance',
            ],
            [
                'fecha_ingreso' => '11/02/2026',
                'nombre' => 'Carlos Gómez',
                'telefono' => '3209876543',
                'correo' => 'carlos.gomez@visitacrm.com',
                'clave' => 'Car!2026',
                'tipo_usuario' => 'vinculado',
            ],
            [
                'fecha_ingreso' => '05/03/2026',
                'nombre' => 'Luisa Fernández',
                'telefono' => '3001122334',
                'correo' => 'luisa.fernandez@visitacrm.com',
                'clave' => 'Lui$Pass9',
                'tipo_usuario' => 'administracion',
            ],
        ];
    @endphp

    <section x-data="{ openModal: false }" class="space-y-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-950">Usuarios</h1>
                <p class="mt-1 text-sm text-slate-600">Administra los usuarios registrados</p>
            </div>

            <button
                type="button"
                @click="openModal = true"
                class="inline-flex h-10 items-center rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                Crear usuario
            </button>
        </div>

        <div class="rounded-xl border border-slate-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[820px] w-full text-left text-sm text-slate-700">
                    <thead class="bg-gray-50 text-slate-600">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Fecha de ingreso</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Nombre</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Teléfono</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Correo</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Clave</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Tipo de usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($usuarios as $usuario)
                            <tr class="hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3">{{ $usuario['fecha_ingreso'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $usuario['nombre'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $usuario['telefono'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $usuario['correo'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 tracking-wider">••••••••</td>
                                <td class="whitespace-nowrap px-4 py-3 capitalize">{{ $usuario['tipo_usuario'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div
            x-show="openModal"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="openModal = false"
        ></div>

        <div
            x-show="openModal"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Crear usuario</h2>
                    <button
                        type="button"
                        @click="openModal = false"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="openModal = false" class="space-y-3 text-sm">
                    <div>
                        <label for="nombre" class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
                        <input
                            id="nombre"
                            type="text"
                            placeholder="Nombre completo"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="telefono" class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
                        <input
                            id="telefono"
                            type="text"
                            placeholder="Ej: 3001234567"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>
                        <label for="correo" class="mb-1.5 block font-semibold text-slate-700">Correo *</label>
                        <input
                            id="correo"
                            type="email"
                            placeholder="usuario@correo.com"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="clave" class="mb-1.5 block font-semibold text-slate-700">Clave *</label>
                        <div class="relative">
                            <input
                                id="clave"
                                type="password"
                                placeholder="••••••••"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 pr-10 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                required
                            >
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label for="tipo_usuario" class="mb-1.5 block font-semibold text-slate-700">Tipo de usuario *</label>
                        <select
                            id="tipo_usuario"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                            <option value="">Selecciona una opción</option>
                            <option value="freelance">freelance</option>
                            <option value="vinculado">vinculado</option>
                            <option value="administracion">administracion</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="mt-1 inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Guardar usuario
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
