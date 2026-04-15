# Skelbiu.lt & Autoplius.lt Monitoring System - Setup Guide

Pilna sistema automatiniu skelbimu monitoringui iš dviejų populiariausiu Lithuanian klasifikuotų skelbimų platformų su Telegram notifikacijomis.

## ✨ Funkcijos

- 🚗 **Dvi platformos**: skelbiu.lt ir autoplius.lt
- 🔍 **Paieška pagal parametrus**: kaina, marka, vieta, rida
- 💬 **Telegram notifikacijos**: iš karto pranešimu apie naujus skelbimuš
- 💾 **Duomenų bazė**: SQLite saugojimas su istorija
- ⚡ **Automatinis monitoring**: nustatytas intervalas
- 📊 **Palyginimas**: rikiavimas pagal kaina, duplikatų salinavimas

## 📋 Reikalavimai

- Node.js 16+
- npm arba yarn
- Telegram bot token
- Telegram chat ID

## 🚀 Diegimas

### 1. Klonuoti ir diegti priklausomybes

```bash
cd CloudCode
npm install
```

### 2. Sukonfigūruoti Telegram

**Gauti Telegram bot tokena:**
- Atidarykite [@BotFather](https://t.me/botfather) Telegramoje
- Naudokite `/newbot` komandą
- Laikykitės instrukcijų ir gaukite tokena

**Gauti savo chat ID:**
- Atidarykite [@userinfobot](https://t.me/userinfobot) Telegramoje
- Jis parodys jūsų user ID (arba chat ID iš grupes)

### 3. Konfigūracija - sukurti `.env` failą

Kopijuoti ``.env.example`` į `.env`:

```bash
cp .env.example .env
```

Redaguoti `.env` ir įvesti jūsų duomenis:

```env
# Telegram konfiguracija
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here

# Paieškos parametrai - automobiliai
SEARCH_MIN_PRICE=5000          # Minimali kaina eurais
SEARCH_MAX_PRICE=15000         # Maksimali kaina eurais
SEARCH_BRAND=BMW               # Markė (pvz: BMW, Audi, Volkswagen)
SEARCH_MODEL=3                 # Modelis (pvz: 3, 5, 7)

# Tikrinimo intervalas (minutėmis)
CHECK_INTERVAL=30

# Duomenų bazė
DB_PATH=./ads.db
```

## 🎯 Pradžia

### Režimas 1: Tiesiog pradėti su numatytaisiais parametrais

```bash
npm run dev
```

### Režimas 2: Custom parametrai iš CLI

```bash
npm run dev -- --brand "Audi" --min-price 8000 --max-price 20000 --location "Kaunas"
```

**Galimi parametrai:**
- `--brand` - mašinos marka
- `--min-price` - minimali kaina
- `--max-price` - maksimali kaina
- `--max-mileage` - maksimali rida (km)
- `--location` - vieta
- `--fuel` - kuro tipas (benzinas, dyzelis, elektra)

### Režimas 3: Production

```bash
npm run build
npm start
```

## 📊 Duomenų bazė

Visa informacija saugoma `ads.db` SQLite duomenų bazėje:

**Lentelės:**
- `ads` - visi saugoti skelbimiai
- `searches` - paieškų istorija

## 🔄 Darbas

Kai sistema pradedama:

1. ✅ Tuoj pat skenuoja abi platformas
2. 📥 Gauna skelbimuš pagal nustatytus parametrus
3. 🗂️ Įrašo į duomenų bazę
4. 📧 Siunčia Telegram pranešimą apie naujus skelbimuš
5. ⏱️ Laukia 30 minučiu (arba nustatyto intervalo)
6. 🔁 Kartoja procesą begale

## 📝 Telegram Pranešimas

Pranešimas turi šią informaciją:

```
🆕 NEW - Found 3 ad(s) matching your criteria:

1. BMW 320i 2015
   💰 €8,500 | 📍 Vilnius
   🛣️  120,000 km
   🔗 View on skelbiu.lt
   Source: skelbiu

2. BMW 330d 2018
   💰 €11,200 | 📍 Kaunas
   🛣️  95,500 km
   🔗 View on autoplius.lt
   Source: autoplius
...
```

Spaudžiant nuorodą, atsidaro skelbimas tiesiais į svetainę.

## ⚙️ Papildomos komandos

```bash
# Tik build'inti
npm run build

# TypeScript check
npm run lint

# Išvalyti duomenų bazę
rm ads.db

# Išvalyti node_modules
rm -r node_modules
npm install
```

## 🔧 Problemos ir sprendimai

### "Telegram bot not configured"
- Patikrinkite, ar `.env` failas sukurtas
- Patikrinkite `TELEGRAM_BOT_TOKEN` ir `TELEGRAM_CHAT_ID` yra teisingi
- Nėra tarpų arba žymu

### "Failed to send test message"
- Tikrinkite Telegram bot pradėjo pokalbį
- Tikrinkite, ar API nėra sutrukdyta
- Permaitykite bot: `/start` BotFather'by

### "No ads found"
- Gali būti, kad nėra skelbimu pagal jūsų kriteririjus
- Pabandykite sumažinti maksimalią kainą
- Tikrinkite, ar serveriai nepasikeite

## 📚 Architektūra

```
src/
├── index.ts          # Pagrindinis failas (scraping + monitoring)
├── config.ts         # Konfigūracijos valdymas (neaktyvus šioje versijoje)
├── database.ts       # DB operacijos (neaktyvus šioje versijoje)
├── telegram.ts       # Telegram bot (neaktyvus šioje versijoje)
└── scraper.ts        # Web scraping logika (neaktyvus šioje versijoje)
```

Šioje versijoje yra vienas `index.ts` failas su visa logika - paprastinant.

## 🚀 Ateityje

- [ ] Web UI prieiga
- [ ] PostgreSQL duomenų bazė
- [ ] Discord / Slack integracija
- [ ] Email pranešimai
- [ ] Push notifikacijos
- [ ] Kainos tendencijos analizė
- [ ] Pavertimas iš svetainių API (jei jie duos)

## 📄 Licencija

MIT

## ⚠️ Atsakomybė

Naudojant šią sistemą sutinkate su svetainių "Terms of Service" ir "Robots.txt" taisyklėmis. Autoriai neatsakingi už:
- Svetainių strukūrų pokeitius
- IP blokirvimo problemėse
- Duomenų netikslumuose

Naudokite atsakingai!
