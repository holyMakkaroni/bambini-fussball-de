---
phase: 05-seo-infrastructure
plan: 02
subsystem: seo
tags: [json-ld, schema-org, article-schema, author-collection, astro-content]

# Dependency graph
requires:
  - phase: 05-01
    provides: Meta tag infrastructure and content schema validation
provides:
  - Authors collection with JSON schema for structured author data
  - Article JSON-LD schema on all article pages
  - Author entity with @id linking to author profile URL
  - reference() pattern for Astro content collections
affects: [05-03, author-pages, content-creation]

# Tech tracking
tech-stack:
  added: []
  patterns: [Astro reference() for content relationships, JSON-LD in head slot]

key-files:
  created:
    - bambini-fussball/src/content/authors/redaktion.json
  modified:
    - bambini-fussball/src/content.config.ts
    - bambini-fussball/src/layouts/ArticleLayout.astro
    - bambini-fussball/src/pages/trainer/[id].astro
    - bambini-fussball/src/pages/eltern/[id].astro
    - bambini-fussball/src/pages/vereine/[id].astro
    - bambini-fussball/src/content/trainer/beispiel-artikel.md

key-decisions:
  - "Authors stored as JSON files with structured schema (name, bio, credentials, image, sameAs)"
  - "Article schema uses reference('authors') for type-safe author links"
  - "Author @id uses pattern: https://bambini-fussball.pages.dev/autor/{id}/"
  - "JSON-LD injected via head slot for proper document structure"

patterns-established:
  - "Content reference pattern: reference('collection') for related content"
  - "JSON-LD pattern: construct schema object, JSON.stringify() into script tag"
  - "Author resolution: getEntry(article.data.author) in dynamic routes"

# Metrics
duration: 4min
completed: 2026-01-22
---

# Phase 5 Plan 02: Article JSON-LD Schema Summary

**Authors collection with reference() support and Article JSON-LD schema with linked author entity on all article pages**

## Performance

- **Duration:** 4 min
- **Started:** 2026-01-22T12:57:00Z
- **Completed:** 2026-01-22T12:59:00Z
- **Tasks:** 2
- **Files modified:** 7

## Accomplishments
- Created authors collection with JSON schema (name, bio, credentials, image, sameAs array)
- Converted article author field from string to Astro reference('authors') for type safety
- Article JSON-LD schema outputs on all article pages with full Schema.org markup
- Author entity includes @id linking to /autor/{id}/ for entity disambiguation
- Author links in article headers now point to author profile URLs
- All three category routes (trainer, eltern, vereine) resolve author references

## Task Commits

Each task was committed atomically:

1. **Task 1: Create authors collection and update content config** - `f7493eb` (feat)
2. **Task 2: Add Article JSON-LD to ArticleLayout and update dynamic routes** - `dc9a075` (feat)

## Files Created/Modified
- `bambini-fussball/src/content/authors/redaktion.json` - Default author profile (created)
- `bambini-fussball/src/content.config.ts` - Added authors collection, changed author to reference()
- `bambini-fussball/src/layouts/ArticleLayout.astro` - Added Article JSON-LD schema, updated props
- `bambini-fussball/src/pages/trainer/[id].astro` - Added getEntry for author resolution
- `bambini-fussball/src/pages/eltern/[id].astro` - Added getEntry for author resolution
- `bambini-fussball/src/pages/vereine/[id].astro` - Added getEntry for author resolution
- `bambini-fussball/src/content/trainer/beispiel-artikel.md` - Updated author to reference syntax

## Decisions Made
- Author JSON schema includes sameAs array for social links (Schema.org pattern)
- Author @id follows URL pattern for potential future author pages
- Publisher in Article schema is Organization type with site URL
- Default image fallback to og-default.jpg when article has no image

## Deviations from Plan
None - plan executed exactly as written.

## Issues Encountered
None - both tasks completed successfully on first attempt.

## User Setup Required
- Future: Create `/images/authors/redaktion.jpg` for author profile image
- Future: Create author profile pages at `/autor/{id}/` to complete author entity linkage

## Next Phase Readiness
- Article JSON-LD complete with author entity linking
- Ready for sitemap.xml and robots.txt (05-03)
- Author profile pages can be added later without schema changes
- Note: Author profile URLs referenced in JSON-LD but pages not yet created - acceptable for SEO as @id is for entity identification

---
*Phase: 05-seo-infrastructure*
*Completed: 2026-01-22*
