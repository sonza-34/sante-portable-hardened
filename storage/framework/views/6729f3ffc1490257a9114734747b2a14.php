<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Tableau de bord'); ?> — Santé Portable</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: false }">

<?php if(auth()->guard()->check()): ?>
    <div class="flex min-h-screen">
        
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-gray-100 h-screen sticky top-0">
            <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2.5 px-6 py-6">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md">
                    🏥
                </div>
                <span class="font-bold text-gray-900">Santé Portable</span>
            </a>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">📊</span> Tableau de bord
                </a>
                <a href="<?php echo e(route('medical-record')); ?>" class="<?php echo e(request()->routeIs('medical-record') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">📋</span> Dossier médical
                </a>
                <a href="<?php echo e(route('prescriptions')); ?>" class="<?php echo e(request()->routeIs('prescriptions') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">💊</span> Ordonnances
                </a>
                <a href="<?php echo e(route('vaccinations')); ?>" class="<?php echo e(request()->routeIs('vaccinations') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">💉</span> Vaccins
                </a>
                <a href="<?php echo e(route('reminders')); ?>" class="<?php echo e(request()->routeIs('reminders') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">⏰</span> Rappels
                </a>
                <?php if (! (auth()->user()->hasRole('doctor'))): ?>
                    <a href="<?php echo e(route('doctors.index')); ?>" class="<?php echo e(request()->routeIs('doctors.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                        <span class="text-lg">🩺</span> Trouver un médecin
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('share.index')); ?>" class="<?php echo e(request()->routeIs('share.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">🔗</span> Partager
                </a>
                <a href="<?php echo e(route('chatbot')); ?>" class="<?php echo e(request()->routeIs('chatbot') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                    <span class="text-lg">🤖</span> Assistant IA
                </a>

                <?php if(auth()->user()->hasRole('doctor')): ?>
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <p class="px-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Espace médecin</p>
                        <a href="<?php echo e(route('doctor.patients')); ?>" class="<?php echo e(request()->routeIs('doctor.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>">
                            <span class="text-lg">👥</span> Mes patients
                        </a>
                    </div>
                <?php endif; ?>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 mb-3 px-1">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-sm font-bold shrink-0">
                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate"><?php echo e(auth()->user()->name); ?></div>
                        <div class="text-xs text-gray-400"><?php echo e(ucfirst(auth()->user()->roles->pluck('name')->first())); ?></div>
                    </div>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="w-full text-sm font-medium text-red-600 hover:bg-red-50 px-3.5 py-2 rounded-xl transition text-left">
                        ⎋ Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
            <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>
            <aside class="absolute left-0 top-0 h-full w-72 bg-white shadow-xl flex flex-col" @click.outside="sidebarOpen = false">
                <div class="flex items-center justify-between px-5 py-5">
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md">🏥</div>
                        <span class="font-bold text-gray-900">Santé Portable</span>
                    </a>
                    <button @click="sidebarOpen = false" class="text-gray-400 text-2xl leading-none">&times;</button>
                </div>
                <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                    <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">📊</span> Tableau de bord</a>
                    <a href="<?php echo e(route('medical-record')); ?>" class="<?php echo e(request()->routeIs('medical-record') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">📋</span> Dossier médical</a>
                    <a href="<?php echo e(route('prescriptions')); ?>" class="<?php echo e(request()->routeIs('prescriptions') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">💊</span> Ordonnances</a>
                    <a href="<?php echo e(route('vaccinations')); ?>" class="<?php echo e(request()->routeIs('vaccinations') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">💉</span> Vaccins</a>
                    <a href="<?php echo e(route('reminders')); ?>" class="<?php echo e(request()->routeIs('reminders') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">⏰</span> Rappels</a>
                    <?php if (! (auth()->user()->hasRole('doctor'))): ?>
                        <a href="<?php echo e(route('doctors.index')); ?>" class="<?php echo e(request()->routeIs('doctors.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">🩺</span> Trouver un médecin</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('share.index')); ?>" class="<?php echo e(request()->routeIs('share.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">🔗</span> Partager</a>
                    <a href="<?php echo e(route('chatbot')); ?>" class="<?php echo e(request()->routeIs('chatbot') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">🤖</span> Assistant IA</a>
                    <?php if(auth()->user()->hasRole('doctor')): ?>
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <p class="px-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Espace médecin</p>
                            <a href="<?php echo e(route('doctor.patients')); ?>" class="<?php echo e(request()->routeIs('doctor.*') ? 'sidebar-link-active' : 'sidebar-link'); ?>"><span class="text-lg">👥</span> Mes patients</a>
                        </div>
                    <?php endif; ?>
                </nav>
                <div class="p-4 border-t border-gray-100">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="w-full text-sm font-medium text-red-600 hover:bg-red-50 px-3.5 py-2 rounded-xl transition text-left">⎋ Déconnexion</button>
                    </form>
                </div>
            </aside>
        </div>

        
        <div class="flex-1 min-w-0">
            
            <div class="lg:hidden sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-gray-100 px-4 py-3 flex items-center justify-between">
                <button @click="sidebarOpen = true" class="text-gray-600 text-2xl leading-none">☰</button>
                <span class="font-bold text-gray-900">Santé Portable</span>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-bold">
                    <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                </div>
            </div>

            <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
                <?php if(session('success')): ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                        <span>✅</span> <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                        <span>❌</span> <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
<?php else: ?>
    
    <main>
        <?php if(session('success')): ?>
            <div class="max-w-6xl mx-auto px-4 pt-6">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">✅ <?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <footer class="border-t border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-4 py-6 text-center text-sm text-gray-500">
            <p>© <?php echo e(date('Y')); ?> <strong class="text-gray-800">Santé Portable</strong> — Un dossier médical qui vous suit partout 🇲🇦</p>
            <p class="text-xs mt-1 text-gray-400">100% privé • Made in Morocco • Sécurisé</p>
        </div>
    </footer>
<?php endif; ?>

</body>
</html>
<?php /**PATH C:\Users\1996\Downloads\sante-portable-hardened\sante-portable\resources\views/layouts/app.blade.php ENDPATH**/ ?>