<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="h-dvh bg-zinc-950 overflow-hidden">
<div class="w-full max-w-[430px] mx-auto overflow-y-auto" style="height: 100dvh; background-color: #0a0212;">
    {{ $slot }}
</div>

@auth
    <livewire:chat.chat-notification />
    @if(!auth()->user()->is_admin)
        <livewire:auth.ban-watcher />
    @endif
    @include('partials.push-prompt')
    @if(!auth()->user()->is_admin)
    <script>
        window.addEventListener('load', () => {
            Echo.private('user.{{ auth()->id() }}')
                .listen('.banned', () => {
                    fetch('/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    }).finally(() => {
                        window.location.href = '/login?banned=1';
                    });
                });
    </script>
    @endif
@endauth

@fluxScripts
</body>
</html>