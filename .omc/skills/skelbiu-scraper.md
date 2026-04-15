---
name: Skelbiu Scraper Strategy
description: Smart skelbiu.lt scraping with error handling and rate limiting
triggers:
  - skelbiu
  - scraper
  - scraping
  - parsinimas
source: extracted
---

## Strategy

When implementing skelbiu.lt scraper:

1. **HTTP Request Pattern**
   - Use axios with User-Agent headers
   - Add 1-2 second delays between requests (rate limiting)
   - Handle 404, 403, timeouts gracefully

2. **HTML Parsing with Cheerio**
   - Parse listing container: `.listing-item` or similar
   - Extract fields: title, price, location, posted date, URL
   - Handle missing fields (optional data)

3. **Data Validation**
   - Ensure price is numeric
   - Validate URL format
   - Filter out expired/malformed ads

4. **Error Resilience**
   - Catch network errors → retry with exponential backoff
   - Catch parse errors → skip ad, continue
   - Log warnings for debugging

## Implementation Checklist

- [ ] Create `src/scraper.ts` with `ScrapierService` class
- [ ] Add `fetchListings(query: string): Promise<Ad[]>` method
- [ ] Add error handlers + retry logic
- [ ] Test with 5-10 sample queries
- [ ] Add caching (optional) to reduce API calls

## Testing Tips

Use `/ask claude "test skelbiu scraper against live site"` to validate real responses.
