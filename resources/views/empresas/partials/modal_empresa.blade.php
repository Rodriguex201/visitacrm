<div
    x-show="openModal"
    x-transition.opacity
    class="fixed inset-0 z-40 bg-slate-900/45"
    @click="closeModal()"
    x-cloak
></div>

<div
    x-show="openModal"
    x-transition
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl" @click.stop>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-slate-900" x-text="openEdit ? 'Editar Empresa' : 'Nueva Empresa'"></h2>
            <button type="button" @click="closeModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6l-12 12" />
                </svg>
            </button>
        </div>

        @if (($showModalErrors ?? $errors->any()))
            <div class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form :action="formAction" method="POST" class="space-y-3 text-sm">
            @csrf
            <input type="hidden" name="modal_mode" :value="openEdit ? 'edit' : 'create'">
            <input type="hidden" name="empresa_id" :value="editId">
            <template x-if="openEdit">
                <input type="hidden" name="_method" value="PUT">
            </template>

            @if ($esAdministracion)
                @include('empresas.partials.modal_form_full')
            @else
                @include('empresas.partials.modal_form_basic')
            @endif

            <button type="submit" class="mt-1 inline-flex h-10 w-full items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700" x-text="openEdit ? 'Guardar cambios' : 'Crear Empresa'"></button>
        </form>
    </div>
</div>
