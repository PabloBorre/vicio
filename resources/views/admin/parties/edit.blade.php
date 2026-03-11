<x-layouts::app.sidebar>
    <div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.parties.index') }}" wire:navigate class="size-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-white text-xl font-bold">Editar: {{ $party->name }}</h1>
                <p class="text-zinc-500 text-sm">QR: <span class="font-mono text-zinc-400">{{ $party->qr_code }}</span></p>
            </div>
        </div>

        {{-- Cambio de estado --}}
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 space-y-3">
            <p class="text-zinc-400 text-sm font-medium">Cambiar estado manualmente</p>
            <form method="POST" action="{{ route('admin.parties.status', $party) }}" class="flex flex-wrap gap-2">
                @csrf @method('PATCH')
                @foreach(['draft' => 'Borrador', 'registration' => 'Registro', 'countdown' => 'Cuenta atrás', 'active' => 'Activa', 'finished' => 'Finalizada'] as $value => $label)
                    <button
                        type="submit"
                        name="status"
                        value="{{ $value }}"
                        @class([
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all',
                            'vicio-gradient text-white ring-2 ring-vicio-400 ring-offset-2 ring-offset-zinc-900' => $party->status === $value,
                            'bg-zinc-800 text-zinc-400 hover:bg-zinc-700' => $party->status !== $value,
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('admin.parties.update', $party) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            @include('admin.parties._form')

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.parties.index') }}" wire:navigate
                    class="flex-1 bg-zinc-800 text-zinc-300 font-semibold py-3 rounded-xl text-center hover:bg-zinc-700 transition-colors text-sm">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 vicio-gradient text-white font-semibold py-3 rounded-xl hover:opacity-90 transition-opacity text-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-layouts::app.sidebar>