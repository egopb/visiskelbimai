import axios from 'axios';
import * as cheerio from 'cheerio';
import { StoredAd } from './database.js';
import { config } from './config.js';

export interface Ad {
  id: string;
  title: string;
  price: number;
  location: string;
  date: string;
  url: string;
  source: 'autoplius' | 'skelbiu';
}

const headers = {
  'User-Agent':
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
};

export async function scrapeSkelbiu(): Promise<Ad[]> {
  const ads: Ad[] = [];

  try {
    const url = buildSkelbiumUrl();
    console.log(`🔍 Scraping skelbiu.lt: ${url}`);

    const { data } = await axios.get(url, { headers, timeout: 10000 });
    const $ = cheerio.load(data);

    // Adapt selectors based on actual HTML structure
    const listings = $('.listing-item, .ad-item, [data-ad-id]');

    listings.each((index, element) => {
      try {
        const $elem = $(element);

        // Try various selectors for title
        const title = $elem.find('h2, .title, .ad-title, a.listing-link').text().trim();

        // Try various selectors for price
        const priceText = $elem.find('.price, .ad-price, .cost').text().trim();
        const price = extractPrice(priceText);

        // Try various selectors for location
        const location = $elem.find('.location, .city, .ad-location').text().trim();

        // Get URL
        const url =
          $elem.find('a').attr('href') ||
          $elem.attr('href') ||
          $elem.attr('data-url') ||
          '';

        // Generate ID from URL or title+price
        const id = generateId(url || title + price);

        if (title && price > 0) {
          ads.push({
            id,
            title,
            price,
            location: location || 'Nenurodyta',
            date: new Date().toISOString().split('T')[0],
            url: normalizeUrl(url, 'skelbiu.lt'),
            source: 'skelbiu',
          });
        }
      } catch (e) {
        console.warn('⚠️  Error parsing ad item:', e);
      }
    });

    console.log(`✅ Found ${ads.length} ads on skelbiu.lt`);
  } catch (error) {
    console.error('❌ Error scraping skelbiu.lt:', error instanceof Error ? error.message : error);
  }

  return ads;
}

export async function scrapeAutoplius(): Promise<Ad[]> {
  const ads: Ad[] = [];

  try {
    const url = buildAutopliusUrl();
    console.log(`🔍 Scraping autoplius.lt: ${url}`);

    const { data } = await axios.get(url, { headers, timeout: 10000 });
    const $ = cheerio.load(data);

    // Adapt selectors based on actual HTML structure
    const listings = $('.listing-item, .ad-item, [data-listing-id]');

    listings.each((index, element) => {
      try {
        const $elem = $(element);

        const title = $elem.find('h2, .title, .listing-title, a').text().trim();
        const priceText = $elem.find('.price, .listing-price, .cost').text().trim();
        const price = extractPrice(priceText);
        const location = $elem.find('.location, .city').text().trim();
        const url =
          $elem.find('a').attr('href') ||
          $elem.attr('href') ||
          $elem.attr('data-url') ||
          '';

        const id = generateId(url || title + price);

        if (title && price > 0) {
          ads.push({
            id,
            title,
            price,
            location: location || 'Nenurodyta',
            date: new Date().toISOString().split('T')[0],
            url: normalizeUrl(url, 'autoplius.lt'),
            source: 'autoplius',
          });
        }
      } catch (e) {
        console.warn('⚠️  Error parsing ad item:', e);
      }
    });

    console.log(`✅ Found ${ads.length} ads on autoplius.lt`);
  } catch (error) {
    console.error('❌ Error scraping autoplius.lt:', error instanceof Error ? error.message : error);
  }

  return ads;
}

export async function scrapeAll(): Promise<Ad[]> {
  const [skelbiu, autoplius] = await Promise.all([scrapeSkelbiu(), scrapeAutoplius()]);

  return [...skelbiu, ...autoplius];
}

export function filterAdsByPrice(ads: Ad[]): Ad[] {
  return ads.filter((ad) => ad.price >= config.search.minPrice && ad.price <= config.search.maxPrice);
}

function buildSkelbiumUrl(): string {
  const baseUrl = 'https://www.skelbiu.lt';
  // This is a placeholder - actual URL structure needs investigation
  return `${baseUrl}/skelbimai/automobiliai/?price_from=${config.search.minPrice}&price_to=${config.search.maxPrice}`;
}

function buildAutopliusUrl(): string {
  const baseUrl = 'https://www.autoplius.lt';
  // This is a placeholder - actual URL structure needs investigation
  return `${baseUrl}/skelbimai/automobiliai/?price_from=${config.search.minPrice}&price_to=${config.search.maxPrice}`;
}

function extractPrice(text: string): number {
  const match = text.match(/\d+/);
  return match ? parseInt(match[0]) : 0;
}

function generateId(text: string): string {
  return `ad_${Buffer.from(text).toString('base64').slice(0, 10)}`;
}

function normalizeUrl(url: string, domain: string): string {
  if (!url) return `https://${domain}`;
  if (url.startsWith('http')) return url;
  if (url.startsWith('/')) return `https://${domain}${url}`;
  return `https://${domain}/${url}`;
}

export function compareAds(ads: Ad[]): Ad[] {
  // Remove duplicates (same price, similar title)
  const seen = new Set<string>();
  const unique: Ad[] = [];

  const sorted = [...ads].sort((a, b) => a.price - b.price);

  for (const ad of sorted) {
    const key = `${ad.title}_${ad.price}`;
    if (!seen.has(key)) {
      unique.push(ad);
      seen.add(key);
    }
  }

  return unique;
}
