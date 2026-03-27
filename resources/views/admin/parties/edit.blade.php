<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">
<div class="w-full max-w-[430px] mx-auto flex flex-col" style="height: 100dvh; background-color: #A678C8;">

    {{-- Header --}}
    <div class="shrink-0 flex items-center px-4 py-4 gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
        <a href="{{ route('admin.parties.index') }}" wire:navigate
            class="shrink-0 size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-white font-bold text-xl leading-tight truncate">{{ $party->name }}</h1>
            <p class="text-xs font-mono truncate" style="color: rgba(255,255,255,0.5);">{{ $party->qr_code }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
        @csrf
        <button type="submit"
            class="size-9 rounded-full flex items-center justify-center transition-opacity hover:opacity-80"
            style="background-color: rgba(255,255,255,0.2);">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </button>
    </form>
    </div>

    {{-- Contenido scrollable --}}
    <div class="flex-1 overflow-y-auto px-4 py-5 space-y-4">

        {{-- Cambio de estado --}}
        <div class="rounded-2xl p-4 space-y-3" style="background-color: rgba(255,255,255,0.15);margin-top:10px">
            <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.85);">Estado de la fiesta</p>
            <form method="POST" action="{{ route('admin.parties.status', $party) }}" class="flex gap-2">
                @csrf @method('PATCH')
                @foreach(['registration' => 'Registro', 'active' => 'Activa', 'finished' => 'Finalizada'] as $value => $label)
                    @php $isCurrent = $party->status === $value; @endphp
                    <button
                        type="submit"
                        name="status"
                        value="{{ $value }}"
                        class="flex-1 py-2 rounded-xl text-xs font-semibold transition-opacity hover:opacity-80"
                        style="{{ $isCurrent
                            ? 'background-color: #2d0a3e; color: white;'
                            : 'background-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);' }}"
                    >
                        @if($value === 'active' && $isCurrent)
                            <span class="inline-block size-1.5 rounded-full bg-green-400 mr-1 align-middle" style="animation: pulse 1.5s infinite;"></span>
                        @endif
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>

        {{-- Formulario principal --}}
        <form method="POST" action="{{ route('admin.parties.update', $party) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')

            @include('admin.parties.form')

            {{-- Botones --}}
            <div class="flex gap-3 pt-2 pb-6">
                <a href="{{ route('admin.parties.index') }}" wire:navigate
                    class="flex-1 font-semibold py-4 rounded-2xl text-center transition-opacity hover:opacity-80 text-sm"
                    style="background-color: rgba(255,255,255,0.2); color: white;">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 font-bold py-4 rounded-2xl transition-opacity hover:opacity-90 text-sm text-white"
                    style="background-color: #2d0a3e;">
                    Guardar cambios
                </button>
            </div>
        </form>

    </div>

</div>
@fluxScripts
</body>
</html>