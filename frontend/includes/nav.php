<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$loggedIn = isLoggedIn();
?>
<!-- Sidebar -->
<aside class="w-56 shrink-0 bg-white border-r border-gray-200/80 flex flex-col h-screen sticky top-0">
    <!-- Logo -->
    <div class="px-5 py-5">
        <a href="index.php" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center text-sm font-bold text-white">V</div>
            <span class="text-[15px] font-semibold text-gray-900">VisiSkelbimai</span>
        </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 space-y-0.5">
        <a href="index.php"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $currentPage === 'index.php' ? 'active text-gray-900' : 'text-gray-500' ?>">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
            Dashboard
        </a>

        <?php if ($loggedIn): ?>
        <div class="pt-4 pb-1.5 px-3">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Paieškos</span>
        </div>
        <a href="skelbiu.php"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $currentPage === 'skelbiu.php' ? 'active text-gray-900' : 'text-gray-500' ?>">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Skelbiu.lt
        </a>
        <a href="autoplius.php"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $currentPage === 'autoplius.php' ? 'active text-gray-900' : 'text-gray-500' ?>">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Autoplius.lt
        </a>
        <a href="aruodas.php"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $currentPage === 'aruodas.php' ? 'active text-gray-900' : 'text-gray-500' ?>">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            Aruodas.lt
        </a>
        <?php endif; ?>
    </nav>

    <!-- User -->
    <div class="px-3 py-4 border-t border-gray-100">
        <?php if ($loggedIn): ?>
        <div class="flex items-center justify-between px-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600">
                    <?= strtoupper(substr(currentUser(), 0, 1)) ?>
                </div>
                <span class="text-[13px] text-gray-600"><?= htmlspecialchars(currentUser()) ?></span>
            </div>
            <a href="login.php?action=logout" class="text-gray-400 hover:text-gray-600 transition-colors" title="Atsijungti">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
        <?php else: ?>
        <a href="login.php" class="flex items-center justify-center px-3 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-[13px] font-medium transition-colors">
            Prisijungti
        </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 min-h-screen overflow-y-auto">
