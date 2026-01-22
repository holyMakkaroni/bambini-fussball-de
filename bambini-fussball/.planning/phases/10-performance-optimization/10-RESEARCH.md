# Phase 10: Performance Optimization - Research

**Researched:** 2026-01-22
**Domain:** Astro 5.x Image Optimization, WebP, Lazy Loading, Responsive Images
**Confidence:** HIGH

## Summary

This research covers implementing comprehensive image optimization for the bambini-fussball Astro site to meet PERF-01 (lazy loading), PERF-02 (WebP format), and PERF-03 (optimized sizes). The project already has the foundation in place with `image.layout: 'constrained'` and `image.responsiveStyles: true` configured in astro.config.mjs.

Astro 5.x provides built-in image optimization through the `<Image />` and `<Picture />` components from `astro:assets`. These components automatically convert images to WebP, generate responsive srcset attributes, apply lazy loading by default, and prevent CLS. The current content schema uses `z.string()` for image fields, which should be updated to use the `image()` helper for full optimization benefits.

**Primary recommendation:** Use Astro's built-in `<Image />` component with `layout="constrained"` for all content images, add `priority` attribute for above-the-fold images, and update content collection schemas to use the `image()` helper for automatic optimization of frontmatter images.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| astro:assets | Built-in (Astro 5.x) | Image optimization, WebP conversion, responsive images | Official Astro solution, zero dependencies |
| Sharp | Auto-installed | Build-time image processing | Default image service, industry standard |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| `<Image />` | Built-in | Single-format optimized images | Standard content images |
| `<Picture />` | Built-in | Multi-format images with fallbacks | When AVIF+WebP+fallback needed |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| `<Image />` | `<Picture />` | Picture generates more HTML but provides format fallbacks; use for broad browser support |
| Sharp | Squoosh | Squoosh has no native dependencies but Sharp is faster and recommended |
| Native lazy loading | IntersectionObserver | Native is simpler and sufficient for modern browsers |

**Installation:**
No additional installation required - all components are built into Astro 5.x.

## Architecture Patterns

### Recommended Project Structure
```
src/
  assets/              # All optimizable images (MUST be here, not public/)
    articles/          # Article featured images
    authors/           # Author profile images
    icons/             # Site icons (SVG)
  components/
    OptimizedImage.astro  # Wrapper component for consistent image handling
  content/
    trainer/           # Markdown with image references
    eltern/
    vereine/
```

### Pattern 1: Content Collection Images with image() Helper

**What:** Use Astro's `image()` schema helper to validate and auto-optimize frontmatter images
**When to use:** Any content collection with image fields
**Example:**
```typescript
// src/content.config.ts
import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const articleSchema = ({ image }) => z.object({
  title: z.string().max(60),
  description: z.string().max(160),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: reference('authors'),
  // Use image() helper instead of z.string()
  image: image().optional(),
  imageAlt: z.string().optional(),
});

const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});
```
Source: [Astro Content Collections Docs](https://docs.astro.build/en/guides/content-collections/)

### Pattern 2: Hero Image with Priority Loading

**What:** Use `priority` attribute for above-the-fold LCP images
**When to use:** Hero images, featured images visible on initial load
**Example:**
```astro
---
import { Image } from 'astro:assets';
import heroImage from '../assets/hero.jpg';
---

<!-- priority sets loading="eager", decoding="sync", fetchpriority="high" -->
<Image
  src={heroImage}
  alt="Bambini playing football"
  priority
  layout="constrained"
  width={1200}
  height={630}
/>
```
Source: [Astro Assets API Reference](https://docs.astro.build/en/reference/modules/astro-assets/)

### Pattern 3: Article Content Images with Lazy Loading

**What:** Default lazy loading for below-the-fold images
**When to use:** All images not visible on initial viewport
**Example:**
```astro
---
import { Image } from 'astro:assets';
// Image from content collection
const { data } = Astro.props.article;
---

<!-- Default: loading="lazy", decoding="async" -->
<Image
  src={data.image}
  alt={data.imageAlt || data.title}
  layout="constrained"
  width={800}
  height={450}
/>
```

### Pattern 4: Multi-Format Picture Component

**What:** Serve AVIF/WebP with fallback for maximum compatibility
**When to use:** Critical images where every byte matters
**Example:**
```astro
---
import { Picture } from 'astro:assets';
import myImage from '../assets/featured.jpg';
---

<Picture
  src={myImage}
  formats={['avif', 'webp']}
  fallbackFormat="jpg"
  alt="Description"
  layout="constrained"
  width={800}
  height={450}
/>
```
Source: [Astro Images Guide](https://docs.astro.build/en/guides/images/)

### Anti-Patterns to Avoid
- **Using `<img>` tags directly:** Bypasses all optimization; always use `<Image />` or `<Picture />`
- **Placing images in public/:** Images in public/ are NOT optimized; always use src/assets/
- **Lazy loading above-the-fold images:** Hurts LCP score; use `priority` for hero images
- **Missing alt text:** Accessibility violation; always provide descriptive alt
- **Using z.string() for image schemas:** Loses type safety and optimization; use `image()` helper

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| WebP conversion | Custom Sharp script | `<Image />` component | Handles format detection, caching, build integration |
| Responsive srcset | Manual srcset strings | `layout="constrained"` | Auto-generates breakpoints based on image dimensions |
| Lazy loading | IntersectionObserver JS | Native `loading="lazy"` | Built-in, default in Astro, better performance |
| CLS prevention | CSS aspect-ratio hacks | Astro's automatic width/height | Infers dimensions from source, applies automatically |
| Image caching | Manual cache headers | Astro build hashing | Content-hashed filenames enable aggressive caching |

**Key insight:** Astro's image pipeline handles the entire optimization chain at build time. Custom solutions add complexity without benefit and may miss edge cases (format support detection, cache invalidation, srcset breakpoint calculation).

## Common Pitfalls

### Pitfall 1: Missing Dimensions for Remote Images
**What goes wrong:** Build error or CLS in production
**Why it happens:** Remote images can't have dimensions auto-detected
**How to avoid:** Always provide `width` and `height` for remote images, or use `inferSize={true}`
**Warning signs:** "MissingImageDimension" error during build

### Pitfall 2: Lazy Loading the LCP Element
**What goes wrong:** Lighthouse reports "Largest Contentful Paint image was lazily loaded"
**Why it happens:** Hero/featured images default to lazy loading
**How to avoid:** Add `priority` attribute to above-the-fold images
**Warning signs:** LCP > 2.5s on mobile, Lighthouse warning

### Pitfall 3: Images in public/ Directory
**What goes wrong:** Images served unoptimized (original format, no srcset)
**Why it happens:** Developer habit from other frameworks
**How to avoid:** Always place optimizable images in `src/assets/`
**Warning signs:** Large file sizes in production, no WebP in network tab

### Pitfall 4: Using z.string() for Content Collection Images
**What goes wrong:** Images not processed, no type safety, manual path handling
**Why it happens:** Simpler initial implementation
**How to avoid:** Update schema to use `image()` helper with callback form
**Warning signs:** `data.image` returns string instead of ImageMetadata

### Pitfall 5: Missing imageAlt in Content Collections
**What goes wrong:** Accessibility violations, SEO impact
**Why it happens:** Alt text not required in schema
**How to avoid:** Add `imageAlt: z.string()` to schema alongside `image()`
**Warning signs:** Images with empty or missing alt attributes

## Code Examples

Verified patterns from official sources:

### Complete ArticleLayout with Optimized Image
```astro
---
// src/layouts/ArticleLayout.astro
import { Image } from 'astro:assets';
import BaseLayout from './BaseLayout.astro';

interface Props {
  title: string;
  description: string;
  image?: ImageMetadata;  // Type from image() helper
  imageAlt?: string;
  pubDate: Date;
}

const { title, description, image, imageAlt, pubDate } = Astro.props;
---

<BaseLayout title={title} description={description}>
  <article>
    {image && (
      <Image
        src={image}
        alt={imageAlt || title}
        layout="constrained"
        width={1200}
        height={630}
        priority  <!-- Above-the-fold, use eager loading -->
      />
    )}
    <h1>{title}</h1>
    <slot />
  </article>
</BaseLayout>
```
Source: [Astro Images Guide](https://docs.astro.build/en/guides/images/)

### Updated Content Schema with image() Helper
```typescript
// src/content.config.ts
import { defineCollection, z, reference } from 'astro:content';
import { glob } from 'astro/loaders';

const authors = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/authors' }),
  schema: ({ image }) => z.object({
    name: z.string(),
    bio: z.string().max(300),
    credentials: z.string(),
    image: image(),  // Author photo
    sameAs: z.array(z.string().url()).optional().default([])
  })
});

const articleSchema = ({ image }) => z.object({
  title: z.string().max(60, 'Title must be 60 characters or less'),
  description: z.string().max(160, 'Description must be 160 characters or less'),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: reference('authors'),
  image: image().optional(),  // Featured image (ImageMetadata)
  imageAlt: z.string().optional(),  // Alt text for accessibility
});

const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});

export const collections = { authors, trainer, /* ... */ };
```
Source: [Astro Content Collections](https://docs.astro.build/en/guides/content-collections/)

### ArticleCard with Optimized Thumbnail
```astro
---
// src/components/ArticleCard.astro
import { Image } from 'astro:assets';
import type { CollectionEntry } from 'astro:content';

interface Props {
  article: CollectionEntry<'trainer'> | CollectionEntry<'eltern'> | CollectionEntry<'vereine'>;
  basePath: string;
}

const { article, basePath } = Astro.props;
const { title, description, pubDate, image, imageAlt } = article.data;
---

<article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
  {image && (
    <Image
      src={image}
      alt={imageAlt || title}
      layout="constrained"
      width={400}
      height={225}
      class="w-full h-48 object-cover"
      <!-- No priority - card images are below fold -->
    />
  )}
  <div class="p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-2">
      <a href={`${basePath}${article.id}/`} class="hover:text-green-700">
        {title}
      </a>
    </h2>
    <p class="text-gray-600 mb-4">{description}</p>
  </div>
</article>
```

### Astro Config (Already Configured)
```javascript
// astro.config.mjs - Current configuration is correct
export default defineConfig({
  output: 'static',
  image: {
    layout: 'constrained',      // Default layout for all images
    responsiveStyles: true,     // Enable responsive CSS
  },
  // ...
});
```
Source: [Astro Configuration Reference](https://docs.astro.build/en/reference/configuration-reference/)

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| `@astrojs/image` integration | Built-in `astro:assets` | Astro 3.0 (2023) | No external dependency needed |
| Manual srcset strings | `layout` attribute | Astro 5.10 (2025) | Auto-generated responsive images |
| Experimental responsive images | Stable `image.responsiveStyles` | Astro 5.10 (2025) | No experimental flag needed |
| Custom lazy loading JS | Native `loading="lazy"` default | Astro 3.0+ | Zero JavaScript, better performance |

**Deprecated/outdated:**
- `@astrojs/image`: Replaced by built-in `astro:assets` in Astro 3.0+
- `experimental.responsiveImages`: Now stable as `image.layout` and `image.responsiveStyles`
- Squoosh image service: Sharp is default and recommended

## Open Questions

Things that couldn't be fully resolved:

1. **OG Image Format Limitations**
   - What we know: `image()` helper produces WebP which some platforms (Facebook) don't accept for og:image
   - What's unclear: Best practice for og:image with Astro's image pipeline
   - Recommendation: May need separate og:image handling with explicit format="jpg"

2. **Existing Content Migration**
   - What we know: Current schema uses `z.string()`, articles may have string paths
   - What's unclear: How many articles have images, what format paths are in
   - Recommendation: Audit existing content before schema migration

## Sources

### Primary (HIGH confidence)
- [Astro Images Guide](https://docs.astro.build/en/guides/images/) - Complete image optimization documentation
- [Astro Assets API Reference](https://docs.astro.build/en/reference/modules/astro-assets/) - Component props and attributes
- [Astro Configuration Reference](https://docs.astro.build/en/reference/configuration-reference/) - Image config options

### Secondary (MEDIUM confidence)
- [Lighthouse Performance Scoring](https://developer.chrome.com/docs/lighthouse/performance/performance-scoring) - Score calculation methodology
- [Astro Content Collections](https://docs.astro.build/en/guides/content-collections/) - Schema definition with image() helper
- [Cloudflare Pages Astro Guide](https://developers.cloudflare.com/pages/framework-guides/deploy-an-astro-site/) - Deployment context

### Tertiary (LOW confidence)
- Community blog posts on Astro image optimization (patterns verified against official docs)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Official Astro documentation is comprehensive and current
- Architecture: HIGH - Patterns derived from official examples
- Pitfalls: HIGH - Common issues documented in Astro error reference

**Research date:** 2026-01-22
**Valid until:** 2026-04-22 (3 months - Astro releases are frequent but image API is stable)
