<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="h-dvh overflow-hidden bg-zinc-950">

<flux:sidebar sticky stashable class="border-e border-zinc-800 bg-zinc-950">

    <flux:sidebar.header class="border-b border-zinc-800 py-4">
        <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
    </flux:sidebar.header>

    <flux:sidebar.nav class="py-4">
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

        @if(auth()->user()?->is_admin)
            <flux:sidebar.group :heading="__('Administración')" class="grid">
                <flux:sidebar.item
                    icon="calendar-days"
                    href="/admin/parties"
                    :current="request()->is('admin/*')"
                    wire:navigate
                >
                    {{ __('Fiestas') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        @endif
    </flux:sidebar.nav>

    <flux:spacer />

    <flux:sidebar.nav class="border-t border-zinc-800 py-2">
        <flux:sidebar.item
            icon="cog-6-tooth"
            :href="route('profile.edit')"
            :current="request()->routeIs('profile.*')"
            wire:navigate
        >
            {{ __('Ajustes') }}
        </flux:sidebar.item>

        <x-desktop-user-menu class="w-full" />
    </flux:sidebar.nav>

</flux:sidebar>

{{-- Header móvil --}}
<flux:header class="lg:hidden border-b border-zinc-800 bg-zinc-950 py-3">
    <flux:sidebar.toggle icon="bars-2" inset="left" />
    <flux:spacer />
    <x-app-logo href="{{ route('dashboard') }}" wire:navigate />
    <flux:spacer />
    <div class="size-8"></div>
</flux:header>

<flux:main class="bg-zinc-950 !p-0">
    {{ $slot }}
</flux:main>

@fluxScripts
</body>
</html>