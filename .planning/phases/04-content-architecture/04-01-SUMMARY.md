---
phase: 04-content-architecture
plan: 01
subsystem: content
tags:
  - astro5
  - content-collections
  - navigation
  - glob-loader

dependency-graph:
  requires:
    - phase-01 (astro foundation)
    - phase-03 (legal pages using BaseLayout)
  provides:
    - Astro 5 Content Layer API with glob() loaders
    - Site-wide navigation component
    - BaseLayout with Header integration
  affects:
    - 04-02 (category pages will use content collections)
    - All future pages (will have navigation)

tech-stack:
  added: []
  patterns:
    - "glob() loader for content collections"
    - "z.coerce.date() for YAML date parsing"

key-files:
  created:
    - bambini-fussball/src/components/Header.astro
  modified:
    - bambini-fussball/src/content.config.ts
    - bambini-fussball/src/layouts/BaseLayout.astro

decisions:
  - "Astro 5 glob() loader syntax (not legacy type: 'content')"
  - "z.coerce.date() for date fields (YAML parses dates as strings)"
  - "Color-coded navigation hover states matching homepage cards"

metrics:
  duration: "~4 minutes"
  completed: "2026-01-22"
---

# Phase 04 Plan 01: Content Config Migration and Navigation Summary

**One-liner:** Migrated content collections to Astro 5 glob() loader syntax and added site-wide Header navigation with three category links.

## Changes Made

### Task 1: Migrate content.config.ts to Astro 5 glob() loader
- Added `glob` import from `astro/loaders`
- Replaced `type: 'content'` with `loader: glob({ pattern, base })` for trainer, eltern, vereine collections
- Changed `z.date()` to `z.coerce.date()` for pubDate and updatedDate fields
- Build completes without content collection errors

### Task 2: Create Header navigation and integrate into BaseLayout
- Created `Header.astro` with:
  - Logo/site name linking to home
  - Three navigation links: Trainer, Eltern, Vereine
  - Color-coded hover states (green, blue, amber)
  - Touch-friendly targets (min-h-[44px] inline-flex items-center)
  - aria-label="Hauptnavigation" for accessibility
  - Mobile-responsive layout (flex-col to flex-row)
- Updated `BaseLayout.astro`:
  - Added Header import
  - Wrapped slot in `<main class="flex-1">` for proper layout
  - Header renders on all pages using BaseLayout

## Commits

| Commit | Type | Description |
|--------|------|-------------|
| 92a0267 | feat | migrate content.config.ts to Astro 5 glob() loader |
| 48ce513 | feat | add site-wide Header navigation component |

## Verification Results

- [x] `npm run build` completes without errors
- [x] Header navigation visible on all pages
- [x] All three category links present: Trainer, Eltern, Vereine
- [x] Links have correct href attributes with trailing slashes
- [x] content.config.ts uses glob() loader syntax
- [x] Touch targets are 44px minimum height
- [x] aria-label present for accessibility

## Deviations from Plan

None - plan executed exactly as written.

## Next Phase Readiness

**Ready for 04-02:** Category pages can now:
- Query content collections via Astro Content Layer API
- Inherit navigation from BaseLayout
- Use glob() loaders to fetch articles

**Note:** Content directories (trainer/, eltern/, vereine/) exist but are empty. Warnings about "No files found" are expected until content is created in later phases.
