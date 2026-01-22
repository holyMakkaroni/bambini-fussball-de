---
phase: 04-content-architecture
plan: 03
subsystem: content
tags: [astro, breadcrumbs, dynamic-routes, schema-org, markdown]

# Dependency graph
requires:
  - phase: 04-01
    provides: Content collections with glob loader
  - phase: 04-02
    provides: Category pillar pages with ArticleCard component
provides:
  - ArticleLayout with breadcrumb navigation
  - Dynamic [id].astro routes for all categories
  - Schema.org BreadcrumbList JSON-LD
  - Test article demonstrating content flow
affects: [05-content-creation, seo-optimization]

# Tech tracking
tech-stack:
  added: [astro-breadcrumbs]
  patterns: [render() standalone import, article.id for routes]

key-files:
  created:
    - bambini-fussball/src/layouts/ArticleLayout.astro
    - bambini-fussball/src/pages/trainer/[id].astro
    - bambini-fussball/src/pages/eltern/[id].astro
    - bambini-fussball/src/pages/vereine/[id].astro
    - bambini-fussball/src/content/trainer/beispiel-artikel.md
  modified:
    - bambini-fussball/package.json

key-decisions:
  - "astro-breadcrumbs for Schema.org JSON-LD support"
  - "Custom crumbs array for German labels (Startseite not Home)"
  - "article.id for routes per Astro 5 pattern"

patterns-established:
  - "ArticleLayout: wrapper for all article pages with breadcrumbs"
  - "Dynamic route pattern: getStaticPaths + render() from astro:content"

# Metrics
duration: 3min
completed: 2026-01-22
---

# Phase 4 Plan 3: Dynamic Article Routes Summary

**ArticleLayout with astro-breadcrumbs (Schema.org JSON-LD), dynamic [id].astro routes for all three categories, and test article verifying complete markdown-to-page flow**

## Performance

- **Duration:** 3 min
- **Started:** 2026-01-22T11:23:13Z
- **Completed:** 2026-01-22T11:25:46Z
- **Tasks:** 3
- **Files created:** 5

## Accomplishments
- ArticleLayout with breadcrumb navigation and Schema.org BreadcrumbList JSON-LD
- Dynamic [id].astro routes for /trainer/, /eltern/, /vereine/ categories
- Test article renders with proper hierarchy: Startseite > Trainer > Article title
- Markdown headings, lists, paragraphs styled and render correctly

## Task Commits

Each task was committed atomically:

1. **Task 1: Install astro-breadcrumbs package** - `abc4fbc` (chore)
2. **Task 2: Create ArticleLayout with breadcrumbs** - `bae029a` (feat)
3. **Task 3: Create dynamic article routes and test article** - `1acefd3` (feat)

## Files Created/Modified

- `bambini-fussball/package.json` - Added astro-breadcrumbs ^3.3.3
- `bambini-fussball/src/layouts/ArticleLayout.astro` - Article page wrapper with breadcrumbs, prose styling, German date formatting
- `bambini-fussball/src/pages/trainer/[id].astro` - Dynamic route for trainer articles
- `bambini-fussball/src/pages/eltern/[id].astro` - Dynamic route for eltern articles
- `bambini-fussball/src/pages/vereine/[id].astro` - Dynamic route for vereine articles
- `bambini-fussball/src/content/trainer/beispiel-artikel.md` - Test article with realistic structure

## Decisions Made

1. **astro-breadcrumbs package** - Provides Schema.org JSON-LD automatically, better than hand-rolling
2. **Custom crumbs array** - German labels (Startseite, Trainer, etc.) for semantic clarity
3. **render() as standalone import** - Astro 5 pattern (not article.render() method)
4. **article.id for routes** - Astro 5 replaced slug with id

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

None - build and verification succeeded on first attempt.

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

- Content architecture complete (04-01 + 04-02 + 04-03)
- Ready for content creation phase
- All three category collections configured with pillar pages and article routes
- Breadcrumbs provide SEO benefit via Schema.org structured data

**CONT-02 requirement satisfied:** Dynamic article routes work for all categories

---
*Phase: 04-content-architecture*
*Completed: 2026-01-22*
