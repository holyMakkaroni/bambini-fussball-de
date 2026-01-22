---
phase: 10
plan: 01
subsystem: image-optimization
tags:
  - astro
  - images
  - performance
  - webp
  - lazy-loading

dependency-graph:
  requires: []
  provides:
    - content-schema-image-helper
    - optimized-hero-images
    - optimized-thumbnail-images
  affects:
    - 10-02 (font optimization plan)

tech-stack:
  added: []
  patterns:
    - astro-image-component
    - content-collection-image-schema

key-files:
  created: []
  modified:
    - src/content.config.ts
    - src/layouts/ArticleLayout.astro
    - src/components/ArticleCard.astro
    - src/components/RelatedArticles.astro
    - src/pages/trainer/[id].astro
    - src/pages/eltern/[id].astro
    - src/pages/vereine/[id].astro

decisions:
  - id: keep-author-string-images
    context: Author images stored in /public/images/authors/ use string paths
    choice: Keep z.string() for authors schema instead of image() helper
    reason: Public path images cannot be resolved by image() helper which expects importable paths

metrics:
  duration: ~6 minutes
  completed: 2026-01-22
---

# Phase 10 Plan 01: Enable Astro Image Optimization Summary

Astro image() helper enabled for article content collections with Image component rendering for automatic WebP conversion, responsive srcset, and lazy loading.

## What Was Done

### Task 1: Update content schema to use image() helper
- Changed `articleSchema` from plain object to function form receiving `{ image }` parameter
- Replaced `z.string().optional()` with `image().optional()` for article image fields
- Added `imageAlt: z.string().optional()` field for accessibility
- Kept `authors` schema using `z.string()` since author images use public paths
- **Commit:** 3894fc9

### Task 2: Update ArticleLayout for optimized hero images
- Imported `Image` component from `astro:assets` and `ImageMetadata` type
- Updated Props interface to use `ImageMetadata` for image field
- Added hero image rendering with priority loading (`loading="eager"`, `decoding="sync"`)
- Updated JSON-LD schema to handle `ImageMetadata.src` for og:image
- Updated all three category page files to pass `imageAlt` prop
- **Commit:** 41e182c

### Task 3: Update ArticleCard and RelatedArticles for thumbnail images
- Added `Image` component import to both components
- ArticleCard: 400x225 thumbnails with default lazy loading
- RelatedArticles: 300x169 smaller thumbnails for grid items
- Both use `imageAlt` with title fallback for accessibility
- Restructured card layout to accommodate images properly
- **Commit:** 6339409

## Deviations from Plan

### Auto-fixed Issues

**1. [Rule 3 - Blocking] Author images use public paths**
- **Found during:** Task 1
- **Issue:** Build failed because `image()` helper tried to resolve `/images/authors/redaktion.jpg` which is a public path, not an importable path
- **Fix:** Kept `authors` schema using `z.string()` for public path images while using `image()` for article content collections
- **Files modified:** src/content.config.ts
- **Commit:** 3894fc9

## Technical Details

### Image Component Configuration
- Hero images: `width={1200} height={630}` with `loading="eager" decoding="sync"` for LCP optimization
- ArticleCard thumbnails: `width={400} height={225}` with default lazy loading
- RelatedArticles thumbnails: `width={300} height={169}` with default lazy loading

### Schema Pattern
```typescript
const articleSchema = ({ image }) => z.object({
  // ... other fields
  image: image().optional(),
  imageAlt: z.string().optional(),
});
```

### Image Rendering Pattern
```astro
{image && (
  <Image
    src={image}
    alt={imageAlt || title}
    width={1200}
    height={630}
    loading="eager"
    class="w-full rounded-lg"
  />
)}
```

## Verification Results

- Build completes successfully with no errors
- No TypeScript type errors
- Existing articles without images still validate and render
- Schema properly accepts ImageMetadata type from content collections

## Success Criteria Met

- [x] content.config.ts uses image() helper for article image fields
- [x] ArticleLayout renders hero images with priority loading attributes
- [x] ArticleCard renders thumbnails with default lazy loading
- [x] RelatedArticles renders thumbnails with default lazy loading
- [x] Build succeeds with no errors
- [x] All existing content (without images) still validates and renders

## Next Phase Readiness

The image optimization infrastructure is in place. To actually see WebP conversion and responsive srcset output, articles need to include images in their frontmatter using relative paths to images stored in `src/` (not `public/`).

Example article frontmatter:
```yaml
---
title: "Example Article"
image: "./images/hero.jpg"
imageAlt: "Description of the image"
---
```

The next plan (10-02) should address font optimization for additional performance gains.
