@php $isEdit = isset($party); @endphp

{{-- Estilos reutilizables para los inputs --}}
@php
$inputStyle = 'background-color: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 14px 16px; color: white; font-size: 16px; width: 100%; outline: none; transition: border-color 0.2s;';
$labelStyle = 'color: rgba(255,255,255,0.85); font-size: 13px; font-weight: 600;';
$errorStyle = 'color: #fca5a5; font-size: 12px; margin-top: 4px;';
@endphp

{{-- Nombre --}}
<div class="space-y-1.5">
    <label style="{{ $labelStyle }}">Nombre de la fiesta *</label>
    <input
        type="text"
        name="name"
        value="{{ old('name', $party->name ?? '') }}"
        placeholder="Ej: Nochevieja 2025"
        style="{{ $inputStyle }}"
        onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
        onblur="this.style.borderColor='rgba(255,255,255,0.25)'"
    />
    @error('name') <p style="{{ $errorStyle }}">{{ $message }}</p> @enderror
</div>

{{-- Descripción --}}
<div class="space-y-1.5">
    <label style="{{ $labelStyle }}">Descripción</label>
    <textarea
        name="description"
        rows="3"
        placeholder="Cuéntale a los asistentes de qué va la noche..."
        style="{{ $inputStyle }} resize: none;"
        onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
        onblur="this.style.borderColor='rgba(255,255,255,0.25)'"
    >{{ old('description', $party->description ?? '') }}</textarea>
    @error('description') <p style="{{ $errorStyle }}">{{ $message }}</p> @enderror
</div>

{{-- Ubicación --}}
<div class="space-y-1.5">
    <label style="{{ $labelStyle }}">Ubicación</label>
    <input
        type="text"
        name="location"
        value="{{ old('location', $party->location ?? '') }}"
        placeholder="Ej: Sala Apolo, Barcelona"
        style="{{ $inputStyle }}"
        onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
        onblur="this.style.borderColor='rgba(255,255,255,0.25)'"
    />
    @error('location') <p style="{{ $errorStyle }}">{{ $message }}</p> @enderror
</div>

{{-- Fecha de inicio --}}
<div class="space-y-1.5">
    <label style="{{ $labelStyle }}">Inicio de la fiesta *</label>
    <input
        type="datetime-local"
        name="starts_at"
        value="{{ old('starts_at', isset($party) ? $party->starts_at->format('Y-m-d\TH:i') : '') }}"
        style="{{ $inputStyle }} color-scheme: dark;"
        onfocus="this.style.borderColor='rgba(255,255,255,0.6)'"
        onblur="this.style.borderColor='rgba(255,255,255,0.25)'"
    />
    @error('starts_at') <p style="{{ $errorStyle }}">{{ $message }}</p> @enderror
</div>

{{-- Imagen de portada --}}
<div class="space-y-2">
    <label style="{{ $labelStyle }}">Imagen de portada</label>
    @if($isEdit && $party->cover_image)
        <div class="flex items-center gap-3 mb-2">
            <img src="{{ asset('storage/' . $party->cover_image) }}"
                alt="Portada actual"
                class="h-16 w-28 rounded-xl object-cover"
                style="outline: 2px solid rgba(255,255,255,0.3); outline-offset: 1px;">
            <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Sube una nueva para reemplazarla</p>
        </div>
    @endif
    <input
        type="file"
        name="cover_image"
        accept="image/*"
        style="background-color: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 12px 16px; color: rgba(255,255,255,0.7); font-size: 14px; width: 100%;"
    />
    @error('cover_image') <p style="{{ $errorStyle }}">{{ $message }}</p> @enderror
</div>