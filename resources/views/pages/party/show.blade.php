<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-950">

    <livewire:party.party-show :party="$party" />

    @fluxScripts
</body>
</html>