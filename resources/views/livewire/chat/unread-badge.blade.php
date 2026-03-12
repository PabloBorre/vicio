<flux:sidebar.item
    icon="chat-bubble-left-right"
    :href="route('chats.index')"
    :current="request()->routeIs('chats.*')"
    :badge="$count > 0 ? ($count > 99 ? '99+' : $count) : null"
    badge-color="pink"
    wire:navigate
>
    {{ __('Chats') }}
</flux:sidebar.item>
