<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950 overflow-hidden" style="max-width: 430px; margin:auto"">
    <div class="w-full max-w-[430px] mx-auto overflow-hidden" style="height:100dvh;">
        <livewire:party.swipe-engine :party="$party" />
    </div>
@auth
    <livewire:chat.chat-notification />
    <livewire:auth.ban-watcher />
    @include('partials.push-prompt')
@endauth
    @fluxScripts
</body>
</html>