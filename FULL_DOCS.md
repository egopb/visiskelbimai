# 📚 Multi-Category Monitoring System - Pilna Dokumentacija

## 🎯 Sistemos Apžvalga

Tai automatinė sistema monitoringui iš trijų svarbiausių Lithuanian klasifikuotų skelbimų platformų su prioritetizuotais tikrinimo intervalais:

| Kategorija | Platformos | Intervalas | Prioritetas | Aprašymas |
|-----------|-----------|-----------|-----------|-----------|
| 🚗 **Automobiliai** | skelbiu.lt, autoplius.lt | 15-20 min | ⭐⭐⭐ | Aukščiausias prioritetas - tikrinimas labai dažnai |
| 🏠 **Nekilnojamasis turtas** | skelbiu.lt, aruodas.lt | 45-60 min | ⭐⭐ | Vidutinis prioritetas - rečiau nei automobiliai |
| 💻 **Elektronika** | skelbiu.lt | 120+ min | ⭐ | Žemiausias prioritetas - retai keičiasi |

## 🛠️ Diegimas ir Konfigūracija

### 1. Klonuoti ir diegti

```bash
cd CloudCode
npm install
```

### 2. Sukonfigūruoti Telegram

Gauti iš [@BotFather](https://t.me/botfather):
- `TELEGRAM_BOT_TOKEN` - bot'o token
- `TELEGRAM_CHAT_ID` - jūsų chat ID iš [@userinfobot](https://t.me/userinfobot)

### 3. Redaguoti `.env` failą

Kopijuoti `.env.example`:
```bash
cp .env.example .env
```

Redaguoti visus parametrus pagal jūsų poreikius.

## ⚙️ .env Konfigūracija (Detali)

### TELEGRAM

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_CHAT_ID=987654321
```

### AUTOMOBILIAI (Priority 1) ⭐⭐⭐

Tikrinamas kas **15 minučių** iš dalies, kas **20 minučių** iš dalies.

**Skelbiu.lt paieška:**
```env
CARS_ENABLED=true
CARS_CHECK_INTERVAL=15
CARS_SKELBIU_ENABLED=true
CARS_SKELBIU_BRAND=BMW          # Marka (BMW, Audi, VW, etc)
CARS_SKELBIU_MIN_PRICE=5000     # Min kaina eurais
CARS_SKELBIU_MAX_PRICE=15000    # Max kaina eurais
CARS_SKELBIU_MAX_MILEAGE=150000 # Max rida kilometrais
CARS_SKELBIU_LOCATION=Vilnius   # Miesto pavadinimas
```

**Autoplius.lt paieška:**
```env
CARS_AUTOPLIUS_ENABLED=true
CARS_AUTOPLIUS_BRAND=Audi
CARS_AUTOPLIUS_MIN_PRICE=5000
CARS_AUTOPLIUS_MAX_PRICE=20000
CARS_AUTOPLIUS_MAX_MILEAGE=200000
```

**Pavyzdžiai:**

*BMW iš Vilniaus, 5-15 tūkstančių:*
```env
CARS_CHECK_INTERVAL=15
CARS_SKELBIU_BRAND=BMW
CARS_SKELBIU_MIN_PRICE=5000
CARS_SKELBIU_MAX_PRICE=15000
CARS_SKELBIU_LOCATION=Vilnius
```

*Bet kokie automobiliai, pigūs:*
```env
CARS_CHECK_INTERVAL=20
CARS_SKELBIU_MIN_PRICE=2000
CARS_SKELBIU_MAX_PRICE=5000
CARS_SKELBIU_MAX_MILEAGE=200000
```

### NEKILNOJAMASIS TURTAS (Priority 2) ⭐⭐

Tikrinamas kas **45-60 minučių**.

**Skelbiu.lt paieška:**
```env
REALESTATE_ENABLED=true
REALESTATE_CHECK_INTERVAL=45
REALESTATE_SKELBIU_ENABLED=true
REALESTATE_SKELBIU_MIN_PRICE=50000    # Min kaina
REALESTATE_SKELBIU_MAX_PRICE=150000   # Max kaina
REALESTATE_SKELBIU_TYPE=flat          # flat, house, room
REALESTATE_SKELBIU_LOCATION=Vilnius
```

**Aruodas.lt paieška:**
```env
REALESTATE_ARUODAS_ENABLED=true
REALESTATE_ARUODAS_MIN_PRICE=60000
REALESTATE_ARUODAS_MAX_PRICE=180000
REALESTATE_ARUODAS_LOCATION=Vilnius
```

**Pavyzdžiai:**

*3 kambariai Vilniuje, 50-100 tūkstančių:*
```env
REALESTATE_CHECK_INTERVAL=45
REALESTATE_SKELBIU_TYPE=flat
REALESTATE_SKELBIU_MIN_PRICE=50000
REALESTATE_SKELBIU_MAX_PRICE=100000
REALESTATE_SKELBIU_LOCATION=Vilnius
```

*Namai Kaune, 80-200 tūkstančių:*
```env
REALESTATE_CHECK_INTERVAL=60
REALESTATE_SKELBIU_TYPE=house
REALESTATE_SKELBIU_MIN_PRICE=80000
REALESTATE_SKELBIU_MAX_PRICE=200000
REALESTATE_SKELBIU_LOCATION=Kaunas
```

### ELEKTRONIKA (Priority 3) ⭐

Tikrinamas kas **120 minučių** (2 valandos).

**Skelbiu.lt paieška:**
```env
ELECTRONICS_ENABLED=true
ELECTRONICS_CHECK_INTERVAL=120
ELECTRONICS_SKELBIU_ENABLED=true
ELECTRONICS_SKELBIU_MIN_PRICE=100     # Min kaina
ELECTRONICS_SKELBIU_MAX_PRICE=2000    # Max kaina
ELECTRONICS_SKELBIU_CATEGORY=phones   # phones, laptops, etc
```

**Pavyzdžiai:**

*Telefonai, 200-800 eurų:*
```env
ELECTRONICS_CHECK_INTERVAL=120
ELECTRONICS_SKELBIU_CATEGORY=phones
ELECTRONICS_SKELBIU_MIN_PRICE=200
ELECTRONICS_SKELBIU_MAX_PRICE=800
```

*Laptopi, iki 1500:*
```env
ELECTRONICS_CHECK_INTERVAL=180
ELECTRONICS_SKELBIU_CATEGORY=laptops
ELECTRONICS_SKELBIU_MAX_PRICE=1500
```

## 🚀 Paleisti Sistemą

**Development režimas:**
```bash
npm run dev
```

**Production režimas:**
```bash
npm run build
npm start
```

## 📊 Duomenų Bazė

Visos duomenys saugomi `ads.db` (SQLite):

**Lentelės:**
- `ads` - visi saugoti skelbimiai su metaduomenimis
- `notification_log` - išsiųstų notifikacijų žurnalas
- `searches` - atliktu paieškų istorija

**Papildomi duomenys:**
- `duplicate_count` - kiek kartų pasirodė tas pats skelbimas
- `notified_at` - kada buvo pasiųsta notifikacija

## 💬 Telegram Pranešimo Formatas

### Naujas skelbimas (🆕 NEW):
```
🆕 NEW - Found 3 ad(s):

1. BMW 320i 2015
   💰 €8,500 | 📍 Vilnius
   🛣️  120,000 km
   🔗 View on skelbiu.lt
   Category: automobiliai

2. Audi A4 2018
   💰 €11,200 | 📍 Kaunas
   🛣️  95,500 km
   🔗 View on autoplius.lt
   Category: automobiliai
```

### Duplikatas (📌 DUPLICATE):
```
📌 DUPLICATE - Found 2 ad(s):
(Skelbimai kurie jau buvo matomj anksčiau - tik informacija apie pasikartojimą)
```

### Atnaujinimas (🔄 UPDATE):
```
🔄 UPDATE - Found 1 ad(s):
(Jei nustatysite rankiniu būdu norinti atnaujinti)
```

## 🔄 Dublikatų Valdymas

Sistemaeigiasi keip su dublikatais:

1. **Pirmą kartą rastas** → 🆕 Pasiųloma notifikacija
2. **Rastas vėl pakartotinai** → 📌 Dublikatas nurašomas (30% tikimybė pranešto)
3. **Ignore** → Nebesiunčiama pranešimu
4. **Žurnalas** → Saugomos visus dublikatu dažnį (`duplicate_count`)

## 📋 Naudojo Scenarijial - Su Numatytaisiais

Jei paliksite `.env` su default parametrais:

```env
# Automobiliai - BMW iš Vilniaus
CARS_CHECK_INTERVAL=15
CARS_SKELBIU_BRAND=BMW
CARS_SKELBIU_MIN_PRICE=5000
CARS_SKELBIU_MAX_PRICE=15000

# Nekilnojamasis - Butai Vilniuje
REALESTATE_CHECK_INTERVAL=45
REALESTATE_SKELBIU_TYPE=flat
REALESTATE_SKELBIU_MIN_PRICE=50000
REALESTATE_SKELBIU_MAX_PRICE=150000

# Elektronika - Telefonai
ELECTRONICS_CHECK_INTERVAL=120
ELECTRONICS_SKELBIU_CATEGORY=phones
ELECTRONICS_SKELBIU_MAX_PRICE=2000
```

Paleidus `npm run dev`, gausite:
- ✅ Notifikaciju apie naujus BMW kas 15 minučių
- ✅ Notifikaciju apie naujus butus kas 45 minučių  
- ✅ Notifikaciju apie naujus telefonus kas 120 minučių
- ✅ Ignoruojami dublikatai (išskyrus retų informacinių pranešimų)

## 🎓 Realūs Scenarijai

### Scenarijus 1: Ieškau pigaus BMW

```env
CARS_ENABLED=true
CARS_CHECK_INTERVAL=10
CARS_SKELBIU_BRAND=BMW
CARS_SKELBIU_MIN_PRICE=3000
CARS_SKELBIU_MAX_PRICE=8000
CARS_SKELBIU_MAX_MILEAGE=120000
CARS_SKELBIU_LOCATION=Vilnius
CARS_AUTOPLIUS_ENABLED=false
```

Rezultatas: Kas 10 minučių tikrinti tik pigius BMW iš Vilniaus.

### Scenarijus 2: Nekilnojamojo turto investitorius

```env
REALESTATE_ENABLED=true
REALESTATE_CHECK_INTERVAL=30
REALESTATE_SKELBIU_ENABLED=true
REALESTATE_SKELBIU_MIN_PRICE=100000
REALESTATE_SKELBIU_MAX_PRICE=500000
REALESTATE_SKELBIU_LOCATION=
REALESTATE_ARUODAS_ENABLED=true
REALESTATE_ARUODAS_MIN_PRICE=100000
REALESTATE_ARUODAS_MAX_PRICE=500000
```

Rezultatas: Kas 30 minučių skenavimas abejose platformose - visoje Lietuvoje.

### Scenarijus 3: "Seklusis" - sekantis visuose kategorigose

```env
CARS_ENABLED=true
CARS_CHECK_INTERVAL=20
REALESTATE_ENABLED=true
REALESTATE_CHECK_INTERVAL=60
ELECTRONICS_ENABLED=true
ELECTRONICS_CHECK_INTERVAL=180

# Automobiliai
CARS_SKELBIU_MIN_PRICE=2000
CARS_SKELBIU_MAX_PRICE=50000

# Nekilnojamasis
REALESTATE_SKELBIU_MIN_PRICE=30000
REALESTATE_SKELBIU_MAX_PRICE=300000

# Elektronika
ELECTRONICS_SKELBIU_MIN_PRICE=50
ELECTRONICS_SKELBIU_MAX_PRICE=5000
```

Rezultatas: Gautos pranešimu iš visų trijų kategorijų su skirtingais intervalais.

## ⚡ Optimizavimas

**Jei gaunate per daug notifikacijų:**
```env
CARS_CHECK_INTERVAL=30      # Padidinti intervalą
CARS_SKELBIU_MIN_PRICE=8000 # Padidinti minimalią kainą
```

**Jei nenorataite praleisti skelbimų:**
```env
CARS_CHECK_INTERVAL=10      # Sumažinti intervalą
```

**Jei chcete tik vieną platformą:**
```env
CARS_SKELBIU_ENABLED=true
CARS_AUTOPLIUS_ENABLED=false
```

## 📞 Troubleshooting

| Problema | Sprendimas |
|----------|-----------|
| ❌ "Telegram bot not configured" | Patikrinti `.env` - TELEGRAM_BOT_TOKEN ir TELEGRAM_CHAT_ID |
| ❌ "No ads found" | Patikrinti parametrus - gali būti per siauras ieškoti |
| ⚠️ Per daug notifikacijų | Padidinti CHECK_INTERVAL arba susiauriniti paieškos kriterijus |
| 🔄 Ir kartą pranešama apie tat patį skelbimą | Normalus - duplikatai kartais pranešami informacijos tikslu |

## 🚀 Production Deployment

Naudoti PM2:

```bash
npm run build
npm install -g pm2
pm2 start dist/index.js --name "skelbimai-monitoring"
pm2 save
pm2 startup
```

---

**Klausimai ar problemos?** Žiūrėkite QUICKSTART.md arba SETUP.md failų!

🎉 **Sėkmės jūsų skelbimų medžionėje!**
