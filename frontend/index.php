<?php
/**
 * Skelbimu Monitoringo Dashboard — Rezultatų puslapis (public)
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

// Load mock data
$jsonPath = __DIR__ . '/data/mock-ads.json';
$data = json_decode(file_get_contents($jsonPath), true);
$ads = $data['ads'] ?? [];
$lastScan = $data['lastScan'] ?? '';
$scanCount = $data['scanCount'] ?? 0;

// Filters from GET params
$filterSource = $_GET['source'] ?? 'all';
$filterCategory = $_GET['category'] ?? 'all';
$filterSort = $_GET['sort'] ?? 'score';
$filterSearch = $_GET['q'] ?? '';

// Apply filters
$filtered = array_filter($ads, function ($ad) use ($filterSource, $filterCategory, $filterSearch) {
    if ($filterSource !== 'all' && $ad['source'] !== $filterSource) return false;
    if ($filterCategory !== 'all' && $ad['category'] !== $filterCategory) return false;
    if ($filterSearch && stripos($ad['title'], $filterSearch) === false) return false;
    return true;
});

// Sort
usort($filtered, function ($a, $b) use ($filterSort) {
    return match ($filterSort) {
        'price_asc' => $a['price'] - $b['price'],
        'price_desc' => $b['price'] - $a['price'],
        'date' => strtotime($b['date']) - strtotime($a['date']),
        default => $b['totalScore'] - $a['totalScore'], // score desc
    };
});

// Stats
$totalAds = count($ads);
$filteredCount = count($filtered);
$todayAds = count(array_filter($ads, fn($a) => date('Y-m-d', strtotime($a['date'])) === date('Y-m-d')));
$avgPrice = $totalAds > 0 ? array_sum(array_column($ads, 'price')) / $totalAds : 0;
$sources = array_unique(array_column($ads, 'source'));
$categories = array_unique(array_column($ads, 'category'));

// Group filtered ads by source (platform)
$platformOrder = ['skelbiu', 'autoplius', 'aruodas'];
$grouped = [];
foreach ($platformOrder as $p) {
    $grouped[$p] = array_values(array_filter($filtered, fn($a) => $a['source'] === $p));
}

$pageTitle = 'VisiSkelbimai — Rezultatai';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="px-6 sm:px-8 py-6 space-y-6 max-w-[1200px]">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm">
        <span class="font-semibold text-gray-900 dark:text-white">VisiSkelbimai</span>
        <span class="text-gray-300 dark:text-slate-600">/</span>
        <span class="text-gray-500 dark:text-slate-400">Dashboard</span>
    </div>

    <?php if (!isLoggedIn()): ?>
    <div class="card p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Prisijunkite, kad galėtumėte valdyti paieškas</p>
                <p class="text-xs text-gray-400 dark:text-slate-500">Skelbiu.lt, Autoplius.lt ir Aruodas.lt paieškų konfigūravimas</p>
            </div>
        </div>
        <a href="login.php"
           class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 transition-colors">
            Prisijungti
        </a>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-slate-400">Viso skelbimų</span>
                <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $totalAds ?></div>
            <div class="text-xs text-gray-400 dark:text-slate-500 mt-1">iš <?= count($sources) ?> platformų</div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-slate-400">Šiandien naujų</span>
                <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $todayAds ?></div>
            <div class="text-xs text-gray-400 dark:text-slate-500 mt-1">per paskutines 24 val.</div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-slate-400">Vid. kaina</span>
                <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white">€<?= number_format($avgPrice, 0, ',', ' ') ?></div>
            <div class="text-xs text-gray-400 dark:text-slate-500 mt-1">visų kategorijų</div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 dark:text-slate-400">Top reitingas</span>
                <svg class="w-5 h-5 text-gray-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= !empty($ads) ? number_format(max(array_column($ads, 'totalScore')), 1) : '0' ?>/100</div>
            <div class="text-xs text-gray-400 dark:text-slate-500 mt-1">geriausias pasiūlymas</div>
        </div>
    </div>

    <!-- Platform breakdown -->
    <div class="card p-5">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Platformų apžvalga</h3>
        <?php foreach ($platformOrder as $p):
            $pAds = $grouped[$p] ?? [];
            $pMeta = $platformMeta[$p];
            $pCount = count($pAds);
            $pPercent = $totalAds > 0 ? round($pCount / $totalAds * 100) : 0;
            $barColors = ['skelbiu' => 'bg-emerald-500', 'autoplius' => 'bg-gray-900 dark:bg-slate-300', 'aruodas' => 'bg-orange-500'];
        ?>
        <div class="mb-3.5 last:mb-0">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-gray-700 dark:text-slate-300"><?= $pMeta['name'] ?></span>
                <span class="text-sm text-gray-400 dark:text-slate-500"><?= $pPercent ?>%</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="score-bar h-full rounded-full <?= $barColors[$p] ?>" style="width: <?= $pPercent ?>%"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <form method="get" class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($filterSearch) ?>"
                   placeholder="Ieškoti skelbimuose..."
                   class="w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-4 py-2.5 pl-10 text-sm text-gray-900 dark:text-slate-100
                          focus:outline-none focus:border-gray-400 dark:focus:border-slate-500 focus:ring-1 focus:ring-gray-200 dark:focus:ring-slate-700
                          placeholder:text-gray-400 dark:placeholder:text-slate-500">
            <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <select name="source" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-slate-300 focus:outline-none focus:border-gray-400 dark:focus:border-slate-500">
            <option value="all" <?= $filterSource === 'all' ? 'selected' : '' ?>>Visos platformos</option>
            <?php foreach ($sources as $s): ?>
            <option value="<?= $s ?>" <?= $filterSource === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-slate-300 focus:outline-none focus:border-gray-400 dark:focus:border-slate-500">
            <option value="all" <?= $filterCategory === 'all' ? 'selected' : '' ?>>Visos kategorijos</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c ?>" <?= $filterCategory === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-slate-300 focus:outline-none focus:border-gray-400 dark:focus:border-slate-500">
            <option value="score" <?= $filterSort === 'score' ? 'selected' : '' ?>>Reitingas</option>
            <option value="price_asc" <?= $filterSort === 'price_asc' ? 'selected' : '' ?>>Kaina ↑</option>
            <option value="price_desc" <?= $filterSort === 'price_desc' ? 'selected' : '' ?>>Kaina ↓</option>
            <option value="date" <?= $filterSort === 'date' ? 'selected' : '' ?>>Naujausi</option>
        </select>
        <button type="submit" class="bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">Filtruoti</button>
        <?php if ($filterSource !== 'all' || $filterCategory !== 'all' || $filterSearch || $filterSort !== 'score'): ?>
        <a href="?" class="text-sm text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">✕ Valyti</a>
        <?php endif; ?>
    </form>

    <!-- Recent ads / Platform sections -->
    <?php foreach ($platformOrder as $platform):
        $platformAds = $grouped[$platform];
        $meta = $platformMeta[$platform];
        if (empty($platformAds)) continue;
    ?>
    <div class="card overflow-hidden">
        <!-- Platform header -->
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-slate-700">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-<?= $meta['color'] ?>-500"></span>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?= $meta['name'] ?></h3>
                <span class="text-xs text-gray-400 dark:text-slate-500"><?= count($platformAds) ?> skelbimų</span>
            </div>
            <a href="<?= $meta['url'] ?>" target="_blank" rel="noopener" class="text-sm text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300 transition-colors">
                View all →
            </a>
        </div>

        <!-- Ad rows -->
        <?php foreach ($platformAds as $idx => $ad): ?>
        <a href="<?= htmlspecialchars($ad['url']) ?>" target="_blank" rel="noopener"
           class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 dark:border-slate-800 last:border-0 hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors">
            <!-- Score badge -->
            <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-sm font-bold shrink-0 <?= scoreBg($ad['totalScore']) ?>">
                <?= number_format($ad['totalScore'], 0) ?>
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900 dark:text-white truncate"><?= htmlspecialchars($ad['title']) ?></div>
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-slate-500 mt-0.5">
                    <span><?= htmlspecialchars($ad['location']) ?></span>
                    <span><?= timeAgo($ad['date']) ?> prieš</span>
                    <?php if (!empty($ad['mileage'])): ?>
                    <span><?= number_format($ad['mileage'], 0, '', ' ') ?> km</span>
                    <?php endif; ?>
                    <?php if (!empty($ad['fuelType'])): ?>
                    <span><?= htmlspecialchars($ad['fuelType']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Category -->
            <span class="hidden sm:inline-block px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-xs text-gray-500 dark:text-slate-400"><?= categoryLabel($ad['category']) ?></span>
            <!-- Price -->
            <div class="text-right shrink-0">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">€<?= number_format($ad['price'], 0, ',', ' ') ?></span>
            </div>
            <!-- Arrow -->
            <svg class="w-4 h-4 text-gray-300 dark:text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if (empty($filtered)): ?>
    <div class="card text-center py-16">
        <p class="text-gray-400 dark:text-slate-500 text-base">Skelbimų nerasta</p>
        <p class="text-gray-300 dark:text-slate-600 text-sm mt-1">Pabandykite pakeisti filtrus</p>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
