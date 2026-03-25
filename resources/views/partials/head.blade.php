<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="vapid-key" content="{{ config('webpush.vapid.public_key') }}" />
<meta name="theme-color" content="#49197C" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

<title>
    {{ filled($title ?? null) ? $title.' — VicioApp' : 'VicioApp' }}
</title>

<link rel="icon" type="image/png" href="/public/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/public/favicon.svg" />
<link rel="shortcut icon" href="/public/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/public/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="VicioApp" />
<link rel="manifest" href="/public/site.webmanifest" />

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    document.documentElement.classList.add('dark');
    localStorage.setItem('flux_appearance', 'dark');
</script>   
