# 🚀 Multi-Category Ads Monitoring System

Automatinė sistema **3 kategorijų** skelbimų monitoringui iš populiariausių Lithuanian klasifikuotų skelbimų saityno su prioritetizuotais tikrinimo intervalais ir Telegram notifikacijomis.

## ✨ Galimybės

| Kategorija | Platformos | Intervalas | Prioritetas |
|-----------|-----------|-----------|-----------|
| 🚗 **Automobiliai** | skelbiu.lt, autoplius.lt | 15-20 min | ⭐⭐⭐ |
| 🏠 **Nekilnojamasis turtas** | skelbiu.lt, aruodas.lt | 45-60 min | ⭐⭐ |
| 💻 **Elektronika** | skelbiu.lt | 120+ min | ⭐ |

✅ Telegram notifikacijos  
✅ Automatinis monitoring  
✅ Duplikatų prevencija  
✅ SQLite duomenų bazė  
✅ Paprasta konfigūracija  
✅ Rikiavimas pagal kainą

## 🚀 Greitoji Pradžia

```bash
# 1. Diegti priklausomybes
npm install

# 2. Konfigūruoti .env
cp .env.example .env
# Redaguoti Telegram token ir chat ID

# 3. Pradėti
npm run dev
```

## 📚 Dokumentacija

- **[QUICKSTART.md](QUICKSTART.md)** - 5 minučių setup
- **[FULL_DOCS.md](FULL_DOCS.md)** - Pilna dokumentacija su scenarijais
- **[SETUP.md](SETUP.md)** - Detalus diegimas

## Development - OMC Testing

Šis projektas kuriamas su **oh-my-claudecode (OMC)** integration testing tikslais.

### Quick Start with OMC

```bash
# 1. Install OMC global
npm install -g oh-my-claude-sisyphus@latest

# 2. Start Claude Code + setup OMC
/setup

# 3. Test /autopilot inside Claude Code session
/autopilot "Finish skelbiu-compare scraper - add real skelbiu.lt HTTP requests"

# 4. Or use Team mode
/team 2:executor "Implement full scraper logic and comparison algorithms"
```

### Manual Setup

```bash
npm install
npm run build
npm start -- "iPhone"
```

## Project Structure

```
.
├── src/
│   ├── index.ts          # Main CLI entry point
│   ├── scraper.ts        # Skelbiu.lt scraper (to be built)
│   └── comparator.ts     # Ad comparison logic (to be built)
├── .omc/
│   └── skills/           # Custom OMC skills directory
├── package.json
└── tsconfig.json
```

## OMC Custom Skills

Custom skills stored in `.omc/skills/` for this project:

- `skelbiu-scraper.md` – Scraping strategy
- `comparison-logic.md` – Comparison algorithms
- `error-handling.md` – HTTP & parsing error resilience

## Status

🚧 **In Development with OMC**

Created for testing oh-my-claudecode multi-agent orchestration capabilities.
