<?php
/**
 * Platform metadata — shared across all pages
 */

$platformMeta = [
    'skelbiu' => [
        'name' => 'Skelbiu.lt',
        'icon' => '🟢',
        'color' => 'emerald',
        'url' => 'https://skelbiu.lt',
        'desc' => 'Didžiausia Lietuvos skelbimų svetainė',
        'page' => 'skelbiu.php',
    ],
    'autoplius' => [
        'name' => 'Autoplius.lt',
        'icon' => '🔵',
        'color' => 'blue',
        'url' => 'https://autoplius.lt',
        'desc' => 'Automobilių skelbimai ir rinka',
        'page' => 'autoplius.php',
    ],
    'aruodas' => [
        'name' => 'Aruodas.lt',
        'icon' => '🟠',
        'color' => 'orange',
        'url' => 'https://aruodas.lt',
        'desc' => 'Nekilnojamojo turto portalas',
        'page' => 'aruodas.php',
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
    if ($score >= 80) return 'text-emerald-600 dark:text-emerald-400';
    if ($score >= 60) return 'text-yellow-600 dark:text-yellow-400';
    if ($score >= 40) return 'text-orange-600 dark:text-orange-400';
    return 'text-red-600 dark:text-red-400';
}

function scoreBg(float $score): string {
    if ($score >= 80) return 'bg-emerald-50 dark:bg-emerald-500/20 border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400';
    if ($score >= 60) return 'bg-yellow-50 dark:bg-yellow-500/20 border-yellow-200 dark:border-yellow-500/30 text-yellow-700 dark:text-yellow-400';
    if ($score >= 40) return 'bg-orange-50 dark:bg-orange-500/20 border-orange-200 dark:border-orange-500/30 text-orange-700 dark:text-orange-400';
    return 'bg-red-50 dark:bg-red-500/20 border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400';
}

function categoryIcon(string $cat): string {
    return match ($cat) {
        'automobiliai' => '🚗',
        'nekilnojamasis' => '🏠',
        'elektronika' => '💻',
        default => '📦',
    };
}

function sourceIcon(string $source): string {
    return match ($source) {
        'skelbiu' => '🟢',
        'autoplius' => '🔵',
        'aruodas' => '🟠',
        default => '⚪',
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
