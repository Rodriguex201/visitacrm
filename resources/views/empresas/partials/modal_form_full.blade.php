<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Nombre *</label>
    <input x-model="form.nombre" name="nombre" type="text" placeholder="Nombre de la empresa" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Nombre contacto *</label>
    <input x-model="form.contacto_nombre" name="contacto_nombre" type="text" placeholder="Nombre del contacto" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
</div>

<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <div class="relative">
        <label class="mb-1.5 block font-semibold text-slate-700">Ciudad *</label>
        <div class="flex items-center gap-2">
            <input
                x-model="form.ciudad"
                name="ciudad"
                type="text"
                placeholder="Ciudad"
                class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                @keydown.enter.prevent="searchCity()"
                @input="form.ciudad_codigo = ''"
                autocomplete="off"
                required
            >
            <button
                type="button"
                @click="searchCity()"
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50"
                aria-label="Buscar ciudad"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.6-5.15a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                </svg>
            </button>
        </div>
        <input type="hidden" name="ciudad_codigo" :value="form.ciudad_codigo">

        <div
            x-show="cityLoading"
            class="mt-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-500 shadow-sm"
            x-cloak
        >
            Buscando ciudades...
        </div>

        <div
            x-show="cityResults.length"
            class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-lg"
            x-cloak
        >
            <template x-for="city in cityResults" :key="city.citycodigo">
                <button
                    type="button"
                    @click="selectCity(city)"
                    class="flex w-full flex-col items-start gap-0.5 border-b border-slate-100 px-3 py-2 text-left last:border-b-0 hover:bg-slate-50"
                >
                    <span class="text-sm font-semibold text-slate-800" x-text="city.citynomb"></span>
                    <span class="text-xs text-slate-500" x-text="city.cityNdepto ?? city.citydepto"></span>
                </button>
            </template>
        </div>
    </div>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Sector</label>
    <select x-model="form.sector_id" name="sector_id" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        <option value="">Seleccionar sector</option>
        @foreach ($sectores as $sector)
            <option value="{{ $sector->id }}">{{ $sector->nombre }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Dirección</label>
    <input x-model="form.direccion" name="direccion" type="text" placeholder="Dirección" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
</div>

<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block font-semibold text-slate-700">Teléfono</label>
        <input x-model="form.telefono" name="telefono" type="text" placeholder="Teléfono" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
    </div>
    <div>
        <label class="mb-1.5 block font-semibold text-slate-700">Email</label>
        <input x-model="form.email" name="email" type="email" placeholder="Email" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
    </div>
</div>

<div>
    <label class="mb-1.5 block font-semibold text-slate-700">Notas</label>
    <textarea x-model="form.notas" name="notas" rows="3" placeholder="Notas de la empresa" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
</div>
