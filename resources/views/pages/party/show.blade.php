<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950" style="max-width: 430px !important;">

    <livewire:party.party-show :party="$party" />

@auth
    <livewire:auth.ban-watcher />
@endauth

    @fluxScripts
</body>
</html>