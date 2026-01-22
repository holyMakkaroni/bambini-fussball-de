# Phase 4: Content Architecture - Research

**Researched:** 2026-01-22
**Domain:** Astro 5.x Content Collections, Pillar-Cluster SEO Architecture, Breadcrumb Navigation
**Confidence:** HIGH

## Summary

This phase implements the pillar-cluster content structure with three audience-specific categories (Trainer, Eltern, Vereine). The existing `content.config.ts` uses Astro 4's legacy `type: 'content'` syntax which requires migration to Astro 5's Content Layer API with `glob()` loaders.

Astro 5.x introduced significant changes to content collections: the `slug` property is replaced by `id`, the `render()` method moved to a standalone import, and collections now require explicit `loader` configuration. The current project already has the three collection directories in `src/content/` but they only contain `.gitkeep` placeholder files.

For breadcrumb navigation, the `astro-breadcrumbs` package provides zero-config automatic breadcrumb generation with built-in Schema.org JSON-LD structured data support and accessibility compliance. This is the recommended solution over hand-rolling breadcrumbs.

**Primary recommendation:** Migrate content.config.ts to Astro 5 loader syntax, create pillar pages at `/trainer/index.astro`, `/eltern/index.astro`, `/vereine/index.astro`, add dynamic article routes, install `astro-breadcrumbs` for navigation, and create a Header component with navigation menu.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| astro:content | 5.16.11 (built-in) | Content collections API | Native Astro solution, type-safe, optimized |
| astro/loaders | 5.16.11 (built-in) | glob() and file() loaders | Official Astro 5 content loading mechanism |
| astro/zod | 5.16.11 (built-in) | Schema validation | Type-safe frontmatter validation |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| astro-breadcrumbs | latest | Breadcrumb navigation | Article pages requiring hierarchical navigation |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| astro-breadcrumbs | Custom component | Hand-rolling loses Schema.org JSON-LD, accessibility features, truncation |
| Three separate collections | Single collection with category field | Separate collections provide cleaner type separation, but require more boilerplate |

**Installation:**
```bash
npm install astro-breadcrumbs
```

## Architecture Patterns

### Recommended Project Structure
```
src/
├── content/
│   ├── trainer/           # Trainer articles (Markdown)
│   │   └── example-article.md
│   ├── eltern/            # Eltern articles (Markdown)
│   │   └── example-article.md
│   └── vereine/           # Vereine articles (Markdown)
│       └── example-article.md
├── content.config.ts      # Collection definitions with glob() loaders
├── pages/
│   ├── trainer/
│   │   ├── index.astro    # Pillar page with article listing
│   │   └── [id].astro     # Dynamic article routes
│   ├── eltern/
│   │   ├── index.astro    # Pillar page with article listing
│   │   └── [id].astro     # Dynamic article routes
│   ├── vereine/
│   │   ├── index.astro    # Pillar page with article listing
│   │   └── [id].astro     # Dynamic article routes
│   └── index.astro        # Homepage (already exists)
├── components/
│   ├── Header.astro       # Navigation with category links (NEW)
│   ├── Footer.astro       # Already exists
│   ├── Breadcrumbs.astro  # Wrapper for astro-breadcrumbs (optional)
│   └── ArticleCard.astro  # Article preview card for listings (NEW)
└── layouts/
    ├── BaseLayout.astro   # Already exists
    └── ArticleLayout.astro # Layout for article pages with breadcrumbs (NEW)
```

### Pattern 1: Astro 5 Content Layer Configuration
**What:** Define collections using glob() loader instead of legacy type: 'content'
**When to use:** All content collections in Astro 5.x
**Example:**
```typescript
// src/content.config.ts
// Source: https://docs.astro.build/en/guides/content-collections/
import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const articleSchema = z.object({
  title: z.string(),
  description: z.string(),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: z.string().default('Redaktion'),
});

const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});

const eltern = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/eltern' }),
  schema: articleSchema,
});

const vereine = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/vereine' }),
  schema: articleSchema,
});

export const collections = { trainer, eltern, vereine };
```

### Pattern 2: Dynamic Article Routes with getStaticPaths
**What:** Generate static pages for each article in a collection
**When to use:** Article detail pages
**Example:**
```astro
---
// src/pages/trainer/[id].astro
// Source: https://docs.astro.build/en/guides/routing/
import { getCollection, render } from 'astro:content';
import ArticleLayout from '../../layouts/ArticleLayout.astro';

export async function getStaticPaths() {
  const articles = await getCollection('trainer');
  return articles.map((article) => ({
    params: { id: article.id },
    props: { article },
  }));
}

const { article } = Astro.props;
const { Content, headings } = await render(article);
---

<ArticleLayout
  title={article.data.title}
  description={article.data.description}
  category="trainer"
  categoryLabel="Trainer"
>
  <Content />
</ArticleLayout>
```

### Pattern 3: Pillar/Category Overview Pages
**What:** List all articles in a category with descriptions
**When to use:** Category landing pages (/trainer/, /eltern/, /vereine/)
**Example:**
```astro
---
// src/pages/trainer/index.astro
// Source: https://docs.astro.build/en/guides/content-collections/
import { getCollection } from 'astro:content';
import BaseLayout from '../../layouts/BaseLayout.astro';
import ArticleCard from '../../components/ArticleCard.astro';

const articles = await getCollection('trainer');
// Sort by publication date, newest first
const sortedArticles = articles.sort(
  (a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf()
);
---

<BaseLayout
  title="Trainer-Ratgeber | Bambini-Fussball"
  description="Trainingstipps, Ubungen und Methodik fur die Arbeit mit 4-6-jahrigen Fussballern"
>
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-green-700 mb-6">Fur Trainer</h1>
    <p class="text-lg text-gray-600 mb-8 max-w-3xl">
      Trainingstipps, Ubungen und Methodik fur die Arbeit mit 4-6-jahrigen Fussballern.
    </p>

    {sortedArticles.length > 0 ? (
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        {sortedArticles.map((article) => (
          <ArticleCard article={article} basePath="/trainer/" />
        ))}
      </div>
    ) : (
      <p class="text-gray-500">Artikel folgen in Kurze.</p>
    )}
  </main>
</BaseLayout>
```

### Pattern 4: Breadcrumb Navigation with astro-breadcrumbs
**What:** Automatic breadcrumb generation with Schema.org JSON-LD
**When to use:** All article pages
**Example:**
```astro
---
// src/layouts/ArticleLayout.astro
import BaseLayout from './BaseLayout.astro';
import { Breadcrumbs } from 'astro-breadcrumbs';
import 'astro-breadcrumbs/breadcrumbs.css';

interface Props {
  title: string;
  description: string;
  category: string;
  categoryLabel: string;
}

const { title, description, category, categoryLabel } = Astro.props;

// Custom breadcrumb items for semantic clarity
const crumbs = [
  { text: 'Startseite', href: '/' },
  { text: categoryLabel, href: `/${category}/` },
  { text: title, href: '#', 'aria-current': 'page' }
];
---

<BaseLayout title={`${title} | Bambini-Fussball`} description={description}>
  <main class="container mx-auto px-4 py-8">
    <Breadcrumbs
      crumbs={crumbs}
      indexText="Startseite"
      schemaJsonScript={true}
    />
    <article class="prose prose-lg max-w-3xl mx-auto mt-6">
      <h1>{title}</h1>
      <slot />
    </article>
  </main>
</BaseLayout>
```

### Pattern 5: Navigation Header with Category Links
**What:** Header component providing access to all three audience categories
**When to use:** Site-wide navigation in BaseLayout
**Example:**
```astro
---
// src/components/Header.astro
---

<header class="bg-white border-b border-gray-200">
  <nav class="container mx-auto px-4 py-4" aria-label="Hauptnavigation">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <a href="/" class="text-xl font-bold text-green-700 hover:text-green-800">
        Bambini-Fussball
      </a>
      <ul class="flex flex-wrap gap-2 md:gap-6">
        <li>
          <a
            href="/trainer/"
            class="min-h-[44px] inline-flex items-center px-3 py-2 text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg font-medium"
          >
            Trainer
          </a>
        </li>
        <li>
          <a
            href="/eltern/"
            class="min-h-[44px] inline-flex items-center px-3 py-2 text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg font-medium"
          >
            Eltern
          </a>
        </li>
        <li>
          <a
            href="/vereine/"
            class="min-h-[44px] inline-flex items-center px-3 py-2 text-gray-700 hover:text-amber-700 hover:bg-amber-50 rounded-lg font-medium"
          >
            Vereine
          </a>
        </li>
      </ul>
    </div>
  </nav>
</header>
```

### Anti-Patterns to Avoid
- **Using legacy type: 'content' in Astro 5:** Migrate to glob() loader for future compatibility
- **Using post.slug instead of post.id:** Astro 5 replaced slug with id
- **Using post.render() method:** Import render() as standalone function
- **Hard-coding breadcrumbs without Schema.org:** Use astro-breadcrumbs for SEO benefit
- **Creating article routes without getStaticPaths:** Required for static site generation
- **Unsorted article listings:** Always sort by pubDate for consistent user experience

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Breadcrumb navigation | Custom component | astro-breadcrumbs | Schema.org JSON-LD, accessibility, truncation support included |
| Content validation | Manual checks | Zod schema | Type-safe, catches errors at build time |
| Article ID generation | Custom slug logic | glob() loader | Automatic URL-friendly IDs from filenames |
| Date parsing | Manual Date() | z.coerce.date() | Handles string dates in frontmatter correctly |

**Key insight:** Astro's content collections handle most of the complexity. The main work is defining schemas and creating the page templates. Don't add complexity by bypassing the built-in tooling.

## Common Pitfalls

### Pitfall 1: Legacy Content Config Syntax
**What goes wrong:** Using `type: 'content'` without loader fails in Astro 5
**Why it happens:** Migration from Astro 4 left legacy syntax
**How to avoid:** Always use `loader: glob({ pattern, base })` syntax
**Warning signs:** Build errors about missing loader, collection not found

### Pitfall 2: Using slug Instead of id
**What goes wrong:** `article.slug` is undefined in Astro 5
**Why it happens:** Astro 5 replaced `slug` with `id`
**How to avoid:** Use `article.id` for URLs and route parameters
**Warning signs:** Dynamic routes show "undefined" in URLs

### Pitfall 3: Calling render() as Method
**What goes wrong:** `article.render()` throws "not a function" error
**Why it happens:** Astro 5 moved render() to standalone import
**How to avoid:** `import { render } from 'astro:content'` then `await render(article)`
**Warning signs:** Runtime error about render not being a function

### Pitfall 4: Non-Deterministic Article Order
**What goes wrong:** Articles appear in random order on category pages
**Why it happens:** getCollection() does not guarantee sort order
**How to avoid:** Always sort results explicitly: `articles.sort((a, b) => b.data.pubDate - a.data.pubDate)`
**Warning signs:** Article order changes between builds

### Pitfall 5: Missing z.coerce for Dates
**What goes wrong:** Date validation fails on YAML frontmatter dates
**Why it happens:** YAML dates come as strings, not Date objects
**How to avoid:** Use `z.coerce.date()` instead of `z.date()`
**Warning signs:** "Expected date, received string" validation errors

### Pitfall 6: Forgetting Trailing Slashes
**What goes wrong:** Links to /trainer vs /trainer/ cause 404 or redirects
**Why it happens:** Astro config has `trailingSlash: 'always'`
**How to avoid:** Always include trailing slashes in internal links
**Warning signs:** Unexpected redirects, SEO issues with canonical URLs

## Code Examples

Verified patterns from official sources:

### Markdown Article Frontmatter Template
```markdown
---
title: "Beispielartikel Titel"
description: "Eine kurze Beschreibung des Artikels fur SEO und Vorschau."
pubDate: 2026-01-22
author: "Redaktion"
---

# Artikelinhalt

Hier beginnt der eigentliche Artikeltext...
```

### ArticleCard Component
```astro
---
// src/components/ArticleCard.astro
import type { CollectionEntry } from 'astro:content';

interface Props {
  article: CollectionEntry<'trainer'> | CollectionEntry<'eltern'> | CollectionEntry<'vereine'>;
  basePath: string;
}

const { article, basePath } = Astro.props;
const { title, description, pubDate } = article.data;
---

<article class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
  <h2 class="text-xl font-semibold text-gray-900 mb-2">
    <a href={`${basePath}${article.id}/`} class="hover:text-green-700">
      {title}
    </a>
  </h2>
  <p class="text-gray-600 mb-4">{description}</p>
  <time
    datetime={pubDate.toISOString()}
    class="text-sm text-gray-500"
  >
    {pubDate.toLocaleDateString('de-DE', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })}
  </time>
</article>
```

### Querying Collection with Filter
```typescript
// Filter for published articles only (if draft field exists)
const publishedArticles = await getCollection('trainer', ({ data }) => {
  return data.draft !== true;
});
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| `type: 'content'` | `loader: glob()` | Astro 5.0 | Required migration for all collections |
| `post.slug` | `post.id` | Astro 5.0 | All slug references must be updated |
| `post.render()` | `render(post)` | Astro 5.0 | Import render from astro:content |
| `src/content/config.ts` | `src/content.config.ts` | Astro 5.0 | File must be in src root |
| Manual JSON-LD | astro-breadcrumbs | Current | Automatic Schema.org structured data |

**Deprecated/outdated:**
- `type: 'content'` and `type: 'data'` without loader - use glob() or file() instead
- `entry.slug` - use `entry.id` instead
- `entry.render()` method - import and call `render(entry)` instead

## Open Questions

Things that couldn't be fully resolved:

1. **Prose styling for article content**
   - What we know: Tailwind CSS Typography plugin (@tailwindcss/typography) provides prose classes
   - What's unclear: Whether to install additional plugin or use custom article styles
   - Recommendation: Check if prose classes work with Tailwind 4, otherwise use custom styles

2. **astro-breadcrumbs CSS customization**
   - What we know: Package includes default CSS with BEM classes and CSS variables
   - What's unclear: How well default styling integrates with existing Tailwind design
   - Recommendation: Start with default CSS, customize CSS variables if needed

## Sources

### Primary (HIGH confidence)
- [Astro Content Collections Guide](https://docs.astro.build/en/guides/content-collections/) - Collection definition, querying, rendering
- [Astro Content API Reference](https://docs.astro.build/en/reference/modules/astro-content/) - getCollection, getEntry, render signatures
- [Astro Routing Guide](https://docs.astro.build/en/guides/routing/) - Dynamic routes, getStaticPaths
- [Astro v5 Upgrade Guide](https://docs.astro.build/en/guides/upgrade-to/v5/) - Migration from slug to id, render changes

### Secondary (MEDIUM confidence)
- [Astro Breadcrumbs Documentation](https://docs.astro-breadcrumbs.kasimir.dev/) - Installation, properties, Schema.org support
- [GitHub: astro-breadcrumbs](https://github.com/felix-berlin/astro-breadcrumbs) - Package source, features

### Tertiary (LOW confidence)
- WebSearch results on pillar-cluster SEO patterns - General strategy, not Astro-specific

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Official Astro documentation verified
- Architecture: HIGH - Based on official Astro patterns and upgrade guide
- Pitfalls: HIGH - Directly from Astro 5 migration guide
- Breadcrumbs: MEDIUM - Package documentation verified, integration not tested

**Research date:** 2026-01-22
**Valid until:** 2026-04-22 (90 days - Astro framework is stable)
