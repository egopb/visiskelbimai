/**
 * Pavyzdžiai kaip naudoti skelbimu monitoringo sistemą
 * 
 * npm run dev -- --brand "Audi" --min-price 8000 --max-price 20000
 */

// Pavyzdys 1: Numatytieji nustatymai (BMW, 5000-15000, Vilnius)
// npm run dev

// Pavyzdys 2: Audi ieškojimai pagal kainą
// npm run dev -- --brand "Audi" --min-price 8000 --max-price 25000

// Pavyzdys 3: VW Golf su ridomis
// npm run dev -- --brand "Volkswagen" --model "Golf" --min-price 3000 --max-price 10000 --max-mileage 100000

// Pavyzdys 4: Kaunas, susitaikytos mašinos
// npm run dev -- --location "Kaunas" --min-price 4000 --max-price 12000

// Pavyzdys 5: Elektra / Hibridai
// npm run dev -- --brand "Tesla" --min-price 20000 --max-price 100000

// Pavyzdys 6: Plačios paieškos
// npm run dev -- --min-price 2000 --max-price 50000 --max-mileage 200000

/**
 * .env konfigūracijos pavyzdžiai:
 */

// Pavyzdys 1: Paprasta nustatymas
/*
TELEGRAM_BOT_TOKEN=123456:ABCDEFG123456
TELEGRAM_CHAT_ID=987654321
SEARCH_MIN_PRICE=5000
SEARCH_MAX_PRICE=15000
SEARCH_BRAND=BMW
CHECK_INTERVAL=30
*/

// Pavyzdys 2: Didelė biudžeto viršutinė riba
/*
TELEGRAM_BOT_TOKEN=123456:ABCDEFG123456
TELEGRAM_CHAT_ID=987654321
SEARCH_MIN_PRICE=15000
SEARCH_MAX_PRICE=100000
SEARCH_BRAND=Audi
SEARCH_MODEL=A4
CHECK_INTERVAL=60
*/

// Pavyzdys 3: Dažnas monitoring (kas 15 min)
/*
TELEGRAM_BOT_TOKEN=123456:ABCDEFG123456
TELEGRAM_CHAT_ID=987654321
SEARCH_MIN_PRICE=3000
SEARCH_MAX_PRICE=8000
SEARCH_BRAND=VW
CHECK_INTERVAL=15
*/

/**
 * Telegram Pranešimų Format:
 * 
 * 🆕 NEW - Found 3 ad(s) matching your criteria:
 * 
 * 1. BMW 320i 2015
 *    💰 €8,500 | 📍 Vilnius
 *    🛣️  120,000 km
 *    🔗 View on skelbiu.lt
 *    Source: skelbiu
 * 
 * 2. BMW 330d 2018
 *    💰 €11,200 | 📍 Kaunas
 *    🛣️  95,500 km
 *    🔗 View on autoplius.lt
 *    Source: autoplius
 */
