@extends('layouts.app')

@section('content')
    <section x-data="{ openModal: false }" x-init="if (@js($errors->any())) openModal = true" class="space-y-4">
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

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-xl bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm text-slate-700">
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
                        @forelse ($usuarios as $u)
                            <tr class="hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3">{{ $u->created_at?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $u->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $u->telefono ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $u->email }}</td>
                                <td class="whitespace-nowrap px-4 py-3 tracking-wider">********</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ ucfirst($u->tipo_usuario) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-500">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3">
                {{ $usuarios->links() }}
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

                @if ($errors->any())
                    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf

                    <div>
                        <label for="name" class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            placeholder="Nombre completo"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="telefono" class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
                        <input
                            id="telefono"
                            name="telefono"
                            type="text"
                            value="{{ old('telefono') }}"
                            placeholder="Ej: 3001234567"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block font-semibold text-slate-700">Correo *</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="usuario@correo.com"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block font-semibold text-slate-700">Clave *</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="tipo_usuario" class="mb-1.5 block font-semibold text-slate-700">Tipo de usuario *</label>
                        <select
                            id="tipo_usuario"
                            name="tipo_usuario"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                            <option value="">Selecciona una opción</option>
                            <option value="freelance" @selected(old('tipo_usuario') === 'freelance')>freelance</option>
                            <option value="vinculado" @selected(old('tipo_usuario') === 'vinculado')>vinculado</option>
                            <option value="administracion" @selected(old('tipo_usuario') === 'administracion')>administracion</option>
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
