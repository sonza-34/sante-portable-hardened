<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="no-referrer">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, private">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Lien invalide</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    {{--
        NOTE SÉCURITÉ (anti-énumération) :
        On affiche un message GÉNÉRIQUE quel que soit le cas (révoqué, expiré, inconnu).
        Distinguer les cas reviendrait à donner un oracle à un attaquant qui brute-force
        des tokens : il saurait que tel token existe s'il voit "révoqué" vs "expiré" vs 404.
    --}}
    <div class="card max-w-md text-center">
        <div class="text-6xl mb-4">🔒</div>
        <h1 class="text-2xl font-bold mb-2">Lien invalide ou expiré</h1>
        <p class="text-gray-600">
            Ce lien de partage n'est pas valide, a expiré ou a été révoqué.
        </p>
        <p class="text-gray-500 text-sm mt-3">
            Demande au patient de générer un nouveau lien depuis son espace Santé Portable.
        </p>
    </div>
</body>
</html>
