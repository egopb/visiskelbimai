---
name: Ad Comparison Algorithms
description: Smart comparison of skelbiu.lt ads by price, location, freshness
triggers:
  - lyginti
  - compare
  - comparison
  - reitingas
source: extracted
---

## Comparison Dimensions

When implementing ad comparison in `src/comparator.ts`:

### 1. **Price Scoring**
```
- Best price (min) = 100/100
- 10% above min = 90/100
- 25% above min = 70/100
- 50% above min = 40/100
- >50% above = 20/100
```

### 2. **Freshness Scoring**
```
- Posted today = 100/100
- 1-3 days old = 80/100
- 1 week old = 60/100
- 2+ weeks old = 30/100
```

### 3. **Location Preference**
```
User specifies preferred location(s) → boost those scores by 20%
```

### 4. **Overall Ranking**
```
Score = (Price * 0.5) + (Freshness * 0.3) + (Location * 0.2)
```

## Implementation

- [ ] Create `ComparatorService` class
- [ ] Implement scoring functions for each dimension
- [ ] Add sorting/filtering methods
- [ ] Return ranked `Ad[]` with scores
- [ ] Format human-readable output (tables, charts)

## Advanced Features (Future)

- Seller reputation score
- Photo quality heuristics
- Seasonal trend analysis
