/**
 * Multi-Category Ads Monitoring System
 * 
 * Monitors skelbiu.lt & autoplius.lt for ads matching search criteria.
 * Sends Telegram notifications, prevents duplicates, ranks results.
 */

import { config, validateConfig } from './config.js';
import { scrapeAll, filterAdsByPrice, Ad } from './scraper.js';
import { initDatabase, saveAd, isAdNotified, markAdAsNotified, closeDatabase, StoredAd } from './database.js';
import { initTelegram, sendNotification, sendTestMessage } from './telegram.js';
import { rankAds, deduplicateAds, formatRanking } from './comparator.js';

async function runScan(): Promise<void> {
  console.log('\n🔍 Starting scan...');

  const allAds = await scrapeAll();
  const filtered = filterAdsByPrice(allAds);
  const unique = deduplicateAds(filtered);

  console.log(`📊 Found: ${allAds.length} total, ${filtered.length} in price range, ${unique.length} unique`);

  // Store and notify
  let newCount = 0;
  for (const ad of unique) {
    const stored: StoredAd = {
      id: ad.id,
      title: ad.title,
      price: ad.price,
      location: ad.location,
      date: ad.date,
      url: ad.url,
      source: ad.source,
      createdAt: new Date().toISOString(),
    };

    saveAd(stored);

    if (!isAdNotified(ad.id)) {
      try {
        await sendNotification(stored);
        markAdAsNotified(ad.id);
        newCount++;
      } catch (error) {
        console.error(`❌ Notification failed for: ${ad.title}`);
      }
    }
  }

  console.log(`🆕 New notifications sent: ${newCount}`);

  // Show ranking
  const ranked = rankAds(unique, config.search.brand);
  console.log(formatRanking(ranked));
}

async function main(): Promise<void> {
  console.log('🚀 Multi-Category Ads Monitoring System');
  console.log('========================================\n');

  // Validate config
  const errors = validateConfig();
  if (errors.length > 0) {
    console.error('❌ Configuration errors:');
    errors.forEach(e => console.error(`   - ${e}`));
    process.exit(1);
  }

  // Initialize
  initDatabase();
  initTelegram();

  // Send startup message
  try {
    await sendTestMessage();
  } catch {
    console.warn('⚠️  Could not send startup message');
  }

  // Initial scan
  await runScan();

  // Schedule periodic scans
  const interval = config.monitoring.checkInterval;
  console.log(`\n⏰ Next scan in ${interval / 60000} minutes. Monitoring...`);

  setInterval(() => {
    runScan().catch(err => {
      console.error('❌ Scan error:', err instanceof Error ? err.message : err);
    });
  }, interval);
}

// Graceful shutdown
process.on('SIGINT', () => {
  console.log('\n👋 Shutting down...');
  closeDatabase();
  process.exit(0);
});

main();
