<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950 overflow-hidden">

    <livewire:party.swipe-engine :party="$party" />

    @auth
        <livewire:chat.chat-notification />
        @include('partials.push-prompt')
    @endauth

    @fluxScripts
</body>
</html>