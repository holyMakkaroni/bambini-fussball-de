---
phase: 7
plan: 1
subsystem: content
tags: [eltern, articles, german-content, parent-guides]
dependency-graph:
  requires: [phase-2-infrastructure, phase-3-legal, authors-collection]
  provides: [eltern-article-collection]
  affects: [seo-optimization, internal-linking, site-content]
tech-stack:
  patterns: [astro-content-collections, zod-validation, markdown-frontmatter]
key-files:
  created:
    - src/content/eltern/erster-tag-fussballverein.md
    - src/content/eltern/ausruestung-bambini-spieler.md
    - src/content/eltern/richtigen-fussballverein-finden.md
    - src/content/eltern/verhalten-spielfeldrand.md
    - src/content/eltern/fussball-foerdern-ohne-druck.md
  modified:
    - src/content/eltern/neue-spielformen-kinderfussball.md
decisions:
  - id: eltern-tone
    choice: empathetic-informal-du
    reason: Parent audience requires supportive, non-judgmental tone with informal address
  - id: content-length
    choice: 1600-1800-words
    reason: Comprehensive coverage while maintaining readability
metrics:
  duration: 9min
  completed: 2026-01-22
---

# Phase 7 Plan 1: Eltern Article Collection (Core) Summary

**One-liner:** 5 parent-focused German articles on first day guide, equipment, club selection, sideline behavior, and pressure-free support.

## What Was Built

Created 5 comprehensive parent-focused articles in the eltern content collection:

### 1. erster-tag-fussballverein.md (First Day Guide)
- Complete preparation guide for parents on first training day
- Covers: what to expect, packing list, emotional preparation, parent role during training
- Post-training guidance and handling common scenarios
- Word count: ~1,800 words

### 2. ausruestung-bambini-spieler.md (Equipment Guide)
- Practical guide on necessary gear for bambini players
- Covers: essentials, seasonal clothing, cost expectations
- Advice on buying used gear and avoiding overspending
- Word count: ~1,600 words

### 3. richtigen-fussballverein-finden.md (Club Selection)
- Comprehensive checklist for evaluating football clubs
- Covers: pre-visit research, probetraining observations, trainer evaluation
- Red flags to watch for, comparison of large vs small clubs
- Word count: ~1,700 words

### 4. verhalten-spielfeldrand.md (Sideline Behavior)
- Guide on supporting children from the sideline
- Covers: dos and don'ts, emotional self-regulation strategies
- Special situations: crying children, conflicts, problematic parents
- Word count: ~1,700 words

### 5. fussball-foerdern-ohne-druck.md (Pressure-Free Support)
- Deep dive on supporting children without creating pressure
- Covers: why pressure harms, how it emerges unintentionally
- Practical strategies for pressure-free encouragement
- Warning signs and long-term perspective
- Word count: ~1,800 words

## Technical Details

### Content Validation
All articles pass Zod schema validation:
- Title: max 60 characters (all verified)
- Description: max 160 characters (all verified)
- Author: `redaktion` reference (all verified)
- pubDate: 2026-01-22 (all verified)

### Content Style
- Language: German
- Tone: Empathetic, supportive, parent-focused
- Address: Informal "du" throughout
- Perspective: Parent (NOT trainer perspective)
- Structure: Clear headings, practical takeaways

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Fixed existing article title length**
- **Found during:** Build verification
- **Issue:** `neue-spielformen-kinderfussball.md` had title exceeding 60 characters
- **Fix:** Shortened title from "Neue Spielformen im Kinderfussball - Was Eltern wissen sollten" to "Neue Spielformen im Kinderfussball: Ein Uberblick"
- **Files modified:** src/content/eltern/neue-spielformen-kinderfussball.md
- **Commit:** Part of task execution (blocking issue for build)

## Commits

| Hash | Message |
|------|---------|
| d974858 | feat(07-01): add first day at football club guide for parents |
| e3b82df | feat(07-01): add equipment guide for bambini players |
| dc5e153 | feat(07-01): add club selection guide with checklist for parents |
| 8487a8d | feat(07-01): add sideline behavior guide for parents |
| 6d3014b | feat(07-01): add guide on supporting football without pressure |

## Verification

- [x] All 5 articles created in `src/content/eltern/`
- [x] All frontmatter follows schema (title <= 60, description <= 160)
- [x] All articles use `author: redaktion` reference
- [x] `npm run build` passes without errors
- [x] Content is in German with empathetic parent-focused tone
- [x] Word count 1,200-2,000 per article (all ~1,600-1,800)

## Files Created

```
src/content/eltern/
  erster-tag-fussballverein.md      (~1,800 words)
  ausruestung-bambini-spieler.md    (~1,600 words)
  richtigen-fussballverein-finden.md (~1,700 words)
  verhalten-spielfeldrand.md        (~1,700 words)
  fussball-foerdern-ohne-druck.md   (~1,800 words)
```

## Next Phase Readiness

The eltern content collection now has 5 core articles covering the most important topics for parents of bambini players. These articles:

1. Address common parent concerns and questions
2. Provide practical, actionable guidance
3. Maintain consistent empathetic tone
4. Are fully validated and build-ready

The collection can be expanded with additional articles (nutrition, FAQ, etc.) in future plans.
