<?php
$requestPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($requestPath === '') $requestPath = '/';
$loggedIn = isLoggedIn();
?>
<!-- Sidebar -->
<aside class="w-56 shrink-0 bg-white dark:bg-slate-900 border-r border-gray-200/80 dark:border-slate-800 flex flex-col h-screen sticky top-0">
    <!-- Logo -->
    <div class="px-5 py-5">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center text-sm font-bold text-white dark:text-gray-900">V</div>
            <span class="text-[15px] font-semibold text-gray-900 dark:text-white">VisiSkelbimai</span>
        </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 space-y-0.5">
        <a href="/"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $requestPath === '/' ? 'active text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400' ?>">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
            Dashboard
        </a>

        <?php if ($loggedIn): ?>
        <div class="pt-4 pb-1.5 px-3">
            <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500">Paieškos</span>
        </div>
        <a href="/skelbiu"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $requestPath === '/skelbiu' ? 'active text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400' ?>">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Skelbiu.lt
        </a>
        <a href="/autoplius"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $requestPath === '/autoplius' ? 'active text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400' ?>">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Autoplius.lt
        </a>
        <a href="/aruodas"
           class="sidebar-link flex items-center gap-3 px-3 py-2 text-[13px] <?= $requestPath === '/aruodas' ? 'active text-gray-900 dark:text-white' : 'text-gray-500 dark:text-slate-400' ?>">
            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            Aruodas.lt
        </a>
        <?php endif; ?>
    </nav>

    <!-- Theme Toggle + User -->
    <div class="px-3 py-4 border-t border-gray-100 dark:border-slate-800 space-y-3">
        <button onclick="toggleTheme()" class="w-full flex items-center gap-3 px-3 py-2 sidebar-link text-[13px] text-gray-500 dark:text-slate-400">
            <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg class="w-4 h-4 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <span class="dark:hidden">Tamsi tema</span>
            <span class="hidden dark:inline">Šviesi tema</span>
        </button>

        <?php if ($loggedIn): ?>
        <div class="flex items-center justify-between px-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-xs font-semibold text-gray-600 dark:text-slate-300">
                    <?= strtoupper(substr(currentUser(), 0, 1)) ?>
                </div>
                <span class="text-[13px] text-gray-600 dark:text-slate-400"><?= htmlspecialchars(currentUser()) ?></span>
            </div>
            <a href="/login?action=logout" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition-colors" title="Atsijungti">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        </div>
        <?php else: ?>
        <a href="/login" class="flex items-center justify-center px-3 py-2 rounded-lg bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 text-[13px] font-medium transition-colors">
            Prisijungti
        </a>
        <?php endif; ?>
    </div>
</aside>

<!-- Main Content -->
<div class="flex-1 min-h-screen overflow-y-auto">

<script>
function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}
</script>
