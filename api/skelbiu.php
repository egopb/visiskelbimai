<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/helpers.php';

$platform = 'skelbiu';
$meta = $platformMeta[$platform];
$pageTitle = $meta['name'] . ' paieška — VisiSkelbimai';

// Load saved searches from JSON
$dataDir = is_writable(__DIR__ . '/data') ? __DIR__ . '/data/searches' : '/tmp/visiskelbimai/searches';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$searchFile = "$dataDir/$platform.json";
$searches = [];
if (file_exists($searchFile)) {
    $data = json_decode(file_get_contents($searchFile), true);
    $searches = $data['searches'] ?? [];
}

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
$ic = "w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-{$meta['color']}-500";
?>

<main class="px-6 sm:px-8 py-6 space-y-6 max-w-[1200px]">
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
        <button onclick="document.getElementById('newSearchModal').classList.remove('hidden')"
                class="bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
            + Nauja paieška
        </button>
    </div>

    <?php if (empty($searches)): ?>
    <div class="card p-10 text-center">
        <div class="text-5xl mb-4">🔍</div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nėra aktyvių paieškų</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Sukurkite naują paiešką ir sistema periodiškai skenuos <?= $meta['name'] ?></p>
        <button onclick="document.getElementById('newSearchModal').classList.remove('hidden')"
                class="bg-<?= $meta['color'] ?>-600 hover:bg-<?= $meta['color'] ?>-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
            Sukurti pirmą paiešką
        </button>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($searches as $search): ?>
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($search['name']) ?></h3>
                        <?php if ($search['active']): ?>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-500/25">Aktyvus</span>
                        <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-xs font-medium">Sustabdytas</span>
                        <?php endif; ?>
                        <?php if (!empty($search['category'])): ?>
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs"><?= htmlspecialchars($search['category']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php foreach (($search['params'] ?? []) as $key => $val): ?>
                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-xs text-gray-600 dark:text-slate-300">
                            <span class="text-gray-400 dark:text-slate-500"><?= htmlspecialchars($key) ?>:</span> <?= htmlspecialchars($val) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400 dark:text-slate-500">
                        <span>🔄 Kas <?php
                            $iv = $search['interval'] ?? 60;
                            if ($iv < 60) echo $iv . ' min.';
                            elseif ($iv < 1440) echo ($iv / 60) . ' val.';
                            else echo ($iv / 1440) . ' d.';
                        ?></span>
                        <?php if (!empty($search['lastScan'])): ?>
                        <span>🕐 <?= timeAgo($search['lastScan']) ?> prieš</span>
                        <?php else: ?>
                        <span>🕐 Dar neskenuota</span>
                        <?php endif; ?>
                        <span>📋 <?= $search['foundAds'] ?? 0 ?> skelbimų</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <?php if (!empty($search['searchUrl'])): ?>
                    <a href="<?= htmlspecialchars($search['searchUrl']) ?>" target="_blank" rel="noopener"
                       class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Atidaryti <?= $meta['name'] ?>">🔗</a>
                    <?php endif; ?>
                    <button onclick="scanSearch('<?= $search['id'] ?>', this)"
                            class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-<?= $meta['color'] ?>-600 dark:hover:text-<?= $meta['color'] ?>-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Skenuoti dabar">🔍</button>
                    <button onclick="toggleSearch('<?= $search['id'] ?>')"
                            class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
                            title="<?= $search['active'] ? 'Sustabdyti' : 'Aktyvuoti' ?>"><?= $search['active'] ? '⏸️' : '▶️' ?></button>
                    <button onclick="deleteSearch('<?= $search['id'] ?>')"
                            class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Ištrinti">🗑️</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div id="newSearchModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 dark:bg-black/60 backdrop-blur-sm">
        <div class="w-full max-w-lg mx-4 rounded-2xl border border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nauja <?= $meta['name'] ?> paieška</h2>
                <button onclick="document.getElementById('newSearchModal').classList.add('hidden')"
                        class="text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white text-xl">&times;</button>
            </div>
            <form id="searchForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Paieškos pavadinimas</label>
                    <input type="text" name="search_name" required placeholder="pvz. BMW 3 serija Kaune" class="<?= $ic ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Kategorija</label>
                    <select name="category" data-param="Kategorija" class="<?= $ic ?>">
                        <option>Automobiliai</option><option>Kompiuteriai</option><option>Telefonai</option>
                        <option>Buitinė technika</option><option>Baldai</option><option>Kita</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Raktažodis</label>
                    <input type="text" data-param="Raktažodis" placeholder="pvz. MacBook Pro, iPhone 15..." class="<?= $ic ?>">
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
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Miestas</label>
                    <select data-param="Miestas" class="<?= $ic ?>">
                        <option value="">Visa Lietuva</option>
                        <option>Vilnius</option><option>Kaunas</option><option>Klaipėda</option>
                        <option>Šiauliai</option><option>Panevėžys</option>
                    </select>
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

<script>
const PLATFORM = '<?= $platform ?>';
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('action', 'create');
    fd.append('platform', PLATFORM);
    fd.append('name', this.querySelector('[name="search_name"]').value);
    fd.append('interval', this.querySelector('[name="interval"]').value);
    const params = {};
    this.querySelectorAll('[data-param]').forEach(el => {
        const v = el.value.trim();
        if (v) params[el.dataset.param] = v;
    });
    fd.append('params', JSON.stringify(params));
    const cat = this.querySelector('[name="category"]');
    if (cat && cat.value) fd.append('category', cat.value);
    const res = await fetch('/search-api', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) location.reload();
    else alert(data.error || 'Klaida');
});
async function toggleSearch(id) {
    const fd = new FormData();
    fd.append('action', 'toggle');
    fd.append('platform', PLATFORM);
    fd.append('id', id);
    await fetch('/search-api', { method: 'POST', body: fd });
    location.reload();
}
async function deleteSearch(id) {
    if (!confirm('Tikrai ištrinti šią paiešką?')) return;
    const fd = new FormData();
    fd.append('action', 'delete');
    fd.append('platform', PLATFORM);
    fd.append('id', id);
    await fetch('/search-api', { method: 'POST', body: fd });
    location.reload();
}
async function scanSearch(id, btn) {
    const orig = btn.textContent;
    btn.textContent = '⏳';
    btn.disabled = true;
    try {
        const fd = new FormData();
        fd.append('action', 'scan');
        fd.append('platform', PLATFORM);
        fd.append('id', id);
        const res = await fetch('/search-api', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.url) window.open(data.url, '_blank');
        setTimeout(() => location.reload(), 500);
    } catch(e) {
        btn.textContent = orig;
        btn.disabled = false;
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
