# Phase 1: Foundation - Research

**Researched:** 2026-01-19
**Domain:** Astro 5.x + Tailwind CSS 4 + Cloudflare Pages
**Confidence:** HIGH

## Summary

This research covers the foundation setup for a content-driven website using Astro 5.x with Tailwind CSS 4, deployed to Cloudflare Pages. The stack is well-documented and stable, with Astro recently acquired by Cloudflare (January 2026), making this an officially supported deployment target.

Key findings: Tailwind CSS 4 requires the new Vite plugin approach (not the deprecated @astrojs/tailwind integration), content collections use the new Content Layer API with loaders in `src/content.config.ts`, and for static sites on Cloudflare Pages, no adapter is needed - just push to GitHub and configure build settings.

**Primary recommendation:** Use `npm create astro@latest` for project setup, add Tailwind via `@tailwindcss/vite` plugin, configure `trailingSlash: 'always'` for clean URLs with trailing slashes, and deploy as static site to Cloudflare Pages without the Cloudflare adapter.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| astro | 5.x (5.16+) | Static site framework | Content-first, zero-JS by default, acquired by Cloudflare |
| tailwindcss | 4.x | Utility CSS framework | 100x faster builds, CSS-first config, Vite plugin native |
| @tailwindcss/vite | 4.x | Vite plugin for Tailwind | Official method for Astro 5.2+, replaces @astrojs/tailwind |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| sharp | (bundled) | Image optimization | Included by default in Astro 5, no config needed |
| zod | (bundled) | Schema validation | Included via `astro/zod` for content collection schemas |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| @tailwindcss/vite | @astrojs/tailwind | **Don't use** - deprecated for Tailwind v4 |
| Cloudflare Pages | Cloudflare Workers | Workers recommended for new SSR projects, but Pages fine for static |

**Installation:**
```bash
# Create new project
npm create astro@latest bambini-fussball

# Install Tailwind CSS 4 with Vite plugin
npm install tailwindcss @tailwindcss/vite
```

## Architecture Patterns

### Recommended Project Structure
```
bambini-fussball/
├── public/
│   ├── robots.txt
│   └── favicon.svg
├── src/
│   ├── components/
│   │   ├── common/          # Reusable UI components
│   │   └── layout/          # Header, Footer, Navigation
│   ├── content/
│   │   ├── trainer/         # Trainer articles (Markdown)
│   │   ├── eltern/          # Eltern articles (Markdown)
│   │   └── vereine/         # Vereine articles (Markdown)
│   ├── layouts/
│   │   ├── BaseLayout.astro # HTML structure, head, scripts
│   │   └── ArticleLayout.astro # Single article page template
│   ├── pages/
│   │   ├── index.astro      # Homepage
│   │   ├── trainer/
│   │   │   └── [...slug].astro  # Dynamic route for trainer articles
│   │   ├── eltern/
│   │   │   └── [...slug].astro  # Dynamic route for eltern articles
│   │   └── vereine/
│   │       └── [...slug].astro  # Dynamic route for vereine articles
│   └── styles/
│       └── global.css       # Tailwind import + custom styles
├── src/content.config.ts    # Content collections configuration
├── astro.config.mjs         # Astro configuration
├── package.json
└── tsconfig.json
```

### Pattern 1: Content Collections with Glob Loader (Astro 5+)
**What:** Define content collections using the Content Layer API with glob loaders
**When to use:** All content-driven sites with Markdown/MDX content
**Example:**
```typescript
// src/content.config.ts
import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const trainerCollection = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/trainer" }),
  schema: z.object({
    title: z.string(),
    description: z.string(),
    pubDate: z.coerce.date(),
    category: z.string().optional(),
  })
});

const elternCollection = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/eltern" }),
  schema: z.object({
    title: z.string(),
    description: z.string(),
    pubDate: z.coerce.date(),
  })
});

const vereineCollection = defineCollection({
  loader: glob({ pattern: "**/*.md", base: "./src/content/vereine" }),
  schema: z.object({
    title: z.string(),
    description: z.string(),
    pubDate: z.coerce.date(),
  })
});

export const collections = {
  trainer: trainerCollection,
  eltern: elternCollection,
  vereine: vereineCollection,
};
```

### Pattern 2: Dynamic Routes with getStaticPaths
**What:** Generate static pages from content collections
**When to use:** All category listing and article pages
**Example:**
```typescript
// src/pages/trainer/[...slug].astro
---
import { getCollection } from 'astro:content';
import ArticleLayout from '../../layouts/ArticleLayout.astro';

export async function getStaticPaths() {
  const posts = await getCollection('trainer');
  return posts.map(post => ({
    params: { slug: post.id },
    props: { post },
  }));
}

const { post } = Astro.props;
const { Content } = await post.render();
---
<ArticleLayout title={post.data.title}>
  <Content />
</ArticleLayout>
```

### Pattern 3: German Umlaut Slug Conversion
**What:** Convert German umlauts in filenames/slugs to ASCII-safe equivalents
**When to use:** All content files with German titles
**Example:**
```typescript
// Helper function for slug generation (if needed in build scripts)
function germanSlugify(text: string): string {
  const umlautMap: Record<string, string> = {
    'ä': 'ae', 'ö': 'oe', 'ü': 'ue', 'ß': 'ss',
    'Ä': 'Ae', 'Ö': 'Oe', 'Ü': 'Ue'
  };

  return text
    .replace(/[äöüßÄÖÜ]/g, match => umlautMap[match] || match)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

// File naming convention:
// Content file: src/content/trainer/ballbeherrschung-uebungen.md
// Results in URL: /trainer/ballbeherrschung-uebungen/
```

### Pattern 4: Astro Configuration for Clean URLs
**What:** Configure trailing slashes and static output
**When to use:** Project setup
**Example:**
```javascript
// astro.config.mjs
import { defineConfig } from "astro/config";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  site: 'https://bambini-fussball.de', // Replace with actual domain
  trailingSlash: 'always',
  build: {
    format: 'directory' // Creates /path/index.html for clean URLs
  },
  vite: {
    plugins: [tailwindcss()],
  },
});
```

### Anti-Patterns to Avoid
- **Using @astrojs/tailwind with Tailwind v4:** This integration is deprecated. Use @tailwindcss/vite plugin directly.
- **Installing @astrojs/cloudflare for static sites:** No adapter needed for static sites on Cloudflare Pages.
- **Using legacy content collections syntax:** Use Content Layer API with loaders, not `type: 'content'`.
- **Putting configuration in tailwind.config.js:** Tailwind v4 uses CSS-first configuration with @theme directive.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Content type validation | Manual validation | Zod schemas in content.config.ts | Type-safe, build-time errors |
| URL slug generation | Custom slug function | Astro's file-based routing | Automatic from filename |
| Image optimization | Manual resizing | Astro's built-in Sharp | Automatic responsive images |
| CSS bundling | Manual CSS imports | Tailwind Vite plugin | Optimized, tree-shaken output |
| Static page generation | Manual HTML | getStaticPaths() | Built-in, type-safe |

**Key insight:** Astro handles most content site concerns automatically. Focus on content structure and let Astro/Tailwind handle optimization.

## Common Pitfalls

### Pitfall 1: Wrong Tailwind Integration
**What goes wrong:** Site builds but no Tailwind styles applied
**Why it happens:** Using deprecated @astrojs/tailwind instead of @tailwindcss/vite for Tailwind v4
**How to avoid:** Install `tailwindcss @tailwindcss/vite`, add Vite plugin to astro.config.mjs
**Warning signs:** `@import "tailwindcss"` in CSS not working, no utility classes applied

### Pitfall 2: Content Collections Config Location
**What goes wrong:** Content collections not recognized
**Why it happens:** Config file in wrong location or using old syntax
**How to avoid:** Use `src/content.config.ts` (not `src/content/config.ts`), use glob loaders
**Warning signs:** TypeScript errors about missing collections, empty collection queries

### Pitfall 3: Cloudflare Pages Output Directory
**What goes wrong:** Deployment fails or shows blank page
**Why it happens:** Wrong output directory configured (using `public` instead of `dist`)
**How to avoid:** Set build output directory to `dist` in Cloudflare Pages settings
**Warning signs:** Build succeeds but pages are empty or 404

### Pitfall 4: Missing Trailing Slash Configuration
**What goes wrong:** Duplicate content, SEO issues, broken internal links
**Why it happens:** Astro defaults to `trailingSlash: 'ignore'`
**How to avoid:** Set `trailingSlash: 'always'` in astro.config.mjs, use consistent linking
**Warning signs:** Same page accessible with and without trailing slash

### Pitfall 5: Cloudflare Auto Minify
**What goes wrong:** Hydration errors on interactive components
**Why it happens:** Cloudflare's Auto Minify conflicts with framework hydration
**How to avoid:** Disable Auto Minify in Cloudflare dashboard (Speed > Optimization > Auto Minify)
**Warning signs:** Console errors about hydration mismatches

### Pitfall 6: Legacy Content Collections in Astro 6
**What goes wrong:** Build fails after Astro upgrade
**Why it happens:** Astro 6 removes legacy content collections support
**How to avoid:** Use Content Layer API from the start with glob/file loaders
**Warning signs:** Deprecation warnings in Astro 5, errors in Astro 6

## Code Examples

Verified patterns from official sources:

### Tailwind CSS 4 Setup
```css
/* src/styles/global.css */
@import "tailwindcss";

/* Optional: Custom design tokens */
@theme {
  --color-primary: #1e40af;
  --color-secondary: #9333ea;
}
```

### Global CSS Import in Layout
```astro
---
// src/layouts/BaseLayout.astro
import '../styles/global.css';

interface Props {
  title: string;
  description?: string;
}

const { title, description = 'Bambini Fussball - Tipps und Tricks' } = Astro.props;
---
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content={description} />
    <title>{title}</title>
  </head>
  <body class="min-h-screen bg-white">
    <slot />
  </body>
</html>
```

### Querying Content Collections
```astro
---
// src/pages/trainer/index.astro
import { getCollection } from 'astro:content';
import BaseLayout from '../../layouts/BaseLayout.astro';

const allPosts = await getCollection('trainer');
const sortedPosts = allPosts.sort((a, b) =>
  b.data.pubDate.getTime() - a.data.pubDate.getTime()
);
---
<BaseLayout title="Trainer Tipps">
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Trainer Tipps</h1>
    <ul class="space-y-4">
      {sortedPosts.map(post => (
        <li>
          <a href={`/trainer/${post.id}/`} class="text-blue-600 hover:underline">
            {post.data.title}
          </a>
        </li>
      ))}
    </ul>
  </main>
</BaseLayout>
```

### Sample Markdown Content File
```markdown
---
# src/content/trainer/ballbeherrschung-uebungen.md
title: "Ballbeherrschung Uebungen fuer Bambinis"
description: "Einfache Uebungen zur Verbesserung der Ballbeherrschung"
pubDate: 2026-01-15
category: "technik"
---

# Ballbeherrschung Uebungen

Hier sind einige einfache Uebungen...
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| @astrojs/tailwind | @tailwindcss/vite | Astro 5.2 / Tailwind 4 | Must use Vite plugin |
| tailwind.config.js | CSS @theme directive | Tailwind 4.0 | Configuration in CSS |
| src/content/config.ts | src/content.config.ts | Astro 5.0 | Config file location moved |
| type: 'content' | glob() loader | Astro 5.0 | Content Layer API |
| z from astro:content | z from astro/zod | Astro 5.0+ | Import path change |
| Cloudflare Pages (primary) | Cloudflare Workers (recommended) | 2025 | Pages still works for static |

**Deprecated/outdated:**
- `@astrojs/tailwind`: Use @tailwindcss/vite for Tailwind v4
- Squoosh image service: Removed in Astro 5, Sharp is default
- Legacy content collections: Use Content Layer API with loaders

## Open Questions

Things that couldn't be fully resolved:

1. **Cloudflare-Astro Integration Post-Acquisition**
   - What we know: Cloudflare acquired Astro team in January 2026
   - What's unclear: Whether this will change recommended deployment patterns
   - Recommendation: Use current documented approach, monitor for updates

2. **Astro 6 Timeline**
   - What we know: Astro 6 beta coming soon, removes legacy content collections
   - What's unclear: Exact release date
   - Recommendation: Use Content Layer API to be future-proof

## Sources

### Primary (HIGH confidence)
- [Astro Installation Docs](https://docs.astro.build/en/install-and-setup/) - Project setup, CLI commands
- [Tailwind CSS Astro Guide](https://tailwindcss.com/docs/installation/framework-guides/astro) - Official Vite plugin setup
- [Astro Content Collections](https://docs.astro.build/en/guides/content-collections/) - Content Layer API
- [Astro Configuration Reference](https://docs.astro.build/en/reference/configuration-reference/) - trailingSlash, build options
- [Astro Routing Guide](https://docs.astro.build/en/guides/routing/) - getStaticPaths, dynamic routes
- [Cloudflare Pages Astro Deployment](https://developers.cloudflare.com/pages/framework-guides/deploy-an-astro-site/) - Deployment settings

### Secondary (MEDIUM confidence)
- [Astro 5.0 Blog Post](https://astro.build/blog/astro-5/) - Feature announcements
- [Tailwind CSS 4 Upgrade Guide](https://tailwindcss.com/docs/upgrade-guide) - Breaking changes
- [Cloudflare Workers Astro Docs](https://developers.cloudflare.com/workers/framework-guides/web-apps/astro/) - Workers vs Pages

### Tertiary (LOW confidence)
- Various blog posts about Cloudflare Pages gotchas - marked for validation during implementation

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Official documentation, current as of 2026
- Architecture: HIGH - File-based patterns from official docs
- Pitfalls: MEDIUM - Mix of official docs and community experience

**Research date:** 2026-01-19
**Valid until:** 2026-02-19 (30 days - stack is stable)
