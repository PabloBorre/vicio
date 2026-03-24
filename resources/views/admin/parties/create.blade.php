    <div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.parties.index') }}" wire:navigate class="size-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-white text-xl font-bold">Nueva fiesta</h1>
                <p class="text-zinc-500 text-sm">El QR se generará automáticamente</p>
            </div>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.parties.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @include('admin.parties.form')

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.parties.index') }}" wire:navigate
                    class="flex-1 bg-zinc-800 text-zinc-300 font-semibold py-3 rounded-xl text-center hover:bg-zinc-700 transition-colors text-sm">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 vicio-gradient text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity text-sm">
                    Crear fiesta
                </button>
            </div>
        </form>
    </div>
