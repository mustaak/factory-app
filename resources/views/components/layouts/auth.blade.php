<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Sign In | Factory' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="m-0 min-h-screen bg-[#f3f4f6] text-slate-900 antialiased">

    {{ $slot }}

    @livewireScripts
</body>
</html>