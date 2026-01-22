---
phase: 04-content-architecture
plan: 02
subsystem: content-architecture
tags: [astro, content-collections, pillar-pages, seo]

dependency-graph:
  requires: [04-01]
  provides: [pillar-pages, article-card-component]
  affects: [04-03, future-article-routes]

tech-stack:
  added: []
  patterns:
    - "getCollection with pubDate sorting"
    - "CollectionEntry type unions for shared components"
    - "Empty state handling for content collections"

file-tracking:
  key-files:
    created:
      - bambini-fussball/src/components/ArticleCard.astro
      - bambini-fussball/src/pages/trainer/index.astro
      - bambini-fussball/src/pages/eltern/index.astro
      - bambini-fussball/src/pages/vereine/index.astro
    modified: []

decisions:
  - id: "04-02-01"
    decision: "CollectionEntry union type for ArticleCard"
    rationale: "Single component serves all three categories with type safety"
  - id: "04-02-02"
    decision: "article.id for URLs (not slug)"
    rationale: "Astro 5 replaced slug with id in content collections"
  - id: "04-02-03"
    decision: "Empty state as italicized placeholder text"
    rationale: "Friendly indication that content is coming without error appearance"

metrics:
  duration: "~3 minutes"
  completed: "2026-01-22"
---

# Phase 04 Plan 02: Pillar Category Pages Summary

**One-liner:** ArticleCard component and three pillar pages (/trainer/, /eltern/, /vereine/) with audience-specific content architecture and empty state handling

## What Was Built

### ArticleCard Component
Reusable component for displaying article previews across all category pages:
- TypeScript typed props accepting any of the three collection types
- German date formatting (de-DE locale)
- URL construction using `article.id` (Astro 5 pattern)
- Trailing slash compliance for Astro configuration
- Hover effects for visual feedback

### Pillar Category Pages
Three audience-specific overview pages:

| Route | Audience | Color Theme | Purpose |
|-------|----------|-------------|---------|
| `/trainer/` | Trainers | Green | Training tips, exercises, methodology |
| `/eltern/` | Parents | Blue | Equipment, child development, FAQs |
| `/vereine/` | Clubs | Amber | Organization, best practices |

Each page:
- Fetches articles via `getCollection()` with pubDate sorting (newest first)
- Displays responsive grid of ArticleCards when articles exist
- Shows "Artikel folgen in Kurze." placeholder when empty
- Uses consistent layout with `flex-1` for proper footer positioning

## Technical Details

### ArticleCard Props Interface
```typescript
interface Props {
  article: CollectionEntry<'trainer'> | CollectionEntry<'eltern'> | CollectionEntry<'vereine'>;
  basePath: string;
}
```

### Collection Query Pattern
```typescript
const articles = await getCollection('trainer');
const sortedArticles = articles.sort(
  (a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf()
);
```

## Verification Results

| Check | Result |
|-------|--------|
| `npm run build` | PASS - 6 pages built |
| /trainer/ renders | PASS |
| /eltern/ renders | PASS |
| /vereine/ renders | PASS |
| Empty state displays | PASS |
| Color scheme matches homepage | PASS |

## Deviations from Plan

None - plan executed exactly as written.

## Commits

| Hash | Type | Description |
|------|------|-------------|
| 5bf7e2c | feat | ArticleCard component with TypeScript types |
| 3767cd3 | feat | Three pillar category pages |

## Next Phase Readiness

**Ready for 04-03:** Dynamic article routes

Prerequisites met:
- [x] ArticleCard component exists for article previews
- [x] Category pages exist to link back from articles
- [x] Collection query pattern established
- [x] Empty collections handled gracefully

Next plan will add:
- Dynamic `[id].astro` routes for article detail pages
- ArticleLayout with breadcrumb navigation
- render() function usage for markdown content
