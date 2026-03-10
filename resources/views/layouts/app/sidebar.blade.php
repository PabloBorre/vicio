{{-- Dentro de flux:sidebar.nav, reemplazar el grupo Principal --}}

<flux:sidebar.group :heading="__('Principal')" class="grid">
    <flux:sidebar.item
        icon="fire"
        :href="route('dashboard')"
        :current="request()->routeIs('dashboard')"
        wire:navigate
    >
        {{ __('Inicio') }}
    </flux:sidebar.item>

    <flux:sidebar.item
        icon="chat-bubble-left-right"
        :href="route('chats.index')"
        :current="request()->routeIs('chats.*')"
        wire:navigate
    >
        {{ __('Chats') }}
    </flux:sidebar.item>
</flux:sidebar.group>