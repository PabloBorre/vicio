<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="bg-zinc-950 overflow-hidden">

<livewire:chat.chat-conversation :match="$match" />
<livewire:chat.chat-notification :currentMatchId="$match->id" />
<livewire:auth.ban-watcher />

    @fluxScripts
</body>
</html>