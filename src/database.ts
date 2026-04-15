import * as fs from 'fs';
import * as path from 'path';
import { config } from './config.js';

export interface StoredAd {
  id: string;
  title: string;
  price: number;
  location: string;
  date: string;
  url: string;
  source: 'autoplius' | 'skelbiu';
  createdAt: string;
}

interface DbData {
  ads: Record<string, StoredAd>;
  notified: Record<string, string>;
}

let dbPath = '';
let data: DbData = { ads: {}, notified: {} };

export function initDatabase(): void {
  dbPath = config.database.path.replace('.db', '.json');
  if (fs.existsSync(dbPath)) {
    const raw = fs.readFileSync(dbPath, 'utf-8');
    data = JSON.parse(raw);
  } else {
    data = { ads: {}, notified: {} };
    persist();
  }
  console.log(`📁 Database: ${dbPath} (${Object.keys(data.ads).length} ads)`);
}

function persist(): void {
  fs.writeFileSync(dbPath, JSON.stringify(data, null, 2), 'utf-8');
}

export function closeDatabase(): void {
  persist();
}

export function saveAd(ad: StoredAd): void {
  data.ads[ad.id] = ad;
  persist();
}

export function isAdNotified(adId: string): boolean {
  return adId in data.notified;
}

export function markAdAsNotified(adId: string): void {
  data.notified[adId] = new Date().toISOString();
  persist();
}

export function getAllAds(): StoredAd[] {
  return Object.values(data.ads).sort(
    (a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
  );
}

export function getRecentAds(hours: number): StoredAd[] {
  const cutoff = Date.now() - hours * 60 * 60 * 1000;
  return getAllAds().filter(ad => new Date(ad.createdAt).getTime() > cutoff);
}
