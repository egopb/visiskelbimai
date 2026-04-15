<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/helpers.php';

$platform = 'skelbiu';
$meta = $platformMeta[$platform];
$pageTitle = $meta['name'] . ' paieška — VisiSkelbimai';

// Load saved searches (mock)
$searches = [
    [
        'id' => 1,
        'name' => 'BMW 5 serija Vilniuje',
        'active' => true,
        'interval' => 60,
        'category' => 'Automobiliai',
        'params' => [
            'Markė' => 'BMW',
            'Modelis' => '5 serija',
            'Kaina nuo' => '10 000 €',
            'Kaina iki' => '25 000 €',
            'Metai nuo' => '2016',
            'Miestas' => 'Vilnius',
        ],
        'lastScan' => '2025-07-17T14:30:00Z',
        'foundAds' => 8,
    ],
    [
        'id' => 2,
        'name' => 'MacBook Pro',
        'active' => true,
        'interval' => 360,
        'category' => 'Kompiuteriai',
        'params' => [
            'Raktažodis' => 'MacBook Pro',
            'Kaina nuo' => '500 €',
            'Kaina iki' => '2 000 €',
        ],
        'lastScan' => '2025-07-17T12:00:00Z',
        'foundAds' => 3,
    ],
    [
        'id' => 3,
        'name' => 'iPhone 15',
        'active' => false,
        'interval' => 1440,
        'category' => 'Telefonai',
        'params' => [
            'Raktažodis' => 'iPhone 15 Pro',
            'Kaina iki' => '1 000 €',
        ],
        'lastScan' => '2025-07-16T18:00:00Z',
        'foundAds' => 5,
    ],
];

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="px-6 sm:px-8 py-6 space-y-6 max-w-[1200px]">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-<?= $meta['color'] ?>-100 dark:bg-<?= $meta['color'] ?>-500/15 border border-<?= $meta['color'] ?>-200 dark:border-<?= $meta['color'] ?>-500/25 flex items-center justify-center text-xl">
                <?= $meta['icon'] ?>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white"><?= $meta['name'] ?> paieška</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400"><?= $meta['desc'] ?></p>
            </div>
        </div>
        <button onclick="document.getElementById('newSearchModal').classList.toggle('hidden')"
                class="bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
            + Nauja paieška
        </button>
    </div>

    <!-- Saved Searches -->
    <div class="space-y-3">
        <?php foreach ($searches as $search): ?>
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($search['name']) ?></h3>
                        <?php if ($search['active']): ?>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-500/25">Aktyvus</span>
                        <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-xs font-medium">Sustabdytas</span>
                        <?php endif; ?>
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs"><?= htmlspecialchars($search['category']) ?></span>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php foreach ($search['params'] as $key => $val): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-xs text-gray-600 dark:text-slate-300">
                            <span class="text-gray-400 dark:text-slate-500"><?= htmlspecialchars($key) ?>:</span> <?= htmlspecialchars($val) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400 dark:text-slate-500">
                        <span>🔄 Kas <?php
                            if ($search['interval'] < 60) echo $search['interval'] . ' min.';
                            elseif ($search['interval'] < 1440) echo ($search['interval'] / 60) . ' val.';
                            else echo ($search['interval'] / 1440) . ' d.';
                        ?></span>
                        <span>🕐 Paskutinis: <?= timeAgo($search['lastScan']) ?> prieš</span>
                        <span>📋 Rasta: <?= $search['foundAds'] ?> skelbimų</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Redaguoti">✏️</button>
                    <button class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                            title="<?= $search['active'] ? 'Sustabdyti' : 'Aktyvuoti' ?>"><?= $search['active'] ? '⏸️' : '▶️' ?></button>
                    <button class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Ištrinti">🗑️</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- New Search Modal -->
    <div id="newSearchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm">
        <div class="w-full max-w-lg mx-4 rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nauja <?= $meta['name'] ?> paieška</h2>
                <button onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                        class="text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-xl">&times;</button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Paieškos pavadinimas</label>
                    <input type="text" placeholder="pvz. BMW 3 serija Kaune"
                           class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kategorija</label>
                    <select class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                        <option>Automobiliai</option><option>Kompiuteriai</option><option>Telefonai</option>
                        <option>Buitinė technika</option><option>Baldai</option><option>Kita</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina nuo (€)</label>
                        <input type="number" placeholder="0" class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kaina iki (€)</label>
                        <input type="number" placeholder="50000" class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Raktažodis</label>
                    <input type="text" placeholder="pvz. M paketas, 4K OLED..." class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Miestas</label>
                    <select class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                        <option value="">Visa Lietuva</option>
                        <option>Vilnius</option><option>Kaunas</option><option>Klaipėda</option>
                        <option>Šiauliai</option><option>Panevėžys</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Skenavimo intervalas</label>
                    <select class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                        <option value="15">Kas 15 min.</option><option value="60" selected>Kas 1 val.</option>
                        <option value="360">Kas 6 val.</option><option value="1440">Kas 24 val.</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                        Atšaukti
                    </button>
                    <button type="submit"
                            class="flex-1 bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        Sukurti paiešką
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
