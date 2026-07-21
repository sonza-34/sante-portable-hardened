<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bienvenue') — Santé Portable</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen">

@if(session('success'))
    <div class="max-w-2xl mx-auto px-4 pt-6">
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">✅ {{ session('success') }}</div>
    </div>
@endif

@yield('content')

</body>
</html>
