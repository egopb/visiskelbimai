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
    if ($score >= 80) return 'text-emerald-600';
    if ($score >= 60) return 'text-yellow-600';
    if ($score >= 40) return 'text-orange-600';
    return 'text-red-600';
}

function scoreBg(float $score): string {
    if ($score >= 80) return 'bg-emerald-50 border-emerald-200 text-emerald-700';
    if ($score >= 60) return 'bg-yellow-50 border-yellow-200 text-yellow-700';
    if ($score >= 40) return 'bg-orange-50 border-orange-200 text-orange-700';
    return 'bg-red-50 border-red-200 text-red-700';
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
