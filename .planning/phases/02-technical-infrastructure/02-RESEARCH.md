# Phase 2: Technical Infrastructure - Research

**Researched:** 2026-01-22
**Domain:** Core Web Vitals, Mobile-Friendly Optimization, Performance Testing
**Confidence:** HIGH

## Summary

This phase focuses on achieving mobile-friendliness and establishing Core Web Vitals baseline for the bambini-fussball Astro site. The existing project already has a solid foundation with Astro 5.16.11, Tailwind CSS 4.1.18, and proper viewport configuration.

Core Web Vitals (LCP < 2.5s, INP < 200ms, CLS < 0.1) are the key metrics. Astro provides excellent built-in tools for achieving these targets: the Image component prevents CLS and optimizes LCP, the experimental Fonts API reduces font-swap layout shifts, and the zero-JS-by-default architecture ensures excellent INP scores.

**Primary recommendation:** Enable Astro's responsive images with `image.layout: 'constrained'`, use the experimental Fonts API for optimized font loading with fallbacks, and ensure all interactive elements meet 44x44px touch target minimums.

## Standard Stack

The established libraries/tools for this domain:

### Core (Already Installed)
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| astro | 5.16.11 | Framework | Zero JS by default, built-in image optimization |
| tailwindcss | 4.1.18 | CSS | Mobile-first responsive design utilities |
| @tailwindcss/vite | 4.1.18 | Build integration | Official Vite plugin for Tailwind CSS 4 |

### Supporting (Built-in to Astro)
| Component | Source | Purpose | When to Use |
|-----------|--------|---------|-------------|
| `<Image />` | astro:assets | Image optimization, CLS prevention | All local images |
| `<Picture />` | astro:assets | Multi-format responsive images | Hero images, above-fold images |
| `<Font />` | astro:assets (experimental) | Font optimization with fallbacks | Custom/Google fonts |

### Testing Tools (No Installation Required)
| Tool | Purpose | When to Use |
|------|---------|-------------|
| Google PageSpeed Insights | Field + Lab data, Core Web Vitals | Primary validation tool |
| Chrome DevTools Lighthouse | Lab testing, detailed metrics | Development testing |
| Chrome DevTools Performance | INP debugging, long tasks | Troubleshooting |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Built-in Image | @unpic/astro | More CDN options, but adds dependency |
| Experimental Fonts API | astro-font package | More mature, but external package |
| Manual font fallbacks | Fontaine/Capsize | More control, but more complexity |

**Installation:**
No additional packages required. Astro's built-in components handle all optimization needs.

## Architecture Patterns

### Recommended Project Structure
```
src/
├── layouts/
│   └── BaseLayout.astro      # Viewport, fonts, global styles
├── components/
│   └── Image.astro           # Wrapper for consistent image handling (optional)
├── pages/
│   └── index.astro           # Uses layouts, optimized images
├── styles/
│   └── global.css            # Tailwind import, font declarations
└── assets/                   # Local images (NOT public/)
    └── images/
```

### Pattern 1: Responsive Images Configuration
**What:** Global responsive image configuration in astro.config.mjs
**When to use:** All projects needing responsive images
**Example:**
```typescript
// astro.config.mjs
// Source: https://docs.astro.build/en/guides/images/
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  output: 'static',
  trailingSlash: 'always',
  image: {
    // Enable responsive images globally
    layout: 'constrained',
    // Apply responsive styles automatically
    responsiveStyles: true,
  },
  vite: {
    plugins: [tailwindcss()],
  },
});
```

### Pattern 2: Optimized Image Usage
**What:** Using Image component with proper attributes for LCP and CLS
**When to use:** All images in the project
**Example:**
```astro
---
// Source: https://docs.astro.build/en/guides/images/
import { Image } from 'astro:assets';
import heroImage from '../assets/images/hero.jpg';
---

<!-- Hero image - critical for LCP -->
<Image
  src={heroImage}
  alt="Descriptive alt text"
  width={800}
  height={600}
  loading="eager"
  fetchpriority="high"
/>

<!-- Below-fold image - lazy loaded -->
<Image
  src={otherImage}
  alt="Descriptive alt text"
  width={400}
  height={300}
  loading="lazy"
/>
```

### Pattern 3: Experimental Fonts API
**What:** Built-in font optimization with automatic fallbacks
**When to use:** When using custom or Google fonts
**Example:**
```typescript
// astro.config.mjs
// Source: https://docs.astro.build/en/reference/experimental-flags/fonts/
import { defineConfig, fontProviders } from 'astro/config';

export default defineConfig({
  experimental: {
    fonts: [{
      provider: fontProviders.google(),
      name: 'Inter',
      cssVariable: '--font-inter',
      weights: [400, 600, 700],
      styles: ['normal'],
      subsets: ['latin'],
      fallbacks: ['sans-serif'],
      optimizedFallbacks: true, // Generates metrics-matched fallback
    }]
  }
});
```

```astro
---
// BaseLayout.astro
import { Font } from 'astro:assets';
---
<head>
  <Font cssVariable="--font-inter" preload />
</head>
```

### Pattern 4: Touch-Friendly Interactive Elements
**What:** Minimum 44x44px touch targets for mobile accessibility
**When to use:** All clickable/tappable elements
**Example:**
```astro
<!-- Accessible link with proper touch target -->
<a
  href="/trainer/"
  class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2 text-green-600 hover:text-green-800 font-medium"
>
  Mehr erfahren
</a>

<!-- Button with proper touch target -->
<button
  class="min-h-[44px] min-w-[44px] px-6 py-3 bg-green-600 text-white rounded-lg"
>
  Action
</button>
```

### Anti-Patterns to Avoid
- **Images in public/ folder:** Never optimized, no responsive support - use src/assets/ instead
- **Missing width/height on images:** Causes CLS - always specify dimensions
- **Disabling user zoom:** Hurts accessibility - never use `user-scalable=no`
- **Small touch targets:** Under 44x44px causes tap errors - always ensure minimum size
- **Loading hero images lazily:** Hurts LCP - use `loading="eager"` and `fetchpriority="high"`

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Image optimization | Manual srcset/sizes | Astro `<Image />` component | Auto-generates srcset, prevents CLS, optimizes formats |
| Responsive images | CSS-only solutions | `image.layout: 'constrained'` | Handles all breakpoints, generates proper sizes attribute |
| Font fallback metrics | Manual CSS overrides | Experimental Fonts API with `optimizedFallbacks` | Auto-calculates ascent-override, descent-override, size-adjust |
| Image format conversion | Build scripts | Astro Image with Picture | Auto WebP/AVIF conversion at build time |
| Lazy loading | Intersection Observer | Native `loading="lazy"` | Browser-native, no JS required |

**Key insight:** Astro's zero-JS architecture and built-in image/font optimization handle most Core Web Vitals concerns automatically. Manual solutions often introduce bugs or miss edge cases.

## Common Pitfalls

### Pitfall 1: Images in public/ Folder
**What goes wrong:** Images are served unoptimized, no responsive variants generated
**Why it happens:** Misunderstanding of Astro's asset handling
**How to avoid:** Always place images in `src/assets/` and import them
**Warning signs:** Large image file sizes, no WebP variants, missing srcset

### Pitfall 2: Missing Explicit Dimensions
**What goes wrong:** Browser can't reserve space, causes CLS
**Why it happens:** Dimensions feel redundant with responsive design
**How to avoid:** Always provide width/height on Image component; Astro infers them from imports
**Warning signs:** CLS > 0.1, visible content jumping during load

### Pitfall 3: Font Swap Layout Shift
**What goes wrong:** Text reflows when web font loads, contributing to CLS
**Why it happens:** Fallback font has different metrics than web font
**How to avoid:** Use experimental Fonts API with `optimizedFallbacks: true`
**Warning signs:** Visible text jump, FOUT (Flash of Unstyled Text)

### Pitfall 4: Hero Image Loading Too Late
**What goes wrong:** LCP score degrades, hero appears after other content
**Why it happens:** Default lazy loading applied to above-fold images
**How to avoid:** Add `loading="eager"` and `fetchpriority="high"` to hero images
**Warning signs:** LCP > 2.5s, Lighthouse warning about LCP element

### Pitfall 5: Small Touch Targets
**What goes wrong:** Users tap wrong elements, frustrating mobile experience
**Why it happens:** Desktop-first thinking, visual aesthetics over usability
**How to avoid:** Enforce min-h-[44px] min-w-[44px] on all interactive elements
**Warning signs:** High bounce rate on mobile, PageSpeed accessibility warnings

### Pitfall 6: Blocking JavaScript
**What goes wrong:** Poor INP scores, unresponsive interactions
**Why it happens:** Adding client-side JavaScript without considering hydration
**How to avoid:** Use Astro Islands architecture, minimize client JS, use `client:idle` or `client:visible`
**Warning signs:** INP > 200ms, TBT (Total Blocking Time) > 200ms in Lighthouse

## Code Examples

Verified patterns from official sources:

### Complete BaseLayout with Optimizations
```astro
---
// src/layouts/BaseLayout.astro
// Combines viewport, fonts, and performance best practices
import '../styles/global.css';

interface Props {
  title: string;
  description?: string;
}

const {
  title,
  description = 'Die beste Informationsquelle fur Bambini-Fussball in Deutschland'
} = Astro.props;
---

<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <!-- Critical viewport meta - enables mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content={description} />
    <meta name="generator" content={Astro.generator} />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <title>{title}</title>
  </head>
  <body class="bg-white text-gray-900 font-sans min-h-screen">
    <slot />
  </body>
</html>
```

### Responsive Image with LCP Optimization
```astro
---
// Source: https://docs.astro.build/en/guides/images/
import { Image } from 'astro:assets';
import heroImage from '../assets/hero.jpg';
---

<!-- Above-fold hero - optimized for LCP -->
<Image
  src={heroImage}
  alt="Kinder spielen Bambini-Fussball"
  width={1200}
  height={600}
  loading="eager"
  fetchpriority="high"
  class="w-full h-auto"
/>
```

### Multi-Format Picture for Hero
```astro
---
// Source: https://docs.astro.build/en/guides/images/
import { Picture } from 'astro:assets';
import heroImage from '../assets/hero.png';
---

<!-- Serves AVIF, WebP, or PNG based on browser support -->
<Picture
  src={heroImage}
  formats={['avif', 'webp']}
  alt="Hero image description"
  width={1200}
  height={600}
  loading="eager"
  fetchpriority="high"
/>
```

### Touch-Friendly Card Component
```astro
---
// Example of mobile-friendly interactive card
interface Props {
  title: string;
  description: string;
  href: string;
  color: 'green' | 'blue' | 'amber';
}

const { title, description, href, color } = Astro.props;

const colorClasses = {
  green: 'bg-green-50 border-green-200 text-green-800',
  blue: 'bg-blue-50 border-blue-200 text-blue-800',
  amber: 'bg-amber-50 border-amber-200 text-amber-800',
};

const linkClasses = {
  green: 'text-green-600 hover:text-green-800',
  blue: 'text-blue-600 hover:text-blue-800',
  amber: 'text-amber-600 hover:text-amber-800',
};
---

<article class={`p-6 rounded-lg border ${colorClasses[color]}`}>
  <h2 class="text-2xl font-semibold mb-3">{title}</h2>
  <p class="text-gray-700 mb-4">{description}</p>
  <!-- Touch-friendly link with 44px minimum height -->
  <a
    href={href}
    class={`inline-flex items-center min-h-[44px] font-medium ${linkClasses[color]}`}
  >
    Mehr erfahren &rarr;
  </a>
</article>
```

### Astro Config with Image Optimization
```typescript
// astro.config.mjs - Full configuration for Core Web Vitals
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  output: 'static',
  trailingSlash: 'always',

  // Image optimization configuration
  image: {
    // Enable responsive images globally
    layout: 'constrained',
    // Apply responsive styles automatically
    responsiveStyles: true,
  },

  vite: {
    plugins: [tailwindcss()],
  },
});
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Google Mobile-Friendly Test | PageSpeed Insights Mobile | Dec 2023 | Mobile-Friendly Test retired; use PSI mobile score |
| FID (First Input Delay) | INP (Interaction to Next Paint) | Mar 2024 | INP measures ALL interactions, not just first |
| Manual srcset/sizes | Astro `image.layout` | Astro 5.10.0 | Auto-generated responsive attributes |
| External font packages | Experimental Fonts API | Astro 5.x | Built-in font optimization with fallbacks |
| @astrojs/tailwind | @tailwindcss/vite | Tailwind 4.0 | Direct Vite integration, no wrapper needed |

**Deprecated/outdated:**
- **Google Mobile-Friendly Test tool**: Retired December 2023; use PageSpeed Insights mobile testing
- **FID metric**: Replaced by INP in March 2024; INP is more comprehensive
- **@astrojs/tailwind integration**: Deprecated for Tailwind CSS 4; use @tailwindcss/vite directly

## Testing Strategy

### Primary Testing: PageSpeed Insights
**URL:** https://pagespeed.web.dev/
**What it tests:**
- Core Web Vitals (LCP, INP, CLS) from real user data (Chrome UX Report)
- Lab data via Lighthouse simulation
- Mobile and desktop separately

**Thresholds (Mobile):**
| Metric | Good | Needs Improvement | Poor |
|--------|------|-------------------|------|
| LCP | < 2.5s | 2.5s - 4.0s | > 4.0s |
| INP | < 200ms | 200ms - 500ms | > 500ms |
| CLS | < 0.1 | 0.1 - 0.25 | > 0.25 |

### Secondary Testing: Chrome DevTools Lighthouse
**When to use:** Local development, detailed analysis
**How to run:** DevTools > Lighthouse > Mobile > Performance

**Key metrics to watch:**
- Largest Contentful Paint (LCP)
- Total Blocking Time (TBT) - proxy for INP in lab
- Cumulative Layout Shift (CLS)
- Speed Index

### Debugging Tools
| Tool | Purpose | Access |
|------|---------|--------|
| Performance tab | Analyze INP, find long tasks | DevTools > Performance |
| Network tab | Check image sizes, formats | DevTools > Network |
| Coverage tab | Find unused CSS/JS | DevTools > More tools > Coverage |

## Open Questions

Things that couldn't be fully resolved:

1. **Experimental Fonts API Stability**
   - What we know: Astro's Fonts API is experimental but functional
   - What's unclear: When it will become stable, potential API changes
   - Recommendation: Use it for optimization benefits; fallback to astro-font package if issues arise

2. **Field Data Availability**
   - What we know: PageSpeed Insights shows field data from Chrome UX Report
   - What's unclear: New sites may not have sufficient traffic for field data
   - Recommendation: Rely on lab data initially; monitor field data as traffic grows

3. **INP Testing in Lab**
   - What we know: Lighthouse can't directly measure INP (requires real user interaction)
   - What's unclear: Exact correlation between TBT and real-world INP
   - Recommendation: Use TBT as proxy in lab; monitor real INP via Search Console

## Sources

### Primary (HIGH confidence)
- [Astro Images Documentation](https://docs.astro.build/en/guides/images/) - Image component, responsive images, layout property
- [Astro Experimental Fonts API](https://docs.astro.build/en/reference/experimental-flags/fonts/) - Font optimization, Google fonts provider
- [web.dev Core Web Vitals](https://web.dev/articles/vitals) - Metric definitions and thresholds
- [web.dev INP Optimization](https://web.dev/explore/how-to-optimize-inp) - INP best practices

### Secondary (MEDIUM confidence)
- [Tailwind CSS Responsive Design](https://tailwindcss.com/docs/responsive-design) - Mobile-first breakpoints, touch handling
- [WCAG 2.5.5 Target Size](https://www.w3.org/WAI/WCAG21/Understanding/target-size.html) - 44x44px touch target requirement
- [Cloudflare Pages Serving](https://developers.cloudflare.com/pages/configuration/serving-pages/) - Built-in caching, optimization

### Tertiary (LOW confidence - marked for validation)
- Blog posts on Astro performance optimization - patterns may be outdated
- Community forum discussions on INP - anecdotal evidence

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Using official Astro/Tailwind features, well-documented
- Architecture: HIGH - Patterns from official documentation
- Pitfalls: HIGH - Common issues documented in web.dev and Astro docs
- Testing strategy: HIGH - Official Google tools and thresholds

**Research date:** 2026-01-22
**Valid until:** 2026-02-22 (30 days - stack is stable)
