<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/helpers.php';

$platform = 'autoplius';
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
                    <input type="text" name="search_name" required placeholder="pvz. Audi A4 Avant" class="<?= $ic ?>">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Markė</label>
                        <select data-param="Markė" class="<?= $ic ?>">
                            <option value="">Visos</option>
                            <option>Audi</option><option>BMW</option><option>Citroën</option><option>Ford</option>
                            <option>Honda</option><option>Hyundai</option><option>Kia</option>
                            <option>Mazda</option><option>Mercedes-Benz</option><option>Nissan</option>
                            <option>Opel</option><option>Peugeot</option><option>Renault</option><option>Seat</option>
                            <option>Škoda</option><option>Toyota</option><option>Volkswagen</option><option>Volvo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Modelis</label>
                        <input type="text" data-param="Modelis" placeholder="pvz. Passat, RAV4..." class="<?= $ic ?>">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina nuo (€)</label>
                        <input type="number" data-param="Kaina nuo" placeholder="0" class="<?= $ic ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina iki (€)</label>
                        <input type="number" data-param="Kaina iki" placeholder="50000" class="<?= $ic ?>">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Metai nuo</label>
                        <input type="number" data-param="Metai nuo" placeholder="2015" min="1990" max="2026" class="<?= $ic ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Rida iki (km)</label>
                        <input type="number" data-param="Rida iki" placeholder="200000" class="<?= $ic ?>">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kuro tipas</label>
                        <select data-param="Kuro tipas" class="<?= $ic ?>">
                            <option value="">Visi</option>
                            <option>Dyzelis</option><option>Benzinas</option>
                            <option>Hibridas</option><option>Elektra</option><option>Dujos (LPG)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Pavarų dėžė</label>
                        <select data-param="Pavarų dėžė" class="<?= $ic ?>">
                            <option value="">Visos</option><option>Automatinė</option><option>Mechaninė</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Skenavimo intervalas</label>
                    <select name="interval" class="<?= $ic ?>">
                        <option value="15">Kas 15 min.</option><option value="60" selected>Kas 1 val.</option>
                        <option value="360">Kas 6 val.</option><option value="1440">Kas 24 val.</option>
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
