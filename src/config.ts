import * as path from 'path';
import * as fs from 'fs';

// Load .env manually (no dotenv dependency needed)
const envPath = path.join(process.cwd(), '.env');
if (fs.existsSync(envPath)) {
  const content = fs.readFileSync(envPath, 'utf-8');
  for (const line of content.split('\n')) {
    const match = line.match(/^([^#=]+)=(.*)$/);
    if (match) {
      process.env[match[1].trim()] = match[2].trim();
    }
  }
}

export const config = {
  telegram: {
    botToken: process.env.TELEGRAM_BOT_TOKEN || '',
    chatId: process.env.TELEGRAM_CHAT_ID || '',
  },
  search: {
    minPrice: parseInt(process.env.SEARCH_MIN_PRICE || '1000'),
    maxPrice: parseInt(process.env.SEARCH_MAX_PRICE || '50000'),
    brand: process.env.SEARCH_BRAND || 'BMW',
    model: process.env.SEARCH_MODEL || '3',
  },
  monitoring: {
    checkInterval: parseInt(process.env.CHECK_INTERVAL || '30') * 60 * 1000, // Convert to ms
  },
  database: {
    path: process.env.DB_PATH || path.join(process.cwd(), 'ads.db'),
  },
};

export function validateConfig(): string[] {
  const errors: string[] = [];

  if (!config.telegram.botToken) {
    errors.push('TELEGRAM_BOT_TOKEN is not set');
  }
  if (!config.telegram.chatId) {
    errors.push('TELEGRAM_CHAT_ID is not set');
  }

  return errors;
}
