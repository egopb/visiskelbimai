<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/helpers.php';

$platform = 'aruodas';
$meta = $platformMeta[$platform];
$pageTitle = $meta['name'] . ' paieška — VisiSkelbimai';

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
$ic = "w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-{$meta['color']}-500";
?>

<main class="px-6 sm:px-8 py-6 space-y-6 max-w-[1200px]">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-<?= $meta['color'] ?>-100 dark:bg-<?= $meta['color'] ?>-500/15 border border-<?= $meta['color'] ?>-200 dark:border-<?= $meta['color'] ?>-500/25 flex items-center justify-center text-xl"><?= $meta['icon'] ?></div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?= $meta['name'] ?> paieška</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400"><?= $meta['desc'] ?></p>
            </div>
        </div>
        <button onclick="document.getElementById('newSearchModal').classList.remove('hidden')"
                class="bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">+ Nauja paieška</button>
    </div>

    <div id="searchList"></div>

    <div id="newSearchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm">
        <div class="w-full max-w-lg mx-4 rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nauja <?= $meta['name'] ?> paieška</h2>
                <button onclick="document.getElementById('newSearchModal').classList.add('hidden')" class="text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-xl">&times;</button>
            </div>
            <form id="searchForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Paieškos pavadinimas</label>
                    <input type="text" name="search_name" required placeholder="pvz. 2k butas Senamiestyje" class="<?= $ic ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Tipas</label>
                    <select name="category" data-param="Tipas" class="<?= $ic ?>">
                        <option>Butai pardavimui</option><option>Butai nuomai</option>
                        <option>Namai pardavimui</option><option>Sklypai</option><option>Patalpos</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Miestas</label>
                    <select data-param="Miestas" class="<?= $ic ?>">
                        <option>Vilnius</option><option>Kaunas</option><option>Klaipėda</option>
                        <option>Šiauliai</option><option>Panevėžys</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Rajonas / mikrorajonas</label>
                    <input type="text" data-param="Rajonas" placeholder="pvz. Žirmūnai, Antakalnis..." class="<?= $ic ?>">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina nuo (€)</label>
                        <input type="number" data-param="Kaina nuo" placeholder="0" class="<?= $ic ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina iki (€)</label>
                        <input type="number" data-param="Kaina iki" placeholder="200000" class="<?= $ic ?>">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kambariai nuo</label>
                        <select data-param="Kambariai nuo" class="<?= $ic ?>">
                            <option value="">-</option>
                            <option>1</option><option>2</option><option>3</option><option>4</option><option>5+</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Plotas nuo (m²)</label>
                        <input type="number" data-param="Plotas nuo" placeholder="30" class="<?= $ic ?>">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Skenavimo intervalas</label>
                    <select name="interval" class="<?= $ic ?>">
                        <option value="15">Kas 15 min.</option><option value="60">Kas 1 val.</option>
                        <option value="360" selected>Kas 6 val.</option><option value="1440">Kas 24 val.</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">Atšaukti</button>
                    <button type="submit" class="flex-1 bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Sukurti paiešką</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/search-engine.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
