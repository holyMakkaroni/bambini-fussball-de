# Phase 9: Related Content System - Research

**Researched:** 2026-01-22
**Domain:** Astro 5.x Content Collections, Related Content Algorithms
**Confidence:** HIGH

## Summary

This research investigates how to implement a related articles system for the bambini-fussball site using Astro 5.x Content Collections. The site has three content collections (trainer, eltern, vereine) with 8-10 articles each, totaling approximately 28 articles. Each article currently has title, description, pubDate, and author fields but no tags.

The recommended approach is **same-category related articles** without adding tags to frontmatter. Given the small corpus size (8-10 articles per category) and focused topic domain, filtering by category and excluding the current article provides sufficient relevance while keeping implementation simple. This approach requires no schema changes, no content updates, and leverages existing collection structure.

**Primary recommendation:** Display 3-4 related articles from the same category, sorted by publication date (newest first), excluding the current article. Create a dedicated RelatedArticles.astro component that receives the current article's category and ID, then queries getCollection() to find related content.

## Standard Stack

The established approach for this domain uses Astro's built-in content collection APIs.

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| astro:content | Built-in (Astro 5.16+) | getCollection(), getEntry() for content queries | Official API, type-safe, optimized |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| CollectionKey type | Built-in | Type-safe collection name handling | When querying dynamically across collections |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Same-category filtering | Tags-based matching | Tags require schema changes + content updates for 28 articles; overkill for small corpus |
| Same-category filtering | Manual relatedPosts references | Requires content authors to maintain cross-references; error-prone |
| Same-category filtering | Cross-category keyword matching | More complex, diminishing returns for focused topic domain |

**Installation:**
No additional packages required. Uses built-in Astro content APIs.

## Architecture Patterns

### Recommended Project Structure
```
src/
  components/
    RelatedArticles.astro       # New - displays related articles section
  layouts/
    ArticleLayout.astro         # Modified - includes RelatedArticles
  pages/
    trainer/[id].astro          # Modified - passes category + articleId
    eltern/[id].astro           # Modified - passes category + articleId
    vereine/[id].astro          # Modified - passes category + articleId
```

### Pattern 1: Category-Based Related Content
**What:** Query articles from the same collection, exclude current article, limit to N results
**When to use:** Small corpus (under 50 articles per category), consistent topic domain
**Example:**
```typescript
// Source: Astro Content Collections API
// https://docs.astro.build/en/reference/modules/astro-content/

import { getCollection } from 'astro:content';
import type { CollectionEntry } from 'astro:content';

interface Props {
  category: 'trainer' | 'eltern' | 'vereine';
  currentArticleId: string;
  limit?: number;
}

const { category, currentArticleId, limit = 4 } = Astro.props;

// Get all articles from same category, excluding current
const relatedArticles = (await getCollection(category))
  .filter(article => article.id !== currentArticleId)
  .sort((a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf())
  .slice(0, limit);
```

### Pattern 2: Component Integration in Layout
**What:** RelatedArticles component receives props from ArticleLayout
**When to use:** When related content should appear on all articles consistently
**Example:**
```astro
// ArticleLayout.astro modification
<article class="prose">
  <slot />
</article>

<!-- Add after article content, inside main -->
<RelatedArticles
  category={category}
  currentArticleId={articleId}
  limit={4}
/>
```

### Pattern 3: Utility Function for Reusability
**What:** Extract related articles logic into reusable function
**When to use:** When same logic might be needed elsewhere (e.g., sidebar, homepage)
**Example:**
```typescript
// src/utils/content.ts
import { getCollection } from 'astro:content';
import type { CollectionEntry } from 'astro:content';

type Category = 'trainer' | 'eltern' | 'vereine';

export async function getRelatedArticles(
  category: Category,
  excludeId: string,
  limit: number = 4
): Promise<CollectionEntry<Category>[]> {
  return (await getCollection(category))
    .filter(article => article.id !== excludeId)
    .sort((a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf())
    .slice(0, limit);
}
```

### Anti-Patterns to Avoid
- **Querying in loop:** Never call getCollection() inside a map/filter. Query once, then filter in memory.
- **Over-engineering relations:** For 28 articles, manual relatedPosts references or complex algorithms add maintenance burden without proportional benefit.
- **Ignoring edge cases:** Always handle the case where category has fewer than N articles.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Content querying | Custom file readers | getCollection() | Type-safe, cached, handles loaders |
| Related by tags | Custom tag-matching algorithm | Category filtering (for now) | 28 articles don't need sophisticated matching |
| Type inference | Manual type definitions | CollectionEntry<'collection'> | Auto-generated from schema |

**Key insight:** The site's content structure (3 focused categories) already provides semantic grouping. Adding tags would duplicate this categorization without adding value for the current corpus size.

## Common Pitfalls

### Pitfall 1: Prop Drilling Category Information
**What goes wrong:** ArticleLayout doesn't receive enough information to query related articles
**Why it happens:** The layout receives rendered content but not the collection context
**How to avoid:** Pass category and articleId as explicit props from [id].astro pages to ArticleLayout
**Warning signs:** Unable to access collection name or article ID within layout

### Pitfall 2: Type Errors with Dynamic Collections
**What goes wrong:** TypeScript errors when category is a variable instead of literal
**Why it happens:** getCollection() expects literal collection names for type inference
**How to avoid:** Use type assertion or CollectionKey type with proper guards
**Warning signs:** "Argument of type 'string' is not assignable to parameter of type 'trainer' | 'eltern' | 'vereine'"

### Pitfall 3: N+1 Query Pattern
**What goes wrong:** Calling getEntry() or render() for each related article in template
**Why it happens:** Wanting to show rendered content or resolved references
**How to avoid:** For preview display, only use data.title and data.description which are already available
**Warning signs:** Build times increasing with article count

### Pitfall 4: Empty State Handling
**What goes wrong:** Crash or broken UI when category has 0-3 articles
**Why it happens:** Not checking array length before rendering
**How to avoid:** Conditional rendering: only show section if relatedArticles.length > 0
**Warning signs:** Empty sections, layout shifts, broken grids

### Pitfall 5: Self-Reference in Related Articles
**What goes wrong:** Current article appears in its own related articles list
**Why it happens:** Forgetting to filter by currentArticleId
**How to avoid:** Always filter: `.filter(article => article.id !== currentArticleId)`
**Warning signs:** Article linking to itself in related section

## Code Examples

Verified patterns from official sources:

### Query and Filter Collection
```typescript
// Source: https://docs.astro.build/en/reference/modules/astro-content/
import { getCollection } from 'astro:content';

// Get all published posts from a collection, sorted by date
const publishedPosts = (await getCollection('trainer', ({ data }) => {
  return data.draft !== true; // If draft field existed
})).sort((a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf());
```

### Complete RelatedArticles Component
```astro
---
// src/components/RelatedArticles.astro
import { getCollection } from 'astro:content';
import type { CollectionEntry } from 'astro:content';

type Category = 'trainer' | 'eltern' | 'vereine';

interface Props {
  category: Category;
  currentArticleId: string;
  limit?: number;
}

const { category, currentArticleId, limit = 4 } = Astro.props;

// Category labels for display
const categoryLabels: Record<Category, string> = {
  trainer: 'Trainer',
  eltern: 'Eltern',
  vereine: 'Vereine'
};

// Get related articles: same category, exclude current, sort by date
const relatedArticles = (await getCollection(category))
  .filter(article => article.id !== currentArticleId)
  .sort((a, b) => b.data.pubDate.valueOf() - a.data.pubDate.valueOf())
  .slice(0, limit);
---

{relatedArticles.length > 0 && (
  <aside class="mt-12 pt-8 border-t border-gray-200">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
      Weitere Artikel fur {categoryLabels[category]}
    </h2>
    <div class="grid gap-4 sm:grid-cols-2">
      {relatedArticles.map(article => (
        <a
          href={`/${category}/${article.id}/`}
          class="block p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
        >
          <h3 class="font-semibold text-gray-900 mb-2 hover:text-green-700">
            {article.data.title}
          </h3>
          <p class="text-sm text-gray-600 line-clamp-2">
            {article.data.description}
          </p>
        </a>
      ))}
    </div>
  </aside>
)}
```

### Modified ArticleLayout Integration
```astro
---
// ArticleLayout.astro - add to Props interface
interface Props {
  title: string;
  description: string;
  category: 'trainer' | 'eltern' | 'vereine';
  articleId: string;  // NEW: needed for related articles
  pubDate: Date;
  // ... other props
}
---

<!-- After article content, before closing main -->
<RelatedArticles
  category={category}
  currentArticleId={articleId}
/>
```

### Modified [id].astro Page
```astro
---
// src/pages/trainer/[id].astro
import { getCollection, getEntry, render } from 'astro:content';
import ArticleLayout from '../../layouts/ArticleLayout.astro';

export async function getStaticPaths() {
  const articles = await getCollection('trainer');
  return articles.map((article) => ({
    params: { id: article.id },
    props: { article },
  }));
}

const { article } = Astro.props;
const author = await getEntry(article.data.author);
const { Content } = await render(article);
---

<ArticleLayout
  title={article.data.title}
  description={article.data.description}
  category="trainer"
  articleId={article.id}   {/* NEW: pass article ID */}
  pubDate={article.data.pubDate}
  updatedDate={article.data.updatedDate}
  authorId={author.id}
  authorName={author.data.name}
  image={article.data.image}
>
  <Content />
</ArticleLayout>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Manual relatedPosts array in frontmatter | Automatic category-based querying | Astro 2.0+ | Less content maintenance |
| Import.meta.glob for content | getCollection() Content Layer | Astro 5.0 | Type safety, 5x faster builds |
| Custom file parsers | Built-in loaders | Astro 5.0 | Standardized content handling |

**Deprecated/outdated:**
- `Astro.glob()` for content: Use getCollection() instead (better performance, type safety)
- Manual frontmatter parsing: Content Layer handles this automatically

## Open Questions

Things that couldn't be fully resolved:

1. **Cross-category related articles**
   - What we know: Possible to query multiple collections and merge
   - What's unclear: Whether cross-category recommendations add value for this specific site
   - Recommendation: Start with same-category. Can add cross-category later if users request it.

2. **Tags for future enhancement**
   - What we know: Schema supports optional tags array; would enable more precise matching
   - What's unclear: Whether 28 articles justify the content update effort
   - Recommendation: Defer tags. If corpus grows to 50+ articles, revisit.

3. **Random vs. Recent ordering**
   - What we know: Both are valid; recent provides freshness signal, random provides variety
   - What's unclear: Which better serves reader engagement for this site
   - Recommendation: Use recency (newest first) as it signals active content; simpler implementation.

## Sources

### Primary (HIGH confidence)
- [Astro Content Collections Guide](https://docs.astro.build/en/guides/content-collections/) - getCollection, reference, filtering
- [Astro Content Collections API Reference](https://docs.astro.build/en/reference/modules/astro-content/) - API signatures, type definitions

### Secondary (MEDIUM confidence)
- [Josh Finnie - Creating Similar Posts Component in Astro.js](https://www.joshfinnie.com/blog/creating-a-similar-posts-component-in-astrojs/) - Tag-based algorithm approach
- [Elian Codes - Building Blog Tag Index Pages](https://www.elian.codes/blog/23-02-19-building-blog-tag-index-pages-in-astro/) - Tag filtering patterns
- [Astro Blog Tutorial - Generate Tag Pages](https://docs.astro.build/en/tutorial/5-astro-api/2/) - Official tag handling tutorial

### Tertiary (LOW confidence)
- WebSearch results on related posts algorithms - General patterns, not Astro-specific

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Using only built-in Astro APIs, well-documented
- Architecture: HIGH - Pattern is common in Astro ecosystem, verified with official docs
- Pitfalls: MEDIUM - Based on general Astro experience, some specific to this codebase

**Research date:** 2026-01-22
**Valid until:** 60 days (stable Astro 5.x API)
