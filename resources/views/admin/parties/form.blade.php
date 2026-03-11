@php $isEdit = isset($party); @endphp

{{-- Nombre --}}
<div class="space-y-1.5">
    <label class="text-zinc-300 text-sm font-medium">Nombre de la fiesta *</label>
    <input
        type="text"
        name="name"
        value="{{ old('name', $party->name ?? '') }}"
        placeholder="Ej: Nochevieja 2025"
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
    />
    @error('name') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
</div>

{{-- Descripción --}}
<div class="space-y-1.5">
    <label class="text-zinc-300 text-sm font-medium">Descripción</label>
    <textarea
        name="description"
        rows="3"
        placeholder="Cuéntale a los asistentes de qué va la noche..."
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors resize-none text-sm"
    >{{ old('description', $party->description ?? '') }}</textarea>
    @error('description') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
</div>

{{-- Ubicación --}}
<div class="space-y-1.5">
    <label class="text-zinc-300 text-sm font-medium">Ubicación</label>
    <input
        type="text"
        name="location"
        value="{{ old('location', $party->location ?? '') }}"
        placeholder="Ej: Sala Apolo, Barcelona"
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm"
    />
    @error('location') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
</div>

{{-- Fechas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="space-y-1.5">
        <label class="text-zinc-300 text-sm font-medium">Inicio del swipe *</label>
        <input
            type="datetime-local"
            name="starts_at"
            value="{{ old('starts_at', isset($party) ? $party->starts_at->format('Y-m-d\TH:i') : '') }}"
            class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm [color-scheme:dark]"
        />
        @error('starts_at') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-1.5">
        <label class="text-zinc-300 text-sm font-medium">Apertura de registro</label>
        <input
            type="datetime-local"
            name="registration_opens_at"
            value="{{ old('registration_opens_at', isset($party) && $party->registration_opens_at ? $party->registration_opens_at->format('Y-m-d\TH:i') : '') }}"
            class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-vicio-400 focus:ring-1 focus:ring-vicio-400 transition-colors text-sm [color-scheme:dark]"
        />
        @error('registration_opens_at') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Cover image --}}
<div class="space-y-1.5">
    <label class="text-zinc-300 text-sm font-medium">Imagen de portada</label>
    @if($isEdit && $party->cover_image)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $party->cover_image) }}" class="h-24 rounded-xl object-cover" />
            <p class="text-zinc-600 text-xs mt-1">Sube una nueva para reemplazarla</p>
        </div>
    @endif
    <input
        type="file"
        name="cover_image"
        accept="image/*"
        class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-zinc-400 focus:outline-none focus:border-vicio-400 transition-colors text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-zinc-700 file:text-zinc-300 file:text-xs"
    />
    @error('cover_image') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
</div>