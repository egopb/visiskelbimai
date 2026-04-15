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

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    <?php if (!isLoggedIn()): ?>
    <!-- Login Banner -->
    <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🔒</span>
            <div>
                <p class="text-sm font-medium">Prisijunkite, kad galėtumėte valdyti paieškas</p>
                <p class="text-xs text-slate-500">Skelbiu.lt, Autoplius.lt ir Aruodas.lt paieškų konfigūravimas</p>
            </div>
        </div>
        <a href="/login"
           class="px-5 py-2 rounded-lg text-sm font-medium bg-violet-600 hover:bg-violet-500 text-white transition-colors whitespace-nowrap">
            Prisijungti
        </a>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <div class="text-sm text-slate-500 mb-1">Viso skelbimų</div>
            <div class="text-2xl font-bold"><?= $totalAds ?></div>
            <div class="text-xs text-slate-600 mt-1">iš <?= count($sources) ?> platformų</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <div class="text-sm text-slate-500 mb-1">Šiandien naujų</div>
            <div class="text-2xl font-bold text-emerald-400"><?= $todayAds ?></div>
            <div class="text-xs text-slate-600 mt-1">per paskutines 24 val.</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <div class="text-sm text-slate-500 mb-1">Vid. kaina</div>
            <div class="text-2xl font-bold">€<?= number_format($avgPrice, 0, ',', ' ') ?></div>
            <div class="text-xs text-slate-600 mt-1">visų kategorijų</div>
        </div>
        <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-4">
            <div class="text-sm text-slate-500 mb-1">Top reitingas</div>
            <div class="text-2xl font-bold text-violet-400">
                <?= !empty($ads) ? number_format(max(array_column($ads, 'totalScore')), 1) : '0' ?>/100
            </div>
            <div class="text-xs text-slate-600 mt-1">geriausias pasiūlymas</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="get" class="flex flex-wrap gap-3 items-center">
        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($filterSearch) ?>"
                   placeholder="Ieškoti skelbimuose..."
                   class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 pl-10 text-sm
                          focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/50
                          placeholder:text-slate-600">
            <svg class="absolute left-3 top-3 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <!-- Source filter -->
        <select name="source"
                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-violet-500">
            <option value="all" <?= $filterSource === 'all' ? 'selected' : '' ?>>Visos platformos</option>
            <?php foreach ($sources as $s): ?>
            <option value="<?= $s ?>" <?= $filterSource === $s ? 'selected' : '' ?>>
                <?= sourceIcon($s) ?> <?= ucfirst($s) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Category filter -->
        <select name="category"
                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-violet-500">
            <option value="all" <?= $filterCategory === 'all' ? 'selected' : '' ?>>Visos kategorijos</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c ?>" <?= $filterCategory === $c ? 'selected' : '' ?>>
                <?= categoryIcon($c) ?> <?= ucfirst($c) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Sort -->
        <select name="sort"
                class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-violet-500">
            <option value="score" <?= $filterSort === 'score' ? 'selected' : '' ?>>⭐ Reitingas</option>
            <option value="price_asc" <?= $filterSort === 'price_asc' ? 'selected' : '' ?>>💰 Kaina ↑</option>
            <option value="price_desc" <?= $filterSort === 'price_desc' ? 'selected' : '' ?>>💰 Kaina ↓</option>
            <option value="date" <?= $filterSort === 'date' ? 'selected' : '' ?>>🕐 Naujausi</option>
        </select>

        <button type="submit"
                class="bg-violet-600 hover:bg-violet-500 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
            Filtruoti
        </button>

        <?php if ($filterSource !== 'all' || $filterCategory !== 'all' || $filterSearch || $filterSort !== 'score'): ?>
        <a href="?" class="text-sm text-slate-500 hover:text-slate-300 transition-colors">✕ Valyti</a>
        <?php endif; ?>
    </form>

    <!-- Results count -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Rodoma <span class="text-slate-300 font-medium"><?= $filteredCount ?></span> iš <?= $totalAds ?> skelbimų
        </p>
    </div>

    <?php if (empty($filtered)): ?>
    <div class="text-center py-20">
        <div class="text-4xl mb-4">🔍</div>
        <p class="text-slate-500 text-lg">Skelbimų nerasta</p>
        <p class="text-slate-600 text-sm mt-1">Pabandykite pakeisti filtrus</p>
    </div>
    <?php endif; ?>

    <!-- Platform Sections -->
    <?php foreach ($platformOrder as $platform):
        $platformAds = $grouped[$platform];
        $meta = $platformMeta[$platform];
        if (empty($platformAds)) continue;
        $platformAvg = count($platformAds) > 0 ? array_sum(array_column($platformAds, 'price')) / count($platformAds) : 0;
    ?>
    <section class="space-y-3">
        <!-- Platform Header -->
        <div class="flex items-center justify-between py-3 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-<?= $meta['color'] ?>-500/15 border border-<?= $meta['color'] ?>-500/25 flex items-center justify-center text-xl">
                    <?= $meta['icon'] ?>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold"><?= $meta['name'] ?></h2>
                        <span class="px-2 py-0.5 rounded-full bg-<?= $meta['color'] ?>-500/15 text-<?= $meta['color'] ?>-400 text-xs font-medium">
                            <?= count($platformAds) ?> skelb.
                        </span>
                    </div>
                    <p class="text-xs text-slate-500"><?= $meta['desc'] ?> · vid. €<?= number_format($platformAvg, 0, ',', ' ') ?></p>
                </div>
            </div>
            <a href="<?= $meta['url'] ?>" target="_blank" rel="noopener"
               class="text-xs text-slate-500 hover:text-slate-300 transition-colors hidden sm:block">
                Atidaryti <?= $meta['name'] ?> →
            </a>
        </div>

        <!-- Ads for this platform -->
        <div class="space-y-2">
            <?php foreach ($platformAds as $idx => $ad): ?>
            <a href="<?= htmlspecialchars($ad['url']) ?>" target="_blank" rel="noopener"
               class="card-hover block rounded-xl border border-slate-800 bg-slate-900/50 p-4 sm:p-5 hover:border-<?= $meta['color'] ?>-500/30">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                    <!-- Rank + Score -->
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-1 sm:w-16 shrink-0">
                        <div class="text-lg font-bold text-slate-500">#<?= $idx + 1 ?></div>
                        <div class="sm:w-full text-center px-2 py-1 rounded-lg border text-sm font-bold <?= scoreBg($ad['totalScore']) ?> <?= scoreColor($ad['totalScore']) ?>">
                            <?= number_format($ad['totalScore'], 0) ?>
                        </div>
                    </div>

                    <!-- Main info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2 mb-1.5">
                            <span class="text-base sm:text-lg font-semibold truncate"><?= htmlspecialchars($ad['title']) ?></span>
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-400">
                            <span>📍 <?= htmlspecialchars($ad['location']) ?></span>
                            <span>🕐 <?= timeAgo($ad['date']) ?> prieš</span>
                            <?php if (!empty($ad['mileage'])): ?>
                            <span>🛣️ <?= number_format($ad['mileage'], 0, '', ' ') ?> km</span>
                            <?php endif; ?>
                            <?php if (!empty($ad['fuelType'])): ?>
                            <span>⛽ <?= htmlspecialchars($ad['fuelType']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Category tag -->
                    <div class="sm:shrink-0">
                        <span class="px-2 py-0.5 rounded-md bg-slate-800 text-xs text-slate-400">
                            <?= categoryIcon($ad['category']) ?> <?= categoryLabel($ad['category']) ?>
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="sm:text-right sm:w-28 shrink-0">
                        <div class="text-xl font-bold">€<?= number_format($ad['price'], 0, ',', ' ') ?></div>
                    </div>
                </div>

                <!-- Score bars -->
                <div class="mt-3 pt-3 border-t border-slate-800/50 grid grid-cols-3 gap-3 text-xs">
                    <div>
                        <div class="flex justify-between text-slate-500 mb-1">
                            <span>💰 Kaina</span>
                            <span class="<?= scoreColor($ad['priceScore']) ?>"><?= $ad['priceScore'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="score-bar h-full rounded-full bg-gradient-to-r from-violet-500 to-indigo-500"
                                 style="width: <?= $ad['priceScore'] ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-slate-500 mb-1">
                            <span>⏰ Naujumas</span>
                            <span class="<?= scoreColor($ad['freshnessScore']) ?>"><?= $ad['freshnessScore'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="score-bar h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"
                                 style="width: <?= $ad['freshnessScore'] ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-slate-500 mb-1">
                            <span>📍 Vieta</span>
                            <span class="<?= scoreColor($ad['locationScore']) ?>"><?= $ad['locationScore'] ?></span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="score-bar h-full rounded-full bg-gradient-to-r from-amber-500 to-orange-500"
                                 style="width: <?= $ad['locationScore'] ?>%"></div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
