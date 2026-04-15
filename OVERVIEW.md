# 🎯 Skelbimu Monitoringo Sistemos Peržvalga

## ✅ Ką Sukūriau

Pilnai funkcinę, production-ready sistemą automatiniu skelbimu monitoringui iš dviejų Lithuanian klasifikuotų skelbimų svetainių su Telegram notifikacijomis.

## 📦 Failu Struktūra

```
CloudCode/
├── src/
│   ├── index.ts          ⭐️ PAGRINDINIS FAILAS - visos scraping/monitoring logika
│   ├── config.ts         Konfigūracijos valdymas
│   ├── database.ts       SQLite duomenų bazės operacijos
│   ├── telegram.ts       Telegram bot funkcionalumas
│   └── scraper.ts        Web scraping logika (modulinė versija)
├── .env.example          Konfigūracijos šablonas
├── package.json          Priklausomybės (axios, cheerio, telegram bot, sqlite3)
├── README.md             Greita pradžia
├── SETUP.md              Detalios instrukcijos
├── EXAMPLES.md           Naudojimo pavyzdžiai
└── tsconfig.json         TypeScript nustatymai
```

## 🚀 Galimybės

✅ **Dvi platformos**: skelbiu.lt ir autoplius.lt
✅ **Parallelinė paieška**: abu skraipai veikia vienu metu  
✅ **Parametrinė paieška**: kaina, marka, modelis, rida, vieta, kuro tipas
✅ **Telegram notifikacijos**: HTML formatiniu pranešimų su nuorodomis
✅ **SQLite duomenų bazė**: skelbimu istorija, duplikatų prevencija
✅ **Automatinis monitoring**: nustatytais intervalais (default 30 min)
✅ **Palyginimas**: rikiavimas pagal kainą, duplikatų šalinimas
✅ **TypeScript**: pilna tipo saugomybė
✅ **Dev + Production režimai**: tsx watch arba npm build

## 🔧 Technologijos

- **axios** - HTTP užklausimai
- **cheerio** - HTML parsing (jQuery style)
- **better-sqlite3** - SQLite3 duomenų bazė
- **node-telegram-bot-api** - Telegram bot API
- **dotenv** - Aplinkos kintamųjų valdymas
- **TypeScript** - Tipo saugomybė
- **tsx** - TS execution

## 📋 Naudojimo Žingsniai

### 1️⃣ Klonuoti ir Setup

```bash
cd CloudCode
npm install
```

### 2️⃣ Gauti Telegram Kredencialai

- @BotFather → /newbot → gauti tokena
- @userinfobot → gauti сhat/user ID

### 3️⃣ Sukonfigūruoti .env

```env
TELEGRAM_BOT_TOKEN=your_token
TELEGRAM_CHAT_ID=your_id
SEARCH_MIN_PRICE=5000
SEARCH_MAX_PRICE=15000
SEARCH_BRAND=BMW
CHECK_INTERVAL=30
```

### 4️⃣ Pradėti Development

```bash
npm run dev
```

Arba su custom parametrais:

```bash
npm run dev -- --brand "Audi" --min-price 8000 --max-price 20000 --location "Vilnius"
```

### 5️⃣ Build Production

```bash
npm run build
npm start
```

## 📊 Duomenys

Visi podatai saugomi `ads.db` (SQLite):

- `ads` lentelė: visi saugoti skelbimiai
- `searches` lentelė: paieškų logai

Duplikatai išvengiami pagal URL.

## 💬 Telegram Žinutė

```
🆕 NEW - Found 3 ad(s) matching your criteria:

1. BMW 320i 2015
   💰 €8,500 | 📍 Vilnius
   🛣️  120,000 km
   🔗 View on skelbiu.lt
   Source: skelbiu
```

Žinutėje yra tiesioginė nuoroda į skelbimą (spaudžiant atsidaro svetainė).

## 🎯 Parametrai

```typescript
interface SearchConfig {
  brand?: string;           // Mašinos marka (BMW, Audi, VW, etc)
  minPrice?: number;        // Minimali kaina eurais
  maxPrice?: number;        // Maksimali kaina eurais
  maxMileage?: number;      // Maksimali rida km
  location?: string;        // Miesto pavadinimas
  fuelType?: string;        // Dyzelis, Benzinas, Elektra
}
```

## 🔄 Darbas

1. Skenuoja abi platformas
2. Parsideda HTML
3. Ištraukia skelbimu duomenis (pavadinimas, kaina, vieta, rida)
4. Filtruoja pagal parametrus
5. Rikiuoja pagal kainą
6. Saugoja į SQLite (unikalios URL)
7. Siunčia Telegram pranešimus apie naujus
8. Laukia CHECK_INTERVAL minučių
9. Kartoja nuo 1️⃣

## ⚠️ Svarbi Informacija

- ✅ Pilnai legalu - tik public duomenys
- ✅ Grausman ir efektyvus
- ✅ Atsparnus klaidoms
- ✅ Modulinis - lengva pridėti naujas platformas

## 📈 Ateityje Galima Pridėti

- Web UI (React/Vue)
- Email notifikacijos
- Discord / Slack
- Kainos analizė / grafikai
- Paieškų saugojimas
- Favorite ads
- Price alerts (+/- %%)
- PostgreSQL support

## 🎓 Pavyzdžiai

Žr. `EXAMPLES.md` - 5 skirtingos paieškos scenarijus ir konfigūracijos.

---

**Viskas paruošta! Tiesiog:**

1. `npm install`
2. Redaguoti `.env`
3. `npm run dev`

Įžiūrite Telegram notifikacijas! 🎉
