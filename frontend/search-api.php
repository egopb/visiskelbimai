<?php
/**
 * Search CRUD API — handles create, toggle, delete, scan operations
 * Called via AJAX from search pages (skelbiu, autoplius, aruodas)
 */
require_once __DIR__ . '/includes/auth.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';
$platform = $_POST['platform'] ?? '';

if (!in_array($platform, ['skelbiu', 'autoplius', 'aruodas'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nežinoma platforma']);
    exit;
}

$dataDir = __DIR__ . '/data/searches';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$file = "$dataDir/$platform.json";

function loadSearches(string $file): array {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return $data['searches'] ?? [];
}

function saveSearches(string $file, array $searches): void {
    file_put_contents($file, json_encode(
        ['searches' => array_values($searches)],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    ));
}

function buildSearchUrl(string $platform, array $params): string {
    switch ($platform) {
        case 'skelbiu':
            $q = [];
            if (!empty($params['Raktažodis'])) $q['keywords'] = $params['Raktažodis'];
            if (!empty($params['Miestas']) && $params['Miestas'] !== 'Visa Lietuva') {
                $cities = ['Vilnius' => 465, 'Kaunas' => 466, 'Klaipėda' => 467, 'Šiauliai' => 468, 'Panevėžys' => 469];
                if (isset($cities[$params['Miestas']])) $q['cities'] = $cities[$params['Miestas']];
            }
            if (!empty($params['Kaina nuo'])) $q['price_from'] = (int)$params['Kaina nuo'];
            if (!empty($params['Kaina iki'])) $q['price_to'] = (int)$params['Kaina iki'];
            $categoryPaths = [
                'Automobiliai' => 'automobiliai',
                'Kompiuteriai' => 'kompiuteriai',
                'Telefonai' => 'telefonai',
                'Buitinė technika' => 'buitine-technika',
                'Baldai' => 'baldai',
            ];
            $catPath = $categoryPaths[$params['Kategorija'] ?? ''] ?? '';
            $base = $catPath ? "https://www.skelbiu.lt/skelbimai/$catPath/" : 'https://www.skelbiu.lt/skelbimai/';
            return $base . ($q ? '?' . http_build_query($q) : '');

        case 'autoplius':
            $q = [];
            if (!empty($params['Markė'])) $q['make_id_list'] = $params['Markė'];
            if (!empty($params['Modelis'])) $q['model_id_list'] = $params['Modelis'];
            if (!empty($params['Kaina nuo'])) $q['sell_price_from'] = (int)$params['Kaina nuo'];
            if (!empty($params['Kaina iki'])) $q['sell_price_to'] = (int)$params['Kaina iki'];
            if (!empty($params['Metai nuo'])) $q['make_date_from'] = $params['Metai nuo'];
            if (!empty($params['Rida iki'])) $q['mileage_to'] = (int)$params['Rida iki'];
            if (!empty($params['Kuro tipas'])) {
                $fuelMap = ['Dyzelis' => 1, 'Benzinas' => 2, 'Hibridas' => 5, 'Elektra' => 3, 'Dujos (LPG)' => 4];
                if (isset($fuelMap[$params['Kuro tipas']])) $q['fuel_type_id'] = $fuelMap[$params['Kuro tipas']];
            }
            if (!empty($params['Pavarų dėžė'])) {
                $gearMap = ['Automatinė' => 1, 'Mechaninė' => 2];
                if (isset($gearMap[$params['Pavarų dėžė']])) $q['gearbox_id'] = $gearMap[$params['Pavarų dėžė']];
            }
            return 'https://autoplius.lt/skelbimai/naudoti-automobiliai?' . http_build_query($q);

        case 'aruodas':
            $typeMap = [
                'Butai pardavimui' => 'butai',
                'Butai nuomai' => 'butu-nuoma',
                'Namai pardavimui' => 'namai',
                'Sklypai' => 'sklypai',
                'Patalpos' => 'patalpos',
            ];
            $type = $typeMap[$params['Tipas'] ?? 'Butai pardavimui'] ?? 'butai';
            $cityMap = [
                'Vilnius' => 'vilniuje',
                'Kaunas' => 'kaune',
                'Klaipėda' => 'klaipedoje',
                'Šiauliai' => 'siauliuose',
                'Panevėžys' => 'panevezyje',
            ];
            $city = $cityMap[$params['Miestas'] ?? ''] ?? '';
            $url = "https://www.aruodas.lt/$type/";
            if ($city) $url .= "$city/";
            $q = [];
            if (!empty($params['Kaina nuo'])) $q['price_from'] = (int)$params['Kaina nuo'];
            if (!empty($params['Kaina iki'])) $q['price_to'] = (int)$params['Kaina iki'];
            if (!empty($params['Kambariai nuo'])) $q['rooms_from'] = (int)$params['Kambariai nuo'];
            if (!empty($params['Plotas nuo'])) $q['area_from'] = (int)$params['Plotas nuo'];
            return $q ? $url . '?' . http_build_query($q) : $url;
    }
    return '';
}

function scanPlatform(string $url): int {
    if (!function_exists('curl_init')) return -1;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_HTTPHEADER => ['Accept-Language: lt'],
    ]);
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (!$html || $httpCode !== 200) return -1;
    $patterns = [
        '/Rasta[:\s]+(\d[\d\s]*)\s*skelb/iu',
        '/(\d[\d\s]*)\s*skelb/iu',
        '/Iš viso[:\s]+(\d[\d\s]*)/iu',
        '/count["\s:]+(\d+)/i',
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $html, $m)) {
            return (int)preg_replace('/\s/', '', $m[1]);
        }
    }
    return 0;
}

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            echo json_encode(['error' => 'Pavadinimas privalomas']);
            exit;
        }
        $searches = loadSearches($file);
        $params = json_decode($_POST['params'] ?? '{}', true) ?: [];
        $newSearch = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'active' => true,
            'interval' => max(5, (int)($_POST['interval'] ?? 60)),
            'params' => $params,
            'created' => date('c'),
            'lastScan' => null,
            'foundAds' => 0,
            'searchUrl' => buildSearchUrl($platform, $params),
        ];
        if (!empty($_POST['category'])) {
            $newSearch['category'] = $_POST['category'];
        }
        $searches[] = $newSearch;
        saveSearches($file, $searches);
        echo json_encode(['success' => true, 'search' => $newSearch]);
        break;

    case 'toggle':
        $id = $_POST['id'] ?? '';
        $searches = loadSearches($file);
        $found = false;
        foreach ($searches as &$s) {
            if ($s['id'] === $id) {
                $s['active'] = !$s['active'];
                $found = true;
                break;
            }
        }
        unset($s);
        if ($found) saveSearches($file, $searches);
        echo json_encode(['success' => $found]);
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';
        $searches = loadSearches($file);
        $before = count($searches);
        $searches = array_values(array_filter($searches, fn($s) => $s['id'] !== $id));
        saveSearches($file, $searches);
        echo json_encode(['success' => count($searches) < $before]);
        break;

    case 'scan':
        $id = $_POST['id'] ?? '';
        $searches = loadSearches($file);
        $result = ['success' => false];
        foreach ($searches as &$s) {
            if ($s['id'] === $id) {
                $url = buildSearchUrl($platform, $s['params']);
                $count = scanPlatform($url);
                $s['lastScan'] = date('c');
                if ($count >= 0) $s['foundAds'] = $count;
                $s['searchUrl'] = $url;
                $result = ['success' => true, 'url' => $url, 'count' => $count];
                break;
            }
        }
        unset($s);
        saveSearches($file, $searches);
        echo json_encode($result);
        break;

    case 'scan-all':
        $searches = loadSearches($file);
        $scanned = 0;
        foreach ($searches as &$s) {
            if (!$s['active']) continue;
            $url = buildSearchUrl($platform, $s['params']);
            $count = scanPlatform($url);
            $s['lastScan'] = date('c');
            if ($count >= 0) $s['foundAds'] = $count;
            $s['searchUrl'] = $url;
            $scanned++;
        }
        unset($s);
        saveSearches($file, $searches);
        echo json_encode(['success' => true, 'scanned' => $scanned]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Nežinomas veiksmas']);
}
