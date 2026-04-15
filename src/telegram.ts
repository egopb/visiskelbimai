import TelegramBot from 'node-telegram-bot-api';
import { config } from './config.js';
import { StoredAd } from './database.js';

let bot: TelegramBot | null = null;

export function initTelegram(): TelegramBot {
  bot = new TelegramBot(config.telegram.botToken, { polling: false });
  return bot;
}

export function getTelegram(): TelegramBot {
  if (!bot) {
    throw new Error('Telegram bot not initialized. Call initTelegram() first.');
  }
  return bot;
}

export async function sendNotification(ad: StoredAd): Promise<void> {
  const telegram = getTelegram();

  const message = formatAdMessage(ad);

  try {
    await telegram.sendMessage(config.telegram.chatId, message, {
      parse_mode: 'HTML',
      disable_web_page_preview: false,
    });
    console.log(`✅ Notification sent for: ${ad.title}`);
  } catch (error) {
    console.error(`❌ Failed to send notification:`, error);
    throw error;
  }
}

export async function sendTestMessage(): Promise<void> {
  const telegram = getTelegram();

  try {
    await telegram.sendMessage(
      config.telegram.chatId,
      '✅ <b>Skelbimu monitoringas pradėtas!</b>\n\nBus siunčiamos notifikacijos apie naujus skelbimuš, atitinkančius jūsų kriterijus.',
      { parse_mode: 'HTML' }
    );
    console.log('✅ Test message sent successfully');
  } catch (error) {
    console.error('❌ Failed to send test message:', error);
    throw error;
  }
}

function formatAdMessage(ad: StoredAd): string {
  return `
<b>🚗 Naujas skelbimas!</b>

<b>Pavadinimas:</b> ${escapeHtml(ad.title)}
<b>Kaina:</b> €${ad.price}
<b>Lokacija:</b> ${escapeHtml(ad.location)}
<b>Data:</b> ${ad.date}
<b>Šaltinis:</b> ${ad.source === 'autoplius' ? 'autoplius.lt' : 'skelbiu.lt'}

<a href="${ad.url}">Peržiūrėti skelbimą →</a>
  `.trim();
}

function escapeHtml(text: string): string {
  const map: { [key: string]: string } = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
  };
  return text.replace(/[&<>"']/g, (char) => map[char]);
}
