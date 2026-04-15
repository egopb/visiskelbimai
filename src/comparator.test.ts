import { describe, it, expect } from 'vitest';
import {
  rankAds,
  deduplicateAds,
  formatRanking,
  scorePriceValue,
  scoreFreshness,
  scoreLocation,
  RankedAd,
} from './comparator.js';
import { Ad } from './scraper.js';

function makeAd(overrides: Partial<Ad> = {}): Ad {
  return {
    id: 'test_1',
    title: 'Test Ad',
    price: 5000,
    location: 'Vilnius',
    date: new Date().toISOString(),
    url: 'https://skelbiu.lt/test',
    source: 'skelbiu',
    ...overrides,
  };
}

// ─── scorePriceValue ──────────────────────────────

describe('scorePriceValue', () => {
  it('returns 100 for min price (ratio = 1)', () => {
    expect(scorePriceValue(1000, 1000)).toBe(100);
  });

  it('returns 90 for 10% above min', () => {
    expect(scorePriceValue(1100, 1000)).toBe(90);
  });

  it('returns 70 for 20% above min', () => {
    expect(scorePriceValue(1200, 1000)).toBe(70);
  });

  it('returns 40 for 40% above min', () => {
    expect(scorePriceValue(1400, 1000)).toBe(40);
  });

  it('returns 20 for 2x min price', () => {
    expect(scorePriceValue(2000, 1000)).toBe(20);
  });

  it('returns 50 when minPrice is 0', () => {
    expect(scorePriceValue(500, 0)).toBe(50);
  });

  it('returns 100 when price equals minPrice', () => {
    expect(scorePriceValue(7500, 7500)).toBe(100);
  });
});

// ─── scoreFreshness ───────────────────────────────

describe('scoreFreshness', () => {
  it('returns 100 for ad posted now', () => {
    expect(scoreFreshness(new Date().toISOString())).toBe(100);
  });

  it('returns 100 for ad posted 12 hours ago', () => {
    const date = new Date(Date.now() - 12 * 60 * 60 * 1000).toISOString();
    expect(scoreFreshness(date)).toBe(100);
  });

  it('returns 80 for ad posted 2 days ago', () => {
    const date = new Date(Date.now() - 48 * 60 * 60 * 1000).toISOString();
    expect(scoreFreshness(date)).toBe(80);
  });

  it('returns 60 for ad posted 5 days ago', () => {
    const date = new Date(Date.now() - 120 * 60 * 60 * 1000).toISOString();
    expect(scoreFreshness(date)).toBe(60);
  });

  it('returns 30 for ad posted 3 weeks ago', () => {
    const date = new Date(Date.now() - 21 * 24 * 60 * 60 * 1000).toISOString();
    expect(scoreFreshness(date)).toBe(30);
  });
});

// ─── scoreLocation ────────────────────────────────

describe('scoreLocation', () => {
  it('returns 50 when no preferred location', () => {
    expect(scoreLocation('Vilnius')).toBe(50);
  });

  it('returns 100 when location matches preferred', () => {
    expect(scoreLocation('Vilnius', 'Vilnius')).toBe(100);
  });

  it('returns 100 case-insensitive match', () => {
    expect(scoreLocation('VILNIUS', 'vilnius')).toBe(100);
  });

  it('returns 100 for partial match', () => {
    expect(scoreLocation('Vilnius, Senamiestis', 'Vilnius')).toBe(100);
  });

  it('returns 30 when location does not match', () => {
    expect(scoreLocation('Kaunas', 'Vilnius')).toBe(30);
  });
});

// ─── deduplicateAds ───────────────────────────────

describe('deduplicateAds', () => {
  it('returns empty array for empty input', () => {
    expect(deduplicateAds([])).toEqual([]);
  });

  it('keeps single ad', () => {
    const ads = [makeAd()];
    expect(deduplicateAds(ads)).toHaveLength(1);
  });

  it('removes exact duplicates', () => {
    const ad = makeAd({ title: 'BMW 320d', price: 5000 });
    expect(deduplicateAds([ad, ad])).toHaveLength(1);
  });

  it('removes case-insensitive title duplicates', () => {
    const ad1 = makeAd({ id: '1', title: 'BMW 320d', price: 5000 });
    const ad2 = makeAd({ id: '2', title: 'bmw 320d', price: 5000 });
    expect(deduplicateAds([ad1, ad2])).toHaveLength(1);
  });

  it('keeps ads with same title but different price', () => {
    const ad1 = makeAd({ id: '1', title: 'BMW 320d', price: 5000 });
    const ad2 = makeAd({ id: '2', title: 'BMW 320d', price: 6000 });
    expect(deduplicateAds([ad1, ad2])).toHaveLength(2);
  });

  it('keeps first ad when duplicates found', () => {
    const ad1 = makeAd({ id: '1', title: 'BMW', price: 5000, location: 'Vilnius' });
    const ad2 = makeAd({ id: '2', title: 'BMW', price: 5000, location: 'Kaunas' });
    const result = deduplicateAds([ad1, ad2]);
    expect(result[0].location).toBe('Vilnius');
  });
});

// ─── rankAds ──────────────────────────────────────

describe('rankAds', () => {
  it('returns empty array for empty input', () => {
    expect(rankAds([])).toEqual([]);
  });

  it('ranks single ad', () => {
    const result = rankAds([makeAd()]);
    expect(result).toHaveLength(1);
    expect(result[0].totalScore).toBeGreaterThan(0);
  });

  it('adds score properties to each ad', () => {
    const result = rankAds([makeAd()]);
    expect(result[0]).toHaveProperty('priceScore');
    expect(result[0]).toHaveProperty('freshnessScore');
    expect(result[0]).toHaveProperty('locationScore');
    expect(result[0]).toHaveProperty('totalScore');
  });

  it('ranks cheaper ad higher than expensive ad', () => {
    const cheap = makeAd({ id: '1', title: 'Cheap', price: 1000 });
    const expensive = makeAd({ id: '2', title: 'Expensive', price: 5000 });
    const result = rankAds([expensive, cheap]);
    expect(result[0].title).toBe('Cheap');
  });

  it('boosts ads matching preferred location', () => {
    const vilnius = makeAd({ id: '1', title: 'VLN', price: 1000, location: 'Vilnius' });
    const kaunas = makeAd({ id: '2', title: 'KNS', price: 1000, location: 'Kaunas' });
    const result = rankAds([kaunas, vilnius], 'Vilnius');
    expect(result[0].location).toBe('Vilnius');
  });

  it('sorts by totalScore descending', () => {
    const ads = [
      makeAd({ id: '1', price: 3000 }),
      makeAd({ id: '2', price: 1000 }),
      makeAd({ id: '3', price: 2000 }),
    ];
    const result = rankAds(ads);
    for (let i = 1; i < result.length; i++) {
      expect(result[i - 1].totalScore).toBeGreaterThanOrEqual(result[i].totalScore);
    }
  });
});

// ─── formatRanking ────────────────────────────────

describe('formatRanking', () => {
  it('returns header for empty array', () => {
    const result = formatRanking([]);
    expect(result).toContain('Skelbimu Reitingas');
  });

  it('includes ad title in output', () => {
    const ranked = rankAds([makeAd({ title: 'BMW 320d' })]);
    const result = formatRanking(ranked);
    expect(result).toContain('BMW 320d');
  });

  it('includes price in output', () => {
    const ranked = rankAds([makeAd({ price: 9999 })]);
    const result = formatRanking(ranked);
    expect(result).toContain('9999');
  });

  it('limits to 10 entries', () => {
    const ads = Array.from({ length: 15 }, (_, i) =>
      makeAd({ id: `${i}`, title: `Ad ${i}`, price: 1000 + i * 100 })
    );
    const ranked = rankAds(ads);
    const result = formatRanking(ranked);
    expect(result).toContain('1.');
    expect(result).toContain('10.');
    expect(result).not.toContain('11.');
  });
});
