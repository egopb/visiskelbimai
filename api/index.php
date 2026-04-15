<?php
/**
 * Skelbimu Monitoringo Dashboard
 * 
 * PHP frontend su Tailwind CSS — rodo skelbimų duomenis,
 * reitingus ir filtravimo galimybes.
 */

// Load mock data
$jsonPath = __DIR__ . '/../frontend/data/mock-ads.json';
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

// Platform display metadata
$platformMeta = [
    'skelbiu' => [
        'name' => 'Skelbiu.lt',
        'icon' => '🟢',
        'color' => 'emerald',
        'url' => 'https://skelbiu.lt',
        'desc' => 'Didžiausia Lietuvos skelbimų svetainė',
    ],
    'autoplius' => [
        'name' => 'Autoplius.lt',
        'icon' => '🔵',
        'color' => 'blue',
        'url' => 'https://autoplius.lt',
        'desc' => 'Automobilių skelbimai ir rinka',
    ],
    'aruodas' => [
        'name' => 'Aruodas.lt',
        'icon' => '🟠',
        'color' => 'orange',
        'url' => 'https://aruodas.lt',
        'desc' => 'Nekilnojamojo turto portalas',
    ],
];

// Helpers
function timeAgo(string $dateStr): string {
    $diff = time() - strtotime($dateStr);
    if ($diff < 3600) return floor($diff / 60) . ' min.';
    if ($diff < 86400) return floor($diff / 3600) . ' val.';
    return floor($diff / 86400) . ' d.';
}

function scoreColor(float $score): string {
    if ($score >= 80) return 'text-emerald-400';
    if ($score >= 60) return 'text-yellow-400';
    if ($score >= 40) return 'text-orange-400';
    return 'text-red-400';
}

function scoreBg(float $score): string {
    if ($score >= 80) return 'bg-emerald-500/20 border-emerald-500/30';
    if ($score >= 60) return 'bg-yellow-500/20 border-yellow-500/30';
    if ($score >= 40) return 'bg-orange-500/20 border-orange-500/30';
    return 'bg-red-500/20 border-red-500/30';
}

function sourceIcon(string $source): string {
    return match ($source) {
        'skelbiu' => '🟢',
        'autoplius' => '🔵',
        'aruodas' => '🟠',
        default => '⚪',
    };
}

function categoryIcon(string $cat): string {
    return match ($cat) {
        'automobiliai' => '🚗',
        'nekilnojamasis' => '🏠',
        'elektronika' => '💻',
        default => '📦',
    };
}

function categoryLabel(string $cat): string {
    return match ($cat) {
        'automobiliai' => 'Auto',
        'nekilnojamasis' => 'NT',
        'elektronika' => 'Tech',
        default => $cat,
    };
}
?>
<!DOCTYPE html>
<html lang="lt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skelbimu Monitoringas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .score-bar { transition: width 0.6s ease-out; }
        .card-hover { transition: all 0.2s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

<!-- Header -->
<header class="border-b border-slate-800 glass sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-lg font-bold shadow-lg shadow-violet-500/25">
                    S
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">Skelbimu Monitoringas</h1>
                    <p class="text-xs text-slate-500">skelbiu.lt · autoplius.lt · aruodas.lt</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 text-sm text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                    Paskutinis skenavimas: <?= timeAgo($lastScan) ?> prieš
                </div>
                <div class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-400 font-medium">
                    <?= $scanCount ?> skenavimai
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

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

<!-- Footer -->
<footer class="border-t border-slate-800 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-600">
        <span>Skelbimu Monitoringas v1.0 — CloudCode</span>
        <span>Mock data · <?= date('Y-m-d H:i') ?></span>
    </div>
</footer>

</body>
</html>
