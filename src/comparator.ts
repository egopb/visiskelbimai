import { Ad } from './scraper.js';

export interface RankedAd extends Ad {
  priceScore: number;
  freshnessScore: number;
  locationScore: number;
  totalScore: number;
}

export function rankAds(ads: Ad[], preferredLocation?: string): RankedAd[] {
  if (ads.length === 0) return [];

  const minPrice = Math.min(...ads.map(a => a.price));

  return ads
    .map(ad => {
      const priceScore = scorePriceValue(ad.price, minPrice);
      const freshnessScore = scoreFreshness(ad.date);
      const locationScore = scoreLocation(ad.location, preferredLocation);
      const totalScore = priceScore * 0.5 + freshnessScore * 0.3 + locationScore * 0.2;

      return { ...ad, priceScore, freshnessScore, locationScore, totalScore };
    })
    .sort((a, b) => b.totalScore - a.totalScore);
}

function scorePriceValue(price: number, minPrice: number): number {
  if (minPrice <= 0) return 50;
  const ratio = price / minPrice;
  if (ratio <= 1) return 100;
  if (ratio <= 1.1) return 90;
  if (ratio <= 1.25) return 70;
  if (ratio <= 1.5) return 40;
  return 20;
}

function scoreFreshness(dateStr: string): number {
  const posted = new Date(dateStr);
  const now = new Date();
  const diffHours = (now.getTime() - posted.getTime()) / (1000 * 60 * 60);

  if (diffHours < 24) return 100;
  if (diffHours < 72) return 80;
  if (diffHours < 168) return 60;
  return 30;
}

function scoreLocation(location: string, preferred?: string): number {
  if (!preferred) return 50;
  const loc = location.toLowerCase();
  const pref = preferred.toLowerCase();
  if (loc.includes(pref)) return 100;
  return 30;
}

export function deduplicateAds(ads: Ad[]): Ad[] {
  const seen = new Set<string>();
  const unique: Ad[] = [];

  for (const ad of ads) {
    const key = `${ad.title.toLowerCase()}_${ad.price}`;
    if (!seen.has(key)) {
      unique.push(ad);
      seen.add(key);
    }
  }

  return unique;
}

export function formatRanking(ranked: RankedAd[]): string {
  const lines: string[] = ['', '⭐ Skelbimu Reitingas:', '─'.repeat(60)];

  ranked.slice(0, 10).forEach((ad, idx) => {
    lines.push(`${idx + 1}. ${ad.title}`);
    lines.push(`   💰 €${ad.price} (${ad.priceScore}/100) | 📍 ${ad.location} | ⏰ ${ad.freshnessScore}/100`);
    lines.push(`   📊 Total: ${ad.totalScore.toFixed(1)}/100 | 🔗 ${ad.url}`);
    lines.push('');
  });

  return lines.join('\n');
}
