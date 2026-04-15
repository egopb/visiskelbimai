export interface SearchConfig {
  brand?: string;
  minPrice?: number;
  maxPrice?: number;
  maxMileage?: number;
  location?: string;
  fuelType?: string;
  category?: string;
  type?: string;
}

export type Platform = 'skelbiu' | 'autoplius' | 'aruodas';
export type Category = 'automobiliai' | 'nekilnojamasis' | 'elektronika';

export interface Ad {
  id: string;
  title: string;
  price: number;
  location: string;
  mileage?: number;
  fuelType?: string;
  date: string;
  url: string;
  source: Platform;
  category: Category;
  hashId: string;
  score?: number;
}

export interface CategoryConfig {
  category: Category;
  enabled: boolean;
  checkInterval: number;
  priority: number;
  searches: Array<{
    name: string;
    platforms: Platform[];
    params: SearchConfig;
  }>;
}

export interface NotificationMessage {
  ads: Ad[];
  isUpdate: boolean;
  isDuplicate: boolean;
  timestamp: string;
}

export interface ComparisonResult {
  ad: Ad;
  priceScore: number;
  freshnessScore: number;
  locationScore: number;
  totalScore: number;
}
