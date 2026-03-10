<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950">

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-800 bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Principal')" class="grid">
                    <flux:sidebar.item icon="fire" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Inicio') }}
                    </flux:sidebar.item>

                    {{-- Estas rutas se añadirán en pasos siguientes --}}
                    {{-- <flux:sidebar.item icon="heart" :href="route('matches.index')" wire:navigate>
                        {{ __('Mis Matches') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('chats.index')" wire:navigate>
                        {{ __('Chats') }}
                    </flux:sidebar.item> --}}
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->username ?? auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden border-b border-zinc-800 bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden text-zinc-400" icon="bars-2" inset="left" />

            <flux:spacer />

            {{-- Notificaciones (se activará en paso 6) --}}
            <flux:navbar class="me-2">
                <flux:navbar.item icon="bell" href="#" class="text-zinc-400" />
            </flux:navbar>

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="text-zinc-300"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="flex items-center gap-2 px-2 py-2 text-sm">
                            @if(auth()->user()->profile_photo_path)
                                <img
                                    src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                                    class="size-8 rounded-full object-cover"
                                    alt="{{ auth()->user()->username }}"
                                />
                            @else
                                <flux:avatar :initials="auth()->user()->initials()" />
                            @endif
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->username ?? auth()->user()->name }}</flux:heading>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item icon="cog" :href="route('profile.edit')" wire:navigate>
                        {{ __('Ajustes') }}
                    </flux:menu.item>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Salir') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>