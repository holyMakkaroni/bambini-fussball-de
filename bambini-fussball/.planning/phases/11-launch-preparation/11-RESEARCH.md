# Phase 11: Launch Preparation - Research

**Researched:** 2026-01-22
**Domain:** Technical SEO, XML Sitemap, robots.txt, Pre-Launch Verification
**Confidence:** HIGH

## Summary

This research covers implementing technical SEO elements for the bambini-fussball Astro site to meet TECH-03 (automatic XML sitemap generation) and TECH-04 (robots.txt configuration). The project is a static Astro 5.x site deployed to Cloudflare Pages at https://bambini-fussball.pages.dev/.

Currently, the site has NO sitemap or robots.txt - verified by checking both the live site and the dist/ build output. The site has approximately 30+ pages across trainer/, eltern/, vereine/, autor/, and legal pages (impressum, datenschutz, ueber-uns).

The standard approach uses two official/well-maintained Astro integrations: `@astrojs/sitemap` (official, v3.6.1) for automatic sitemap generation and `astro-robots-txt` (v1.0.0) for robots.txt generation. Both integrate seamlessly with Astro's build process and require minimal configuration. The critical prerequisite is setting the `site` property in astro.config.mjs, which the project already has the pattern for (canonical URLs use 'https://bambini-fussball.pages.dev').

**Primary recommendation:** Install @astrojs/sitemap and astro-robots-txt integrations, add `site` config, configure to exclude legal pages from sitemap priority, verify sitemap accessibility at /sitemap-index.xml, and add sitemap link to HTML head.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| @astrojs/sitemap | ^3.6.1 | Auto-generate XML sitemap | Official Astro integration, 314k weekly downloads |
| astro-robots-txt | ^1.0.0 | Generate robots.txt | Well-maintained, 5k+ users, auto-references sitemap |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| astro-ai-robots-txt | ^0.1.2 | Block AI crawlers | Optional, if blocking GPTBot/Claude/etc. desired |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| @astrojs/sitemap | Manual sitemap.xml | Integration auto-updates; manual requires maintenance |
| astro-robots-txt | public/robots.txt | Integration auto-references sitemap URL; static requires manual sync |
| astro-robots-txt | astro-robots (v2.3.1) | Newer but less stable; astro-robots-txt is proven |

**Installation:**
```bash
npm install @astrojs/sitemap astro-robots-txt
```

Or via Astro CLI:
```bash
npx astro add sitemap
npm install astro-robots-txt
```

## Architecture Patterns

### Recommended Configuration Structure
```javascript
// astro.config.mjs
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import robotsTxt from 'astro-robots-txt';

export default defineConfig({
  site: 'https://bambini-fussball.pages.dev',  // REQUIRED for sitemap
  output: 'static',
  trailingSlash: 'always',
  integrations: [
    sitemap({
      // Optional: customize sitemap
    }),
    robotsTxt({
      // Optional: customize robots.txt
    }),
    // ... other integrations
  ],
});
```

### Pattern 1: Basic Sitemap Configuration
**What:** Minimal setup for automatic sitemap generation
**When to use:** Most static sites with standard page structure
**Example:**
```javascript
// astro.config.mjs
import sitemap from '@astrojs/sitemap';

export default defineConfig({
  site: 'https://bambini-fussball.pages.dev',
  integrations: [
    sitemap(),  // Generates sitemap-index.xml and sitemap-0.xml
  ],
});
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### Pattern 2: Sitemap with Page Filtering
**What:** Exclude specific pages from sitemap (legal pages, author pages)
**When to use:** When some pages shouldn't be prioritized for crawling
**Example:**
```javascript
sitemap({
  filter: (page) =>
    !page.includes('/impressum/') &&
    !page.includes('/datenschutz/'),
})
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### Pattern 3: Sitemap with Priority Configuration
**What:** Set changefreq and priority for SEO hints
**When to use:** When different page types have different update frequencies
**Example:**
```javascript
sitemap({
  serialize(item) {
    // Home page - highest priority
    if (item.url === 'https://bambini-fussball.pages.dev/') {
      item.priority = 1.0;
      item.changefreq = 'weekly';
    }
    // Category index pages
    else if (item.url.match(/\/(trainer|eltern|vereine)\/$/)) {
      item.priority = 0.8;
      item.changefreq = 'weekly';
    }
    // Articles
    else if (item.url.match(/\/(trainer|eltern|vereine)\/.+/)) {
      item.priority = 0.6;
      item.changefreq = 'monthly';
    }
    // Other pages
    else {
      item.priority = 0.3;
    }
    return item;
  },
})
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### Pattern 4: robots.txt with Sitemap Reference
**What:** Auto-generate robots.txt that references the sitemap
**When to use:** Always - ensures crawlers find the sitemap
**Example:**
```javascript
// astro.config.mjs
import robotsTxt from 'astro-robots-txt';

export default defineConfig({
  site: 'https://bambini-fussball.pages.dev',
  integrations: [
    robotsTxt({
      // sitemap: true is default - outputs:
      // Sitemap: https://bambini-fussball.pages.dev/sitemap-index.xml
    }),
  ],
});
```
Source: https://github.com/alextim/astro-lib/tree/main/packages/astro-robots-txt

### Pattern 5: Add Sitemap Discovery Link in Head
**What:** Help crawlers find sitemap via HTML head
**When to use:** Always - belt-and-suspenders approach
**Example:**
```astro
<!-- In BaseLayout.astro <head> section -->
<link rel="sitemap" href="/sitemap-index.xml" />
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### Build Output Structure
```
dist/
  sitemap-index.xml      # Index file (entry point)
  sitemap-0.xml          # Actual URLs (may have more files for large sites)
  robots.txt             # Generated by astro-robots-txt
  index.html
  trainer/
  eltern/
  vereine/
  ...
```

### Anti-Patterns to Avoid
- **Missing `site` config:** Sitemap integration REQUIRES site URL - build will fail without it
- **Static robots.txt in public/:** Requires manual sitemap URL sync; use integration instead
- **Blocking search engines in robots.txt during development:** Use noindex meta tag instead
- **Not testing sitemap accessibility:** Always verify /sitemap-index.xml returns valid XML after build

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| XML sitemap generation | Custom XML builder | @astrojs/sitemap | Handles pagination, XML escaping, index files |
| robots.txt generation | Manual file | astro-robots-txt | Auto-syncs with sitemap URL, policy management |
| Sitemap URL discovery | Manual robots.txt edit | Integration's sitemap:true | Auto-generates correct URL format |
| Page filtering | Manual exclusion list | filter() callback | Integrates with Astro's page list |

**Key insight:** Both integrations hook into Astro's build process and automatically discover all generated pages. Hand-rolling requires manually tracking pages and updating files when content changes.

## Common Pitfalls

### Pitfall 1: Missing `site` Configuration
**What goes wrong:** Build fails with "site" required error or sitemap contains localhost URLs
**Why it happens:** Sitemap needs full URLs, not relative paths
**How to avoid:** Add `site: 'https://bambini-fussball.pages.dev'` to astro.config.mjs
**Warning signs:** Build error mentioning "site" or sitemap URLs starting with localhost

### Pitfall 2: Cloudflare Pages Build Output Issue
**What goes wrong:** robots.txt or sitemap.xml not accessible in production
**Why it happens:** Build output directory mismatch
**How to avoid:** Verify files exist in dist/ after build; both integrations output there by default
**Warning signs:** 404 when accessing /robots.txt or /sitemap-index.xml on live site

### Pitfall 3: trailingSlash Mismatch
**What goes wrong:** Sitemap URLs don't match actual page URLs
**Why it happens:** Sitemap generates URLs with/without trailing slash different from site config
**How to avoid:** Ensure `trailingSlash: 'always'` matches actual URL structure (already configured)
**Warning signs:** Google Search Console shows "Submitted URL not found (404)" errors

### Pitfall 4: Forgetting sitemap-index.xml vs sitemap.xml
**What goes wrong:** Submitting wrong URL to Google Search Console
**Why it happens:** @astrojs/sitemap generates sitemap-index.xml, not sitemap.xml
**How to avoid:** Use /sitemap-index.xml as the sitemap URL
**Warning signs:** 404 when accessing /sitemap.xml

### Pitfall 5: Not Escaping Special Characters in URLs
**What goes wrong:** XML parsing errors in sitemap
**Why it happens:** URLs with &, <, >, ", ' need XML escaping
**How to avoid:** @astrojs/sitemap handles this automatically - don't hand-roll
**Warning signs:** Google Search Console reports "Invalid XML" errors

### Pitfall 6: Blocking Crawlers During Development
**What goes wrong:** Site remains unindexed after launch
**Why it happens:** robots.txt with "Disallow: /" left in place
**How to avoid:** Default astro-robots-txt config allows all crawlers; verify after build
**Warning signs:** robots.txt contains "Disallow: /" or "User-agent: * / Disallow: /"

## Code Examples

Verified patterns from official sources:

### Complete astro.config.mjs Configuration
```javascript
// astro.config.mjs
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import cookieconsent from '@jop-software/astro-cookieconsent';
import sitemap from '@astrojs/sitemap';
import robotsTxt from 'astro-robots-txt';

export default defineConfig({
  site: 'https://bambini-fussball.pages.dev',  // REQUIRED
  output: 'static',
  trailingSlash: 'always',
  image: {
    layout: 'constrained',
    responsiveStyles: true,
  },
  vite: {
    plugins: [tailwindcss()],
  },
  integrations: [
    sitemap({
      filter: (page) =>
        // Optionally exclude legal/utility pages from priority
        !page.includes('/impressum/') &&
        !page.includes('/datenschutz/'),
      serialize(item) {
        // Category pages get higher priority
        if (item.url.match(/\/(trainer|eltern|vereine)\/$/)) {
          item.priority = 0.8;
        }
        return item;
      },
    }),
    robotsTxt({
      // sitemap: true is default, auto-references sitemap-index.xml
      policy: [
        {
          userAgent: '*',
          allow: '/',
        },
      ],
    }),
    cookieconsent({
      // existing config...
    }),
  ],
});
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### BaseLayout Head with Sitemap Link
```astro
---
// src/layouts/BaseLayout.astro
// ... existing imports and props
---

<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content={description} />
    <meta name="generator" content={Astro.generator} />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="canonical" href={canonicalURL} />
    <link rel="sitemap" href="/sitemap-index.xml" />  <!-- ADD THIS -->
    <!-- ... rest of head -->
  </head>
  <!-- ... rest of layout -->
</html>
```
Source: https://docs.astro.build/en/guides/integrations-guide/sitemap/

### Expected robots.txt Output
```
User-agent: *
Allow: /

Sitemap: https://bambini-fussball.pages.dev/sitemap-index.xml
```

### Expected sitemap-index.xml Output
```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://bambini-fussball.pages.dev/sitemap-0.xml</loc>
  </sitemap>
</sitemapindex>
```

### Expected sitemap-0.xml Output (partial)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://bambini-fussball.pages.dev/</loc>
    <priority>0.5</priority>
  </url>
  <url>
    <loc>https://bambini-fussball.pages.dev/trainer/</loc>
    <priority>0.8</priority>
  </url>
  <url>
    <loc>https://bambini-fussball.pages.dev/trainer/bambini-training-einstieg/</loc>
    <priority>0.5</priority>
  </url>
  <!-- ... more URLs -->
</urlset>
```

## Pre-Launch Verification Checklist

Essential verification steps before considering launch complete:

### Sitemap Verification
1. **Build succeeds:** `npm run build` completes without errors
2. **Files exist:** `dist/sitemap-index.xml` and `dist/sitemap-0.xml` present
3. **Valid XML:** Open sitemap files in browser - should render as XML
4. **All pages included:** Count URLs in sitemap matches expected page count (~30+)
5. **URLs correct:** All URLs use production domain with trailing slashes
6. **Accessible after deploy:** https://bambini-fussball.pages.dev/sitemap-index.xml returns 200

### robots.txt Verification
1. **File exists:** `dist/robots.txt` present after build
2. **Allows crawling:** Contains "Allow: /" not "Disallow: /"
3. **References sitemap:** Contains correct sitemap-index.xml URL
4. **Accessible after deploy:** https://bambini-fussball.pages.dev/robots.txt returns 200

### HTML Head Verification
1. **Sitemap link present:** `<link rel="sitemap" href="/sitemap-index.xml" />` in head

### Google Search Console (post-launch)
1. Submit sitemap URL: https://bambini-fussball.pages.dev/sitemap-index.xml
2. Verify no errors in Sitemaps report
3. Use URL Inspection to test key pages

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Manual sitemap.xml | @astrojs/sitemap integration | Astro 2.0+ | Auto-generates, auto-updates |
| Manual robots.txt | astro-robots-txt integration | 2022+ | Auto-references sitemap URL |
| sitemap.xml filename | sitemap-index.xml | @astrojs/sitemap | Supports pagination for large sites |
| Single sitemap file | sitemap-index + numbered files | @astrojs/sitemap 3.0+ | Better for large sites |

**Current versions:**
- @astrojs/sitemap: 3.6.1 (stable, latest)
- astro-robots-txt: 1.0.0 (stable, proven)

## Open Questions

Things that couldn't be fully resolved:

1. **Custom Domain Configuration**
   - What we know: Site currently uses pages.dev domain
   - What's unclear: Will site get custom domain like bambini-fussball.de?
   - Recommendation: Update `site` config when/if custom domain is configured

2. **Excluding Author Pages**
   - What we know: /autor/[id]/ pages exist
   - What's unclear: Should these be in sitemap or excluded?
   - Recommendation: Include by default; author pages help establish E-E-A-T

3. **Legal Pages in Sitemap**
   - What we know: impressum and datenschutz are required pages
   - What's unclear: Should they have lower priority or be excluded?
   - Recommendation: Include but with low priority (0.1) - helps users find them

## Sources

### Primary (HIGH confidence)
- [Astro Sitemap Integration Docs](https://docs.astro.build/en/guides/integrations-guide/sitemap/) - Official documentation, comprehensive
- [astro-robots-txt GitHub](https://github.com/alextim/astro-lib/tree/main/packages/astro-robots-txt) - Source documentation, all config options
- [Astro Configuration Reference](https://docs.astro.build/en/guides/configuring-astro/#site) - Site config requirement

### Secondary (MEDIUM confidence)
- [Google Search Console Sitemaps Help](https://support.google.com/webmasters/answer/7451001) - Validation and submission
- [Cloudflare Pages Community](https://community.cloudflare.com/t/robots-txt-cloudflare-page/636861) - Deployment considerations
- [@astrojs/sitemap npm](https://www.npmjs.com/package/@astrojs/sitemap) - Version and download info

### Tertiary (LOW confidence)
- Community blog posts on Astro SEO (patterns verified against official docs)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Official Astro integration, well-documented
- Architecture: HIGH - Patterns from official documentation
- Pitfalls: HIGH - Common issues documented in community and official sources
- Verification checklist: HIGH - Based on Google and Astro best practices

**Research date:** 2026-01-22
**Valid until:** 2026-04-22 (3 months - both integrations are stable)
