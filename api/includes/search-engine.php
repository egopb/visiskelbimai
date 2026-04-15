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

function buildSearchUrl(platform, p) {
    if (platform === 'skelbiu') {
        const cats = {'Automobiliai':'automobiliai','Kompiuteriai':'kompiuteriai','Telefonai':'telefonai','Buitinė technika':'buitine-technika','Baldai':'baldai'};
        const cities = {'Vilnius':465,'Kaunas':466,'Klaipėda':467,'Šiauliai':468,'Panevėžys':469};
        const q = new URLSearchParams();
        if (p['Raktažodis']) q.set('keywords', p['Raktažodis']);
        if (p['Miestas'] && p['Miestas'] !== 'Visa Lietuva' && cities[p['Miestas']]) q.set('cities', cities[p['Miestas']]);
        if (p['Kaina nuo']) q.set('price_from', p['Kaina nuo']);
        if (p['Kaina iki']) q.set('price_to', p['Kaina iki']);
        const cp = cats[p['Kategorija']] || '';
        const base = cp ? `https://www.skelbiu.lt/skelbimai/${cp}/` : 'https://www.skelbiu.lt/skelbimai/';
        return q.toString() ? base + '?' + q : base;
    }
    if (platform === 'autoplius') {
        const q = new URLSearchParams();
        if (p['Markė']) q.set('make_id_list', p['Markė']);
        if (p['Modelis']) q.set('model_id_list', p['Modelis']);
        if (p['Kaina nuo']) q.set('sell_price_from', p['Kaina nuo']);
        if (p['Kaina iki']) q.set('sell_price_to', p['Kaina iki']);
        if (p['Metai nuo']) q.set('make_date_from', p['Metai nuo']);
        if (p['Rida iki']) q.set('mileage_to', p['Rida iki']);
        const fuels = {'Dyzelis':1,'Benzinas':2,'Hibridas':5,'Elektra':3,'Dujos (LPG)':4};
        if (p['Kuro tipas'] && fuels[p['Kuro tipas']]) q.set('fuel_type_id', fuels[p['Kuro tipas']]);
        const gears = {'Automatinė':1,'Mechaninė':2};
        if (p['Pavarų dėžė'] && gears[p['Pavarų dėžė']]) q.set('gearbox_id', gears[p['Pavarų dėžė']]);
        return 'https://autoplius.lt/skelbimai/naudoti-automobiliai?' + q;
    }
    if (platform === 'aruodas') {
        const types = {'Butai pardavimui':'butai','Butai nuomai':'butu-nuoma','Namai pardavimui':'namai','Sklypai':'sklypai','Patalpos':'patalpos'};
        const cities = {'Vilnius':'vilniuje','Kaunas':'kaune','Klaipėda':'klaipedoje','Šiauliai':'siauliuose','Panevėžys':'panevezyje'};
        const tp = types[p['Tipas']] || 'butai';
        const ct = cities[p['Miestas']] || '';
        let url = `https://www.aruodas.lt/${tp}/`;
        if (ct) url += ct + '/';
        const q = new URLSearchParams();
        if (p['Kaina nuo']) q.set('price_from', p['Kaina nuo']);
        if (p['Kaina iki']) q.set('price_to', p['Kaina iki']);
        if (p['Kambariai nuo']) q.set('rooms_from', p['Kambariai nuo']);
        if (p['Plotas nuo']) q.set('area_from', p['Plotas nuo']);
        return q.toString() ? url + '?' + q : url;
    }
    return '#';
}

const emptyIcons = {'skelbiu':'🔍','autoplius':'🚗','aruodas':'🏠'};

function renderSearches() {
    const searches = loadSearches();
    const el = document.getElementById('searchList');
    if (!searches.length) {
        el.innerHTML = `
        <div class="card p-10 text-center">
            <div class="text-5xl mb-4">${emptyIcons[PLATFORM] || '🔍'}</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nėra aktyvių paieškų</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Sukurkite naują paiešką ir sistema periodiškai skenuos ${esc(META_NAME)}</p>
            <button onclick="document.getElementById('newSearchModal').classList.remove('hidden')"
                    class="bg-${META_COLOR}-600 hover:bg-${META_COLOR}-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Sukurti pirmą paiešką
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
        const url = s.searchUrl || buildSearchUrl(PLATFORM, s.params || {});
        const linkBtn = url && url !== '#' ? `<a href="${esc(url)}" target="_blank" rel="noopener" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Atidaryti ${esc(META_NAME)}">🔗</a>` : '';

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
                        <span>📋 ${s.foundAds || 0} skelbimų</span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    ${linkBtn}
                    <button onclick="openSearch('${s.id}')" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-${META_COLOR}-600 dark:hover:text-${META_COLOR}-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Atidaryti paieškos URL">🔍</button>
                    <button onclick="toggleSearch('${s.id}')" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="${s.active ? 'Sustabdyti' : 'Aktyvuoti'}">${s.active ? '⏸️' : '▶️'}</button>
                    <button onclick="deleteSearch('${s.id}')" class="p-2 rounded-lg text-gray-400 dark:text-slate-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors" title="Ištrinti">🗑️</button>
                </div>
            </div>
        </div>`;
    }).join('') + '</div>';
}

function createSearch(name, interval, params, category) {
    const searches = loadSearches();
    const url = buildSearchUrl(PLATFORM, params);
    searches.push({
        id: genId(), name, active: true,
        interval: parseInt(interval) || 60,
        params, category: category || null,
        created: new Date().toISOString(),
        lastScan: null, foundAds: 0, searchUrl: url
    });
    saveSearches(searches);
    renderSearches();
    document.getElementById('newSearchModal').classList.add('hidden');
    document.getElementById('searchForm').reset();
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

function openSearch(id) {
    const s = loadSearches().find(x => x.id === id);
    if (!s) return;
    const url = s.searchUrl || buildSearchUrl(PLATFORM, s.params || {});
    if (url && url !== '#') window.open(url, '_blank');
}

// Form handler
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const name = this.querySelector('[name="search_name"]').value.trim();
    if (!name) return;
    const interval = this.querySelector('[name="interval"]').value;
    const params = {};
    this.querySelectorAll('[data-param]').forEach(el => {
        const v = el.value.trim();
        if (v) params[el.dataset.param] = v;
    });
    const catEl = this.querySelector('[name="category"]');
    const category = catEl ? catEl.value : null;
    createSearch(name, interval, params, category);
});

// Initial render
renderSearches();
</script>
