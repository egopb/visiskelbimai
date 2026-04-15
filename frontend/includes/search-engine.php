<script>
const PLATFORM = '<?= $platform ?>';
const META_COLOR = '<?= $meta['color'] ?>';
const META_NAME = '<?= $meta['name'] ?>';
const STORAGE_KEY = 'vs_searches_' + PLATFORM;

function loadSearches() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
    catch { return []; }
}
function saveSearches(arr) { localStorage.setItem(STORAGE_KEY, JSON.stringify(arr)); }
function genId() { return Date.now().toString(36) + Math.random().toString(36).substr(2, 8); }

function timeAgo(iso) {
    if (!iso) return 'Dar neskenuota';
    const s = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);
    if (s < 3600) return Math.floor(s / 60) + ' min. prieš';
    if (s < 86400) return Math.floor(s / 3600) + ' val. prieš';
    return Math.floor(s / 86400) + ' d. prieš';
}

function intervalLabel(iv) {
    iv = parseInt(iv);
    if (iv < 60) return iv + ' min.';
    if (iv < 1440) return (iv / 60) + ' val.';
    return (iv / 1440) + ' d.';
}

function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

// ── URL → parametrų parsing ──────────────────────────
function parseSearchUrl(rawUrl) {
    try { var u = new URL(rawUrl); } catch { return null; }
    const host = u.hostname.replace('www.', '');
    const q = u.searchParams;
    const path = u.pathname;

    if (host.includes('skelbiu.lt')) {
        const p = {};
        if (q.get('keywords')) p['Raktažodis'] = q.get('keywords');
        if (q.get('price_from')) p['Kaina nuo'] = q.get('price_from') + ' €';
        if (q.get('price_to')) p['Kaina iki'] = q.get('price_to') + ' €';
        const cityRev = {'465':'Vilnius','466':'Kaunas','467':'Klaipėda','468':'Šiauliai','469':'Panevėžys'};
        if (q.get('cities') && cityRev[q.get('cities')]) p['Miestas'] = cityRev[q.get('cities')];
        const catMap = {'automobiliai':'Automobiliai','kompiuteriai':'Kompiuteriai','telefonai':'Telefonai','buitine-technika':'Buitinė technika','baldai':'Baldai','drabuziai':'Drabužiai','kita':'Kita'};
        const seg = path.split('/').filter(Boolean);
        if (seg.length >= 2) { const c = catMap[seg[1]] || seg[1]; p['Kategorija'] = c; }
        const name = p['Raktažodis'] || p['Kategorija'] || 'Skelbiu.lt paieška';
        return { platform: 'skelbiu', name, params: p, category: p['Kategorija'] || null, url: rawUrl };
    }

    if (host.includes('autoplius.lt')) {
        const p = {};
        const fuelRev = {'1':'Dyzelis','2':'Benzinas','3':'Elektra','4':'Dujos (LPG)','5':'Hibridas'};
        const gearRev = {'1':'Automatinė','2':'Mechaninė'};
        if (q.get('make_id_list') || q.get('make_id')) p['Markė'] = q.get('make_id_list') || q.get('make_id');
        if (q.get('model_id_list') || q.get('model_id')) p['Modelis'] = q.get('model_id_list') || q.get('model_id');
        if (q.get('sell_price_from')) p['Kaina nuo'] = q.get('sell_price_from') + ' €';
        if (q.get('sell_price_to')) p['Kaina iki'] = q.get('sell_price_to') + ' €';
        if (q.get('make_date_from')) p['Metai nuo'] = q.get('make_date_from');
        if (q.get('make_date_to')) p['Metai iki'] = q.get('make_date_to');
        if (q.get('mileage_to')) p['Rida iki'] = q.get('mileage_to') + ' km';
        if (q.get('fuel_type_id') && fuelRev[q.get('fuel_type_id')]) p['Kuras'] = fuelRev[q.get('fuel_type_id')];
        if (q.get('gearbox_id') && gearRev[q.get('gearbox_id')]) p['Pavarų dėžė'] = gearRev[q.get('gearbox_id')];
        // Parse branded URLs like /skelbimai/naudoti-automobiliai/volkswagen/passat
        const seg = path.split('/').filter(Boolean);
        if (seg.length >= 3 && !p['Markė']) p['Markė'] = decodeURIComponent(seg[2]);
        if (seg.length >= 4 && !p['Modelis']) p['Modelis'] = decodeURIComponent(seg[3]);
        const parts = []; if (p['Markė']) parts.push(p['Markė']); if (p['Modelis']) parts.push(p['Modelis']);
        const name = parts.length ? parts.join(' ') : 'Autoplius paieška';
        return { platform: 'autoplius', name, params: p, category: null, url: rawUrl };
    }

    if (host.includes('aruodas.lt')) {
        const p = {};
        const typeRev = {'butai':'Butai pardavimui','butu-nuoma':'Butai nuomai','namai':'Namai pardavimui','sklypai':'Sklypai','patalpos':'Patalpos','namu-nuoma':'Namų nuoma'};
        const cityRev = {'vilniuje':'Vilnius','kaune':'Kaunas','klaipedoje':'Klaipėda','siauliuose':'Šiauliai','panevezyje':'Panevėžys'};
        const seg = path.split('/').filter(Boolean);
        if (seg[0] && typeRev[seg[0]]) p['Tipas'] = typeRev[seg[0]];
        else if (seg[0]) p['Tipas'] = seg[0];
        if (seg[1] && cityRev[seg[1]]) p['Miestas'] = cityRev[seg[1]];
        else if (seg[1] && !seg[1].match(/^\d/)) p['Rajonas'] = decodeURIComponent(seg[1]);
        if (q.get('price_from')) p['Kaina nuo'] = q.get('price_from') + ' €';
        if (q.get('price_to')) p['Kaina iki'] = q.get('price_to') + ' €';
        if (q.get('rooms_from')) p['Kambariai nuo'] = q.get('rooms_from');
        if (q.get('area_from')) p['Plotas nuo'] = q.get('area_from') + ' m²';
        if (q.get('FOrder')) p['Rūšiavimas'] = q.get('FOrder');
        const parts = []; if (p['Tipas']) parts.push(p['Tipas']); if (p['Miestas']) parts.push(p['Miestas']);
        const name = parts.length ? parts.join(' — ') : 'Aruodas paieška';
        return { platform: 'aruodas', name, params: p, category: p['Tipas'] || null, url: rawUrl };
    }

    return null;
}

function detectPlatform(rawUrl) {
    try { var u = new URL(rawUrl); } catch { return null; }
    const h = u.hostname.replace('www.', '');
    if (h.includes('skelbiu.lt')) return 'skelbiu';
    if (h.includes('autoplius.lt')) return 'autoplius';
    if (h.includes('aruodas.lt')) return 'aruodas';
    return null;
}

// ── Rendering ──────────────────────────
const emptyIcons = {'skelbiu':'🔍','autoplius':'🚗','aruodas':'🏠'};

function renderSearches() {
    const searches = loadSearches();
    const el = document.getElementById('searchList');
    if (!searches.length) {
        el.innerHTML = `
        <div class="card p-10 text-center">
            <div class="text-5xl mb-4">${emptyIcons[PLATFORM] || '🔍'}</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nėra aktyvių paieškų</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Nukopijuokite paieškos nuorodą iš ${esc(META_NAME)} ir sistema stebės naujus skelbimus</p>
            <button onclick="document.getElementById('newSearchModal').classList.remove('hidden')"
                    class="bg-${META_COLOR}-600 hover:bg-${META_COLOR}-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Pridėti paiešką
            </button>
        </div>`;
        return;
    }
    el.innerHTML = '<div class="space-y-3">' + searches.map(s => {
        const params = Object.entries(s.params || {}).map(([k,v]) =>
            `<span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-slate-800 text-xs text-gray-600 dark:text-slate-300"><span class="text-gray-400 dark:text-slate-500">${esc(k)}:</span> ${esc(v)}</span>`
        ).join('');
        const catBadge = s.category ? `<span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs">${esc(s.category)}</span>` : '';
        const statusBadge = s.active
            ? '<span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-500/25">Aktyvus</span>'
            : '<span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-xs font-medium">Sustabdytas</span>';
        const url = s.searchUrl || '#';

        return `<div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${esc(s.name)}</h3>
                        ${statusBadge} ${catBadge}
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">${params}</div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-400 dark:text-slate-500">
                        <span>🔄 Kas ${intervalLabel(s.interval || 60)}</span>
                        <span>🕐 ${timeAgo(s.lastScan)}</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="${esc(url)}" target="_blank" rel="noopener" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Atidaryti ${esc(META_NAME)}">🔗</a>
                    <button onclick="toggleSearch('${s.id}')" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="${s.active ? 'Sustabdyti' : 'Aktyvuoti'}">${s.active ? '⏸️' : '▶️'}</button>
                    <button onclick="deleteSearch('${s.id}')" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Ištrinti">🗑️</button>
                </div>
            </div>
        </div>`;
    }).join('') + '</div>';
}

// ── CRUD ──────────────────────────
function createSearchFromUrl(rawUrl, customName, interval) {
    const parsed = parseSearchUrl(rawUrl);
    if (!parsed) { alert('Nepavyko atpažinti nuorodos. Palaikomi: skelbiu.lt, autoplius.lt, aruodas.lt'); return false; }
    if (parsed.platform !== PLATFORM) {
        alert(`Ši nuoroda yra iš ${parsed.platform}, o jūs esate ${META_NAME} puslapyje.`);
        return false;
    }
    const searches = loadSearches();
    searches.push({
        id: genId(),
        name: customName || parsed.name,
        active: true,
        interval: parseInt(interval) || 60,
        params: parsed.params,
        category: parsed.category,
        created: new Date().toISOString(),
        lastScan: null,
        foundAds: 0,
        searchUrl: rawUrl.trim()
    });
    saveSearches(searches);
    renderSearches();
    return true;
}

function toggleSearch(id) {
    const arr = loadSearches();
    const s = arr.find(x => x.id === id);
    if (s) { s.active = !s.active; saveSearches(arr); renderSearches(); }
}

function deleteSearch(id) {
    if (!confirm('Tikrai ištrinti šią paiešką?')) return;
    saveSearches(loadSearches().filter(x => x.id !== id));
    renderSearches();
}

// ── URL paste preview ──────────────────────────
const urlInput = document.getElementById('searchUrl');
const previewBox = document.getElementById('urlPreview');
const nameInput = document.querySelector('[name="search_name"]');

if (urlInput) {
    urlInput.addEventListener('input', function() {
        const raw = this.value.trim();
        if (!raw) { previewBox.classList.add('hidden'); return; }
        const parsed = parseSearchUrl(raw);
        if (!parsed) {
            previewBox.innerHTML = '<p class="text-red-500 dark:text-red-400 text-xs">⚠️ Nepalaikoma nuoroda</p>';
            previewBox.classList.remove('hidden');
            return;
        }
        if (parsed.platform !== PLATFORM) {
            previewBox.innerHTML = `<p class="text-orange-500 dark:text-orange-400 text-xs">⚠️ Ši nuoroda yra iš <b>${parsed.platform}</b>. Puslapyje: <b>${PLATFORM}</b></p>`;
            previewBox.classList.remove('hidden');
            return;
        }
        // Auto-fill name
        if (!nameInput.value || nameInput.dataset.autoFilled === 'true') {
            nameInput.value = parsed.name;
            nameInput.dataset.autoFilled = 'true';
        }
        // Show parsed params
        const tags = Object.entries(parsed.params).map(([k,v]) =>
            `<span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-xs text-gray-600 dark:text-slate-300">${esc(k)}: ${esc(v)}</span>`
        ).join(' ');
        previewBox.innerHTML = `<p class="text-xs text-emerald-600 dark:text-emerald-400 mb-1.5">✅ Atpažinta: ${esc(parsed.name)}</p><div class="flex flex-wrap gap-1.5">${tags}</div>`;
        previewBox.classList.remove('hidden');
    });
    // Clear autoFilled flag on manual name edit
    if (nameInput) nameInput.addEventListener('input', function() { this.dataset.autoFilled = 'false'; });
}

// Form submit
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const url = document.getElementById('searchUrl').value.trim();
    if (!url) { alert('Įklijuokite paieškos nuorodą'); return; }
    const name = nameInput.value.trim();
    const interval = this.querySelector('[name="interval"]').value;
    if (createSearchFromUrl(url, name, interval)) {
        this.reset();
        previewBox.classList.add('hidden');
        document.getElementById('newSearchModal').classList.add('hidden');
    }
});

// Initial render
renderSearches();
</script>
