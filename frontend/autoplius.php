<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/helpers.php';

$platform = 'autoplius';
$meta = $platformMeta[$platform];
$pageTitle = $meta['name'] . ' paieška — VisiSkelbimai';

// Mock saved auto searches
$searches = [
    [
        'id' => 1,
        'name' => 'VW Passat B8 dyzelis',
        'active' => true,
        'interval' => 60,
        'category' => 'Automobiliai',
        'params' => [
            'Markė' => 'Volkswagen',
            'Modelis' => 'Passat',
            'Kuras' => 'Dyzelis',
            'Kaina nuo' => '12 000 €',
            'Kaina iki' => '20 000 €',
            'Metai nuo' => '2017',
            'Rida iki' => '200 000 km',
        ],
        'lastScan' => '2025-07-17T14:00:00Z',
        'foundAds' => 12,
    ],
    [
        'id' => 2,
        'name' => 'Toyota RAV4 Hybrid',
        'active' => true,
        'interval' => 360,
        'category' => 'Automobiliai',
        'params' => [
            'Markė' => 'Toyota',
            'Modelis' => 'RAV4',
            'Kuras' => 'Hibridas',
            'Kaina iki' => '35 000 €',
            'Metai nuo' => '2020',
        ],
        'lastScan' => '2025-07-17T10:00:00Z',
        'foundAds' => 6,
    ],
];

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-<?= $meta['color'] ?>-500/15 border border-<?= $meta['color'] ?>-500/25 flex items-center justify-center text-2xl">
                <?= $meta['icon'] ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold"><?= $meta['name'] ?> paieška</h1>
                <p class="text-sm text-slate-500"><?= $meta['desc'] ?></p>
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
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-5 card-hover">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($search['name']) ?></h3>
                        <?php if ($search['active']): ?>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 text-xs font-medium border border-emerald-500/25">Aktyvus</span>
                        <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full bg-slate-700 text-slate-400 text-xs font-medium">Sustabdytas</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php foreach ($search['params'] as $key => $val): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-800 text-xs text-slate-300">
                            <span class="text-slate-500"><?= htmlspecialchars($key) ?>:</span> <?= htmlspecialchars($val) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
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
                    <button class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Redaguoti">✏️</button>
                    <button class="p-2 rounded-lg text-slate-400 hover:text-<?= $search['active'] ? 'orange' : 'emerald' ?>-400 hover:bg-slate-800 transition-colors"
                            title="<?= $search['active'] ? 'Sustabdyti' : 'Aktyvuoti' ?>"><?= $search['active'] ? '⏸️' : '▶️' ?></button>
                    <button class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors" title="Ištrinti">🗑️</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- New Search Modal -->
    <div id="newSearchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
        <div class="w-full max-w-lg mx-4 rounded-2xl border border-slate-700 bg-slate-900 p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold">Nauja <?= $meta['name'] ?> paieška</h2>
                <button onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-white text-xl">&times;</button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Paieškos pavadinimas</label>
                    <input type="text" placeholder="pvz. Audi A4 Avant"
                           class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Markė</label>
                        <select class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                            <option value="">Visos</option>
                            <option>Audi</option><option>BMW</option><option>Ford</option>
                            <option>Hyundai</option><option>Mercedes-Benz</option><option>Škoda</option>
                            <option>Toyota</option><option>Volkswagen</option><option>Volvo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Modelis</label>
                        <input type="text" placeholder="pvz. Passat, RAV4..."
                               class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Kaina nuo (€)</label>
                        <input type="number" placeholder="0"
                               class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Kaina iki (€)</label>
                        <input type="number" placeholder="50000"
                               class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Metai nuo</label>
                        <input type="number" placeholder="2015" min="1990" max="2026"
                               class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Rida iki (km)</label>
                        <input type="number" placeholder="200000"
                               class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Kuro tipas</label>
                        <select class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                            <option value="">Visi</option>
                            <option>Dyzelis</option><option>Benzinas</option>
                            <option>Hibridas</option><option>Elektra</option><option>Dujos (LPG)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Pavarų dėžė</label>
                        <select class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                            <option value="">Visos</option>
                            <option>Automatinė</option><option>Mechaninė</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Skenavimo intervalas</label>
                    <select class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-<?= $meta['color'] ?>-500">
                        <option value="15">Kas 15 min.</option>
                        <option value="60" selected>Kas 1 val.</option>
                        <option value="360">Kas 6 val.</option>
                        <option value="1440">Kas 24 val.</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-medium border border-slate-700 text-slate-300 hover:bg-slate-800 transition-colors">
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
