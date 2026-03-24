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
        <div>
            <h1 class="text-white font-bold text-xl leading-tight">Nueva fiesta</h1>
            <p class="text-xs" style="color: rgba(255,255,255,0.6);">El QR se generará automáticamente</p>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="flex-1 overflow-y-auto px-4 py-5">
        <form method="POST" action="{{ route('admin.parties.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

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
                    Crear fiesta
                </button>
            </div>
        </form>
    </div>

</div>
@fluxScripts
</body>
</html>