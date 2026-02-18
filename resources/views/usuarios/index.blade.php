@extends('layouts.app')

@section('content')

    <section
        x-data="{
            openCreateModal: false,
            openEditModal: false,
            editingUser: { id: null, name: '', telefono: '', email: '', tipo_usuario: 'freelance', password: '' },
            startEdit(user) {
                this.editingUser = {
                    id: user.id,
                    name: user.name ?? '',
                    telefono: user.telefono ?? '',
                    email: user.email ?? '',
                    tipo_usuario: user.tipo_usuario ?? 'freelance',
                    password: '',
                };
                this.openEditModal = true;
            }
        }"
        x-init="
            if (@js($errors->createUser->any())) {
                openCreateModal = true;
            }

            if (@js($errors->updateUser->any())) {
                editingUser = {
                    id: @js(old('edit_id')),
                    name: @js(old('name', '')),
                    telefono: @js(old('telefono', '')),
                    email: @js(old('email', '')),
                    tipo_usuario: @js(old('tipo_usuario', 'freelance')),
                    password: '',
                };
                openEditModal = true;
            }
        "
        class="space-y-4"
    >

        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-950">Usuarios</h1>
                <p class="mt-1 text-sm text-slate-600">Administra los usuarios registrados</p>
            </div>

            <button
                type="button"
                @click="openCreateModal = true"
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

                <table class="w-full min-w-[920px] text-left text-sm text-slate-700">

                    <thead class="bg-gray-50 text-slate-600">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Fecha de ingreso</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Nombre</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Teléfono</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Correo</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Clave</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Tipo de usuario</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Acciones</th>
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

                                <td class="whitespace-nowrap px-4 py-3">
<button
    type="button"
    @click="startEdit(@js([
        'id' => $u->id,
        'name' => $u->name,
        'telefono' => $u->telefono,
        'email' => $u->email,
        'tipo_usuario' => $u->tipo_usuario,
    ]))"
    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-blue-600"
    title="Editar usuario"
>
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
    </svg>
</button>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-500">No hay usuarios registrados.</td>
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
            x-show="openCreateModal"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="openCreateModal = false"
        ></div>

        <div
            x-show="openCreateModal"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Crear usuario</h2>
                    <button
                        type="button"
                        @click="openCreateModal = false"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>


                @if ($errors->createUser->any())
                    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->createUser->all() as $error)

                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf

                    <div>

                        <label for="create_name" class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
                        <input
                            id="create_name"

                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            placeholder="Nombre completo"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="create_telefono" class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
                        <input

                            id="create_telefono"

                            name="telefono"
                            type="text"
                            value="{{ old('telefono') }}"
                            placeholder="Ej: 3001234567"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>

                        <label for="create_email" class="mb-1.5 block font-semibold text-slate-700">Correo *</label>
                        <input
                            id="create_email"

                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="usuario@correo.com"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>

                        <label for="create_password" class="mb-1.5 block font-semibold text-slate-700">Clave *</label>
                        <input
                            id="create_password"

                            name="password"
                            type="password"
                            placeholder="••••••••"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="create_tipo_usuario" class="mb-1.5 block font-semibold text-slate-700">Tipo de usuario *</label>
                        <select

                            id="create_tipo_usuario"

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

        <div
            x-show="openEditModal"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-40 bg-slate-900/45"
            @click="openEditModal = false"
        ></div>

        <div
            x-show="openEditModal"
            x-transition
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl" @click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Editar usuario</h2>
                    <button
                        type="button"
                        @click="openEditModal = false"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>

                @if ($errors->updateUser->any())
                    <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->updateUser->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form :action="`${@js(url('/usuarios'))}/${editingUser.id}`" method="POST" class="space-y-3 text-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" :value="editingUser.id">

                    <div>
                        <label for="edit_name" class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
                        <input
                            id="edit_name"
                            name="name"
                            type="text"
                            x-model="editingUser.name"
                            placeholder="Nombre completo"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="edit_telefono" class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
                        <input
                            id="edit_telefono"
                            name="telefono"
                            type="text"
                            x-model="editingUser.telefono"
                            placeholder="Ej: 3001234567"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>
                        <label for="edit_email" class="mb-1.5 block font-semibold text-slate-700">Correo *</label>
                        <input
                            id="edit_email"
                            name="email"
                            type="email"
                            x-model="editingUser.email"
                            placeholder="usuario@correo.com"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                    </div>

                    <div>
                        <label for="edit_password" class="mb-1.5 block font-semibold text-slate-700">Clave (opcional)</label>
                        <input
                            id="edit_password"
                            name="password"
                            type="password"
                            placeholder="Dejar vacío para mantener la clave actual"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>
                        <label for="edit_tipo_usuario" class="mb-1.5 block font-semibold text-slate-700">Tipo de usuario *</label>
                        <select
                            id="edit_tipo_usuario"
                            name="tipo_usuario"
                            x-model="editingUser.tipo_usuario"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            required
                        >
                            <option value="freelance">freelance</option>
                            <option value="vinculado">vinculado</option>
                            <option value="administracion">administracion</option>

                        </select>
                    </div>

                    <button
                        type="submit"
                        class="mt-1 inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Actualizar usuario
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
