<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} - {{ $title ?? '' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ public_path('css/output.css') }}">

</head>
<body class="bg-gray-100 dark:bg-gray-900">
<div class="py-4 px-2">
    <div class="mt-12 rounded-lg dark:border-gray-700">
        {{ $slot }}
    </div>
</div>
</body>
</html>
