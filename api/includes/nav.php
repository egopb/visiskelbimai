<?php
$requestPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($requestPath === '') $requestPath = '/';
$loggedIn = isLoggedIn();
?>
<header class="border-b border-slate-800 glass sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo + Nav -->
            <div class="flex items-center gap-6">
                <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-base font-bold shadow-lg shadow-violet-500/25">
                        V
                    </div>
                    <span class="text-lg font-bold tracking-tight hidden sm:block">VisiSkelbimai</span>
                </a>

                <nav class="flex items-center gap-1">
                    <a href="/"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $requestPath === '/' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                        📊 Rezultatai
                    </a>
                    <?php if ($loggedIn): ?>
                    <a href="/skelbiu"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $requestPath === '/skelbiu' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/25' : 'text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10' ?>">
                        🟢 Skelbiu
                    </a>
                    <a href="/autoplius"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $requestPath === '/autoplius' ? 'bg-blue-500/15 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-blue-400 hover:bg-blue-500/10' ?>">
                        🔵 Autoplius
                    </a>
                    <a href="/aruodas"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $requestPath === '/aruodas' ? 'bg-orange-500/15 text-orange-400 border border-orange-500/25' : 'text-slate-400 hover:text-orange-400 hover:bg-orange-500/10' ?>">
                        🟠 Aruodas
                    </a>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Auth -->
            <div class="flex items-center gap-3">
                <?php if ($loggedIn): ?>
                    <span class="text-sm text-slate-500 hidden sm:block">👤 <?= htmlspecialchars(currentUser()) ?></span>
                    <a href="/login?action=logout"
                       class="px-3 py-1.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        Atsijungti
                    </a>
                <?php else: ?>
                    <a href="/login"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium bg-violet-600 hover:bg-violet-500 text-white transition-colors">
                        Prisijungti
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php
$currentPage = rtrim(parse_url($currentPage = basename($_SERVER['SCRIPT_NAME']);SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$loggedIn = isLoggedIn();
?>
<header class="border-b border-slate-800 glass sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo + Nav -->
            <div class="flex items-center gap-6">
                <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-base font-bold shadow-lg shadow-violet-500/25">
                        V
                    </div>
                    <span class="text-lg font-bold tracking-tight hidden sm:block">VisiSkelbimai</span>
                </a>

                <nav class="flex items-center gap-1">
                    <a href="/"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $currentPage === 'index.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>">
                        📊 Rezultatai
                    </a>
                    <?php if ($loggedIn): ?>
                    <a href="/skelbiu"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $currentPage === 'skelbiu.php' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/25' : 'text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10' ?>">
                        🟢 Skelbiu
                    </a>
                    <a href="/autoplius"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $currentPage === 'autoplius.php' ? 'bg-blue-500/15 text-blue-400 border border-blue-500/25' : 'text-slate-400 hover:text-blue-400 hover:bg-blue-500/10' ?>">
                        🔵 Autoplius
                    </a>
                    <a href="/aruodas"
                       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                              <?= $currentPage === 'aruodas.php' ? 'bg-orange-500/15 text-orange-400 border border-orange-500/25' : 'text-slate-400 hover:text-orange-400 hover:bg-orange-500/10' ?>">
                        🟠 Aruodas
                    </a>
                    <?php endif; ?>
                </nav>
            </div>

            <!-- Auth -->
            <div class="flex items-center gap-3">
                <?php if ($loggedIn): ?>
                    <span class="text-sm text-slate-500 hidden sm:block">👤 <?= htmlspecialchars(currentUser()) ?></span>
                    <a href="login.php?action=logout"
                       class="px-3 py-1.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        Atsijungti
                    </a>
                <?php else: ?>
                    <a href="/login"
                       class="px-4 py-1.5 rounded-lg text-sm font-medium bg-violet-600 hover:bg-violet-500 text-white transition-colors">
                        Prisijungti
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
