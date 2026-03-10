<flux:dropdown position="bottom" align="start" {{ $attributes }}>
    <flux:sidebar.profile
        :name="auth()->user()->username ?? auth()->user()->name"
        :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-2 py-2 text-start text-sm">
            @if(auth()->user()->profile_photo_path)
                <img
                    src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                    class="size-8 rounded-full object-cover"
                    alt="{{ auth()->user()->username }}"
                />
            @else
                <flux:avatar
                    :name="auth()->user()->username ?? auth()->user()->name"
                    :initials="auth()->user()->initials()"
                />
            @endif
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ auth()->user()->username ?? auth()->user()->name }}</flux:heading>
                @if(auth()->user()->email)
                    <flux:text class="truncate text-xs">{{ auth()->user()->email }}</flux:text>
                @endif
            </div>
        </div>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Ajustes') }}
            </flux:menu.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Salir') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>