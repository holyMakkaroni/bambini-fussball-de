# Phase 5: SEO Infrastructure - Research

**Researched:** 2026-01-22
**Domain:** SEO Meta Tags, JSON-LD Structured Data, E-E-A-T
**Confidence:** HIGH

## Summary

This research covers implementing comprehensive SEO infrastructure for an Astro 5.x German-language site about Bambini-Fussball. The phase requires unique meta tags on every page, Article schema (JSON-LD) on article pages, Author schema linked to articles, and dedicated Author/About pages for E-E-A-T signals.

The existing codebase already has BaseLayout.astro with basic title/description props and ArticleLayout.astro with breadcrumb JSON-LD. The content collection schema already includes title, description, pubDate, and author (string). Key enhancements needed:
1. Extend BaseLayout head with Open Graph and canonical URL
2. Add Article JSON-LD to ArticleLayout
3. Create author collection with reference() for type-safe author linking
4. Create author and about pages with ProfilePage schema

**Primary recommendation:** Use native Astro patterns (set:html + JSON.stringify) for JSON-LD rather than external packages; create an author collection with reference() for consistent entity identification across Article and ProfilePage schemas.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Astro (native) | 5.x | Meta tags, JSON-LD via set:html | Built-in, no dependencies |
| astro:content | 5.x | reference() for author linking | Type-safe collection relationships |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| astro-seo | 0.8.x | SEO component wrapper | Optional - simplifies og/twitter tags |
| astro-seo-schema | 3.x | TypeScript-typed JSON-LD | Optional - if you want schema-dts types |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Native set:html | astro-seo-schema | Adds dependency but provides TypeScript types for schema.org |
| Native meta tags | astro-seo package | More verbose but more explicit control |

**Decision:** Use native Astro patterns. The existing BaseLayout already has basic meta tags. Extending it is simpler than adding dependencies for a site of this scope.

**No additional installation required** - all functionality available in existing Astro 5.x

## Architecture Patterns

### Recommended Project Structure
```
src/
├── content/
│   ├── trainer/           # Existing article collection
│   ├── eltern/            # Existing article collection
│   ├── vereine/           # Existing article collection
│   └── authors/           # NEW: Author profiles (JSON)
├── layouts/
│   ├── BaseLayout.astro   # Enhanced with OG tags, canonical
│   └── ArticleLayout.astro # Enhanced with Article JSON-LD
├── pages/
│   ├── autor/
│   │   └── [id].astro     # NEW: Author profile pages
│   └── ueber-uns.astro    # NEW: About page
└── content.config.ts      # Enhanced with author collection + reference
```

### Pattern 1: JSON-LD in Astro with set:html
**What:** Native pattern for embedding structured data
**When to use:** Any JSON-LD schema (Article, ProfilePage, BreadcrumbList)
**Example:**
```astro
---
// Source: https://docs.astro.build/en/reference/directives-reference/
const articleSchema = {
  "@context": "https://schema.org",
  "@type": "Article",
  headline: title,
  description: description,
  datePublished: pubDate.toISOString(),
  dateModified: updatedDate?.toISOString() || pubDate.toISOString(),
  author: {
    "@type": "Person",
    "@id": `https://bambini-fussball.pages.dev/autor/${authorId}/`,
    name: authorName,
    url: `https://bambini-fussball.pages.dev/autor/${authorId}/`
  },
  image: socialImage
};
---

<script type="application/ld+json" set:html={JSON.stringify(articleSchema)} />
```

### Pattern 2: Author Collection with reference()
**What:** Type-safe linking between articles and authors
**When to use:** When multiple articles reference the same author entity
**Example:**
```typescript
// Source: https://docs.astro.build/en/guides/content-collections/
// src/content.config.ts
import { defineCollection, reference, z } from 'astro:content';
import { glob } from 'astro/loaders';

const authors = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/authors' }),
  schema: z.object({
    name: z.string(),
    bio: z.string(),
    credentials: z.string(),
    image: z.string(),
    sameAs: z.array(z.string().url()).optional()
  })
});

const articleSchema = z.object({
  title: z.string(),
  description: z.string(),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: reference('authors'),  // Changed from z.string()
  image: z.string().optional()
});
```

### Pattern 3: Consistent @id for Entity Linking
**What:** Using matching @id values between Article author and ProfilePage
**When to use:** Linking Article schema to Author ProfilePage schema
**Example:**
```javascript
// In Article JSON-LD:
"author": {
  "@type": "Person",
  "@id": "https://bambini-fussball.pages.dev/autor/redaktion/",
  "name": "Redaktion",
  "url": "https://bambini-fussball.pages.dev/autor/redaktion/"
}

// In Author page ProfilePage JSON-LD:
{
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "mainEntity": {
    "@type": "Person",
    "@id": "https://bambini-fussball.pages.dev/autor/redaktion/",
    "name": "Redaktion",
    "description": "Die Redaktion von Bambini-Fussball.de",
    "image": "/images/redaktion.jpg",
    "sameAs": ["https://linkedin.com/..."]
  }
}
```

### Anti-Patterns to Avoid
- **String author names without linking:** Don't use plain author strings without reference() - loses entity consistency
- **Mismatched @id values:** Article author @id must match ProfilePage mainEntity @id exactly
- **Embedding JSON without set:html:** Astro escapes content by default - always use set:html for JSON-LD
- **Duplicate main elements:** ArticleLayout has `<main>` inside BaseLayout - ensure only one main per page

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Character counting for meta | Manual validation | Zod refine with maxLength | Build-time validation catches issues |
| URL construction | String concatenation | Astro.url, new URL() | Proper URL handling, XSS prevention |
| Date formatting | Custom formatters | toISOString() | ISO 8601 required for JSON-LD |
| JSON escaping | Template literals | JSON.stringify + set:html | Prevents XSS, proper escaping |
| Schema validation | Manual review | Rich Results Test | Official Google tool |

**Key insight:** JSON-LD looks simple but requires proper escaping (JSON.stringify), consistent identifiers (@id), and validation (Rich Results Test). The set:html directive exists specifically for this use case.

## Common Pitfalls

### Pitfall 1: JSON-LD Escaping Issues
**What goes wrong:** Content with quotes or special characters breaks JSON-LD
**Why it happens:** Using template literals instead of JSON.stringify
**How to avoid:** Always use `JSON.stringify(schemaObject)` with `set:html`
**Warning signs:** Schema validation fails, browser console JSON errors

### Pitfall 2: Inconsistent Author Entity @id
**What goes wrong:** Google can't link articles to author profile
**Why it happens:** Different @id values between Article and ProfilePage
**How to avoid:** Use canonical author URL as @id in both schemas
**Warning signs:** Rich Results Test shows "author" but no profile link

### Pitfall 3: Missing Required Article Schema Properties
**What goes wrong:** Article not eligible for rich results
**Why it happens:** Omitting image (most common), headline too long
**How to avoid:** Include headline, image, author.name, author.url, datePublished
**Warning signs:** Rich Results Test shows "not eligible"

### Pitfall 4: Meta Description Too Long
**What goes wrong:** Google truncates description in SERPs
**Why it happens:** Exceeding ~155-160 character limit
**How to avoid:** Zod schema with z.string().max(160) validation
**Warning signs:** Preview shows "..." truncation

### Pitfall 5: Duplicate Content in Head
**What goes wrong:** Multiple title/description tags
**Why it happens:** BaseLayout has meta, ArticleLayout adds more
**How to avoid:** Props flow from page to layout; layouts don't add their own meta
**Warning signs:** Multiple `<title>` tags in page source

### Pitfall 6: Missing Canonical URL
**What goes wrong:** Duplicate content issues if accessed via different URLs
**Why it happens:** Not setting canonical link element
**How to avoid:** Add `<link rel="canonical" href={Astro.url.href} />` to head
**Warning signs:** Same content indexed under multiple URLs

## Code Examples

Verified patterns from official sources:

### Enhanced BaseLayout Head Section
```astro
---
// Source: Astro docs + Google SEO best practices
interface Props {
  title: string;
  description?: string;
  image?: string;
  type?: 'website' | 'article';
}

const {
  title,
  description = 'Die beste Informationsquelle fur Bambini-Fussball in Deutschland',
  image = '/images/og-default.jpg',
  type = 'website'
} = Astro.props;

const canonicalURL = new URL(Astro.url.pathname, Astro.site);
const socialImageURL = new URL(image, Astro.site);
---

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content={description} />
  <meta name="generator" content={Astro.generator} />
  <link rel="canonical" href={canonicalURL} />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <title>{title}</title>

  <!-- Open Graph -->
  <meta property="og:type" content={type} />
  <meta property="og:url" content={canonicalURL} />
  <meta property="og:title" content={title} />
  <meta property="og:description" content={description} />
  <meta property="og:image" content={socialImageURL} />
  <meta property="og:locale" content="de_DE" />
  <meta property="og:site_name" content="Bambini-Fussball" />

  <!-- Twitter Card (fallback to OG) -->
  <meta name="twitter:card" content="summary_large_image" />

  <slot name="head" />
</head>
```

### Article JSON-LD Schema
```astro
---
// Source: https://developers.google.com/search/docs/appearance/structured-data/article
interface Props {
  title: string;
  description: string;
  pubDate: Date;
  updatedDate?: Date;
  authorId: string;
  authorName: string;
  image?: string;
}

const { title, description, pubDate, updatedDate, authorId, authorName, image } = Astro.props;
const siteUrl = 'https://bambini-fussball.pages.dev';

const articleSchema = {
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": title,
  "description": description,
  "image": image ? `${siteUrl}${image}` : `${siteUrl}/images/og-default.jpg`,
  "datePublished": pubDate.toISOString(),
  "dateModified": (updatedDate || pubDate).toISOString(),
  "author": {
    "@type": "Person",
    "@id": `${siteUrl}/autor/${authorId}/`,
    "name": authorName,
    "url": `${siteUrl}/autor/${authorId}/`
  },
  "publisher": {
    "@type": "Organization",
    "name": "Bambini-Fussball",
    "url": siteUrl
  }
};
---

<script type="application/ld+json" set:html={JSON.stringify(articleSchema)} />
```

### ProfilePage JSON-LD for Author Page
```astro
---
// Source: https://developers.google.com/search/docs/appearance/structured-data/profile-page
const author = Astro.props.author; // From getEntry
const siteUrl = 'https://bambini-fussball.pages.dev';

const profileSchema = {
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "dateCreated": "2026-01-01T00:00:00+01:00",
  "dateModified": new Date().toISOString(),
  "mainEntity": {
    "@type": "Person",
    "@id": `${siteUrl}/autor/${author.id}/`,
    "name": author.data.name,
    "description": author.data.bio,
    "image": `${siteUrl}${author.data.image}`,
    "sameAs": author.data.sameAs || []
  }
};
---

<script type="application/ld+json" set:html={JSON.stringify(profileSchema)} />
```

### Author Collection JSON Format
```json
// src/content/authors/redaktion.json
{
  "name": "Redaktion",
  "bio": "Die Redaktion von Bambini-Fussball.de besteht aus erfahrenen Trainern und Padagogen mit langjahrigem Hintergrund im Kinderfussball.",
  "credentials": "DFB-Lizenztrainer, Sportpadagogen",
  "image": "/images/authors/redaktion.jpg",
  "sameAs": []
}
```

### Content Schema with Author Reference
```typescript
// src/content.config.ts update
import { defineCollection, reference, z } from 'astro:content';
import { glob } from 'astro/loaders';

const authors = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/authors' }),
  schema: z.object({
    name: z.string(),
    bio: z.string().max(300),
    credentials: z.string(),
    image: z.string(),
    sameAs: z.array(z.string().url()).optional().default([])
  })
});

const articleSchema = z.object({
  title: z.string().max(60),
  description: z.string().max(160),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: reference('authors'),
  image: z.string().optional()
});

// Update existing collections with new schema
const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});

// ... eltern, vereine similarly

export const collections = { authors, trainer, eltern, vereine };
```

### Resolving Author Reference in Article Page
```astro
---
// src/pages/trainer/[id].astro update
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
const author = await getEntry(article.data.author); // Resolve reference
const { Content } = await render(article);
---

<ArticleLayout
  title={article.data.title}
  description={article.data.description}
  category="trainer"
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
| Microdata/RDFa | JSON-LD | 2020+ | Google officially recommends JSON-LD |
| SDTT | Rich Results Test + Schema Validator | 2024 | Google migrated tool |
| Author string | Author reference with @id | E-E-A-T era | Entity linking for authority |
| Title focus | E-E-A-T signals | 2022+ | Author credentials matter more |

**Deprecated/outdated:**
- Google Structured Data Testing Tool (SDTT): Replaced by Schema Markup Validator
- Microdata for new implementations: JSON-LD preferred
- Title/description-only SEO: E-E-A-T requires author credibility signals

## Requirements Mapping

| Requirement | Implementation | Schema/Pattern |
|-------------|----------------|----------------|
| SEO-01: Meta title < 60 chars | z.string().max(60) in schema | Build-time validation |
| SEO-02: Meta description < 160 chars | z.string().max(160) in schema | Build-time validation |
| SEO-03: Article JSON-LD | ArticleLayout with set:html | Article schema |
| SEO-04: Author JSON-LD linked | @id matching between schemas | Person schema with @id |
| SEO-05: Author page | /autor/[id].astro with ProfilePage | ProfilePage + Person |
| SEO-06: About page | /ueber-uns.astro | AboutPage or Organization |

## Open Questions

Things that couldn't be fully resolved:

1. **Image for Open Graph**
   - What we know: OG requires 1200x630px images
   - What's unclear: Does the site have default social images yet?
   - Recommendation: Create placeholder, add to Phase 5 tasks

2. **Multiple Authors**
   - What we know: JSON-LD supports author arrays
   - What's unclear: Will articles have multiple authors?
   - Recommendation: Use single author reference for now (matches current schema)

3. **About Page Schema Type**
   - What we know: AboutPage or Organization both valid
   - What's unclear: Which better signals E-E-A-T for this site
   - Recommendation: Use AboutPage with mentions of Organization

## Validation Strategy

**Testing workflow for JSON-LD:**

1. **Local development:**
   - View page source, locate `<script type="application/ld+json">`
   - Copy JSON, paste into https://validator.schema.org/

2. **Pre-deployment:**
   - Build site: `npm run build`
   - Preview: `npm run preview`
   - Use Rich Results Test with localhost URL (or ngrok)

3. **Post-deployment:**
   - Google Rich Results Test: https://search.google.com/test/rich-results
   - Schema Markup Validator: https://validator.schema.org/
   - Enter live URL, verify no errors

**Tools:**
- Rich Results Test: Shows Google-specific eligibility
- Schema.org Validator: General schema validation
- Browser DevTools: Check for JSON syntax errors in console

## Sources

### Primary (HIGH confidence)
- [Google Article Schema Docs](https://developers.google.com/search/docs/appearance/structured-data/article) - Official Article schema properties
- [Google ProfilePage Schema Docs](https://developers.google.com/search/docs/appearance/structured-data/profile-page) - Author profile schema
- [Astro Content Collections](https://docs.astro.build/en/guides/content-collections/) - reference() function
- [Astro Directives](https://docs.astro.build/en/reference/directives-reference/) - set:html directive

### Secondary (MEDIUM confidence)
- [Schema.org author property](https://schema.org/author) - Person/Organization values
- [Rich Results Test](https://developers.google.com/search/docs/appearance/structured-data) - Validation tool
- [Astro SEO Guide](https://eastondev.com/blog/en/posts/dev/20251202-astro-seo-complete-guide/) - Implementation patterns

### Tertiary (LOW confidence)
- [E-E-A-T 2025 Updates](https://www.wearetg.com/blog/google-eeat/) - Recent algorithm emphasis
- [Meta length best practices](https://destination-digital.co.uk/news-blogs-case-studies/title-meta-description-length-google-serps-2025/) - Character limits

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Native Astro patterns well documented
- Architecture: HIGH - reference() pattern from official docs
- JSON-LD structure: HIGH - Google official documentation
- Pitfalls: MEDIUM - Based on community patterns and official guidance
- E-E-A-T signals: MEDIUM - Google guidelines but interpretation varies

**Research date:** 2026-01-22
**Valid until:** 2026-02-22 (30 days - stable domain, Google updates quarterly)
