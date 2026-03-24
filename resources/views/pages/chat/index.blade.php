<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="overflow-hidden" style="background-color: #A678C8;">

<livewire:chat.chat-list />

@auth
    <livewire:auth.ban-watcher />
@endauth

@fluxScripts
</body>
</html>