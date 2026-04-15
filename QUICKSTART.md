# 🎯 GREITASIS STARTAS - Skelbimu Monitoringo Sistema

## ⚡ 5 Minučių Setup

### Žingsnis 1: Instalacija
```bash
cd CloudCode
npm install
```

### Žingsnis 2: Sukurti `.env` failą
Kopijuoti `.env.example`:
```bash
cp .env.example .env
```

Redaguoti `.env` ir įdėti:
- `TELEGRAM_BOT_TOKEN` - iš @BotFather
- `TELEGRAM_CHAT_ID` - iš @userinfobot

### Žingsnis 3: Pradėti
```bash
npm run dev
```

✅ **Baigta!** Gausile Telegram pranešimus apie naujus skelbimuš.

---

## 🛠️ Telegram Token Gūdymas (jei nežinote)

### Gauti Bot Token:
1. Atidaryti Telegram
2. Rasti `@BotFather`
3. Rašyti `/start` ir `/newbot`
4. Laikytis sraito - gauti tokena

### Gauti Chat ID:
1. Atidaryti Telegram
2. Rasti `@userinfobot`
3. Jis parodys jūsų user ID

### Kode naudoti:
```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_CHAT_ID=987654321
```

---

## 🎨 Konfigūracija

### Paprasti pasirinkimai:

**Pigios mašinos:**
```env
SEARCH_MIN_PRICE=1000
SEARCH_MAX_PRICE=5000
SEARCH_BRAND=VW
```

**Premium BMW:**
```env
SEARCH_MIN_PRICE=10000
SEARCH_MAX_PRICE=30000
SEARCH_BRAND=BMW
SEARCH_MODEL=5
```

**Konkretus miestas:**
```env
SEARCH_LOCATION=Vilnius
```

---

## 🚀 CLI Paleisti

Su nenumatytaisiais nustatymais:
```bash
npm run dev
```

Su noriajausiais parametrais:
```bash
npm run dev -- --brand "Audi" --min-price 8000 --max-price 20000
```

---

## 📊 Pamatyti duomenis

WE default uses SQLite database (`ads.db`). Tai yra faile vietos direktorijoje.

---

## 🎯 Ką Gausile

Telegram pranešimuose:
- ✅ Skelbimo pavadinimas
- ✅ Kaina eurais
- ✅ Lokacija
- ✅ Rida (jei yra)
- ✅ Tiesioginė nuoroda į skelbimą
- ✅ Šaltinis (skelbiu.lt arba autoplius.lt)

---

## ⚙️ Production Deploy

```bash
npm run build
npm start
```

Arba naudoti PM2:
```bash
npm install -g pm2
pm2 start dist/index.js --name "skelbimai-monitoring"
pm2 save
pm2 startup
```

---

## 🔄 Tikrinimo intervalas

Keisti `.env`:
```env
CHECK_INTERVAL=30   # kas 30 minučiu
CHECK_INTERVAL=60   # kas valanda
CHECK_INTERVAL=15   # kas 15 minučiu
```

---

## 💡 Patarimai

1. **Pirmą kartą** paleisti su numatytaisiais - pažiūrėti, kaip veikia
2. **Tada** pakoreguoti parametrus jūsų pageidavimams
3. **Padidinti** CHECK_INTERVAL jei gaunate per daug žinučių
4. **Sumažinti** CHECK_INTERVAL jei norite dažnesnės paieškos

---

## ❓ F.A.Q

**P:** Kaip dažnai tikrinamas?  
**A:** Default kas 30 min. Keisti `CHECK_INTERVAL` .env

**P:** Kurie skelbiai saugomi?  
**A:** Tik nauji - nesaugomi dublikatai

**P:** Ar veikia saugumo sąsajoje?  
**A:** Legaliai - tik public duomenys, naudojant web scraping

**P:** Ar galiu keisti savim skelbimu kategorijų?  
**A:** Taip - redaguoti src/index.ts scraping URL'ius

**P:** Ar veikia Linux/Mac?  
**A:** Taip - tas pats setup

---

## 📞 Kontaktai / Pagalba

Jei kažkas neveikia:

1. Patikrinti `.env` - ar žyda teisingai (nėra tarpų)
2. Patikrinti MySQL bot atsisiunčia - rašyti `/start`
3. Čekinti `npm install` veikia be klaidų
4. Žr. `SETUP.md` detaliam troubleshooting

---

**Pradėsite dabar!** 🚀
