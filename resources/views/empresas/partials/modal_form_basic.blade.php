<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Nombre empresa *</label>
    <input x-model="form.nombre" name="nombre" type="text" placeholder="Nombre de la empresa" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Nombre contacto *</label>
    <input x-model="form.contacto_nombre" name="contacto_nombre" type="text" placeholder="Nombre del contacto" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Teléfono *</label>
    <input x-model="form.telefono" name="telefono" type="text" placeholder="Teléfono" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Dirección</label>
    <input x-model="form.direccion" name="direccion" type="text" placeholder="Dirección" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Sector</label>
    <select x-model="form.sector_id" @change="onSectorChange()" name="sector_id" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        <option value="">Seleccionar sector</option>
        @foreach ($sectores as $sector)
            <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
        @endforeach
    </select>
</div>

<div x-show="shouldShowSectorOtro()" x-cloak>
    <label class="mb-1.5 block font-semibold text-slate-700">Especifica el sector</label>
    <input
        x-model="form.sector_otro"
        :required="shouldShowSectorOtro()"
        name="sector_otro"
        type="text"
        maxlength="150"
        placeholder="Especifica el sector"
        class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
    >
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Notas</label>
    <textarea x-model="form.notas" name="notas" rows="3" placeholder="Notas de la empresa" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
</div>
