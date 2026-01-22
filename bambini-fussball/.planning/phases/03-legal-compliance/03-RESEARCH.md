# Phase 3: Legal Compliance - Research

**Researched:** 2026-01-22
**Domain:** German website legal compliance (Impressum, DSGVO, Cookie Consent)
**Confidence:** HIGH

## Summary

German website compliance requires three core legal elements: an Impressum (legal notice under DDG SS5), a Datenschutzerklaerung (privacy policy under DSGVO/GDPR), and a Cookie Consent mechanism for non-essential cookies. Since the TMG was replaced by the DDG on 14 May 2024, all references to "SS5 TMG" must now read "SS5 DDG" or simply omit the legal reference.

For a static Astro site without tracking/analytics, the implementation is straightforward: create two static content pages (Impressum and Datenschutz), add them to a site-wide footer, and implement a lightweight cookie consent banner that stores user preferences in localStorage. Since this is a content-only site without analytics or third-party cookies, the cookie consent implementation is primarily for future-proofing and demonstrating compliance awareness.

**Primary recommendation:** Use `vanilla-cookieconsent` (v3.1.0) via the `@jop-software/astro-cookieconsent` integration for cookie consent, create static `.astro` pages for Impressum and Datenschutz, and add a Footer component to BaseLayout.astro that appears on every page.

## Standard Stack

The established tools for German legal compliance on static sites:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| vanilla-cookieconsent | 3.1.0 | GDPR cookie consent banner | Lightweight (~12KB), vanilla JS, MIT license, highly configurable, 5.3k GitHub stars |
| @jop-software/astro-cookieconsent | 3.x | Astro integration for vanilla-cookieconsent | Official Astro integration, simple setup via astro add |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| None required | - | - | Static pages for Impressum/Datenschutz need no libraries |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| vanilla-cookieconsent | cookies-eu-banner | Smaller (1KB) but fewer features, no granular category management |
| vanilla-cookieconsent | Custom solution | More control but requires maintaining GDPR compliance manually |
| Astro integration | Direct vanilla-cookieconsent | More flexibility but requires manual setup |

**Installation:**
```bash
npx astro add @jop-software/astro-cookieconsent
# or if peer dependency not installed:
npm install @jop-software/astro-cookieconsent vanilla-cookieconsent
```

## Architecture Patterns

### Recommended Project Structure
```
src/
  components/
    Footer.astro          # Site-wide footer with legal links
  layouts/
    BaseLayout.astro      # Base layout including Footer
  pages/
    impressum.astro       # Legal notice page
    datenschutz.astro     # Privacy policy page
    index.astro           # Homepage
```

### Pattern 1: Site-Wide Footer in Layout
**What:** Include Footer component in BaseLayout.astro so it appears on every page automatically
**When to use:** Always - legal links must be accessible from every page
**Example:**
```astro
---
// src/layouts/BaseLayout.astro
import '../styles/global.css';
import Footer from '../components/Footer.astro';

interface Props {
  title: string;
  description?: string;
}

const { title, description = 'Default description' } = Astro.props;
---

<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content={description} />
    <title>{title}</title>
  </head>
  <body class="bg-white text-gray-900 font-sans min-h-screen flex flex-col">
    <slot />
    <Footer />
  </body>
</html>
```

### Pattern 2: Footer Component with Legal Links
**What:** Dedicated footer component with Impressum and Datenschutz links
**When to use:** Required for legal compliance - links must be "easily identifiable, directly accessible"
**Example:**
```astro
---
// src/components/Footer.astro
---
<footer class="mt-auto py-8 border-t border-gray-200">
  <div class="container mx-auto px-4">
    <nav class="flex flex-wrap justify-center gap-6 text-sm text-gray-600">
      <a href="/impressum/" class="inline-flex items-center min-h-[44px] hover:text-gray-900">
        Impressum
      </a>
      <a href="/datenschutz/" class="inline-flex items-center min-h-[44px] hover:text-gray-900">
        Datenschutz
      </a>
    </nav>
  </div>
</footer>
```

### Pattern 3: Cookie Consent Configuration (German)
**What:** Configure vanilla-cookieconsent with German translations and GDPR-compliant options
**When to use:** Required for sites using any non-essential cookies
**Example:**
```javascript
// astro.config.mjs integration example
cookieconsent({
  guiOptions: {
    consentModal: {
      layout: 'box',
      position: 'bottom center',
      equalWeightButtons: true,  // Required: accept/reject must be equal
      flipButtons: false,
    },
    preferencesModal: {
      layout: 'box',
      equalWeightButtons: true,
      flipButtons: false,
    },
  },
  categories: {
    necessary: {
      enabled: true,
      readOnly: true,  // Cannot be disabled
    },
    analytics: {
      enabled: false,  // Off by default (opt-in required)
    },
  },
  language: {
    default: 'de',
    translations: {
      de: {
        consentModal: {
          title: 'Wir verwenden Cookies',
          description: 'Diese Website verwendet Cookies, um Ihnen das beste Erlebnis zu bieten.',
          acceptAllBtn: 'Alle akzeptieren',
          acceptNecessaryBtn: 'Alle ablehnen',
          showPreferencesBtn: 'Einstellungen',
        },
        preferencesModal: {
          title: 'Cookie-Einstellungen',
          acceptAllBtn: 'Alle akzeptieren',
          acceptNecessaryBtn: 'Alle ablehnen',
          savePreferencesBtn: 'Auswahl speichern',
          closeIconLabel: 'Schliessen',
          sections: [
            {
              title: 'Cookie-Nutzung',
              description: 'Wir verwenden Cookies, um grundlegende Funktionen der Website zu gewaehrleisten.',
            },
            {
              title: 'Notwendige Cookies',
              description: 'Diese Cookies sind fuer die Grundfunktionen der Website erforderlich.',
              linkedCategory: 'necessary',
            },
            {
              title: 'Analyse-Cookies',
              description: 'Diese Cookies helfen uns zu verstehen, wie Besucher mit der Website interagieren.',
              linkedCategory: 'analytics',
            },
          ],
        },
      },
    },
  },
})
```

### Anti-Patterns to Avoid
- **Hiding legal links:** Impressum/Datenschutz must be visible and accessible from every page, not buried in submenus
- **Pre-checked consent boxes:** GDPR requires active opt-in, never pre-select non-essential cookies
- **Unequal button prominence:** "Accept all" and "Reject all" must have equal visual weight
- **Cookie wall:** Cannot force cookie acceptance as condition for site access
- **Referencing SS5 TMG:** The TMG was replaced by DDG in May 2024 - update to SS5 DDG or omit

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Cookie consent | Custom localStorage + banner | vanilla-cookieconsent | Handles consent categories, script blocking, preference storage, GDPR-compliant UI |
| Impressum content | Generate from scratch | e-recht24.de or datenschutz-generator.de | Legal generators ensure all required fields are included |
| Datenschutzerklaerung | Write from scratch | Free DSGVO generators | Generators cover all Art. 13 DSGVO requirements including third-party services |
| Script blocking | Manual consent checking | vanilla-cookieconsent's data-category | Built-in script management with `type="text/plain" data-category="analytics"` |

**Key insight:** German legal compliance has many precise requirements. Using established generators and libraries prevents costly Abmahnungen (warning letters with legal fees) from missing required disclosures.

## Common Pitfalls

### Pitfall 1: TMG Reference Instead of DDG
**What goes wrong:** Impressum cites "Angaben gemaess SS5 TMG" (repealed law)
**Why it happens:** Copy-pasting old templates or using outdated generators
**How to avoid:** Use "Angaben gemaess SS5 DDG" or omit the legal reference entirely (content is required, citation is optional)
**Warning signs:** Any mention of "TMG" or "Telemediengesetz" in your Impressum

### Pitfall 2: Missing Equal Button Weight
**What goes wrong:** "Accept all" button is prominent, "Reject all" is small/gray
**Why it happens:** UI design prioritizes acceptance
**How to avoid:** Set `equalWeightButtons: true` in cookie consent config; March 2025 German court ruling requires this
**Warning signs:** Visual difference between accept/reject buttons

### Pitfall 3: Incomplete Impressum
**What goes wrong:** Missing required fields like physical address, email, or VAT/W-IdNr
**Why it happens:** Not knowing all requirements or using incomplete templates
**How to avoid:** Use reputable German legal generators (e-recht24.de, datenschutz-generator.de)
**Warning signs:** No street address (PO boxes insufficient), no email (contact forms insufficient), no business identifier

### Pitfall 4: Privacy Policy Not Accessible
**What goes wrong:** Datenschutzerklaerung exists but isn't linked from every page
**Why it happens:** Footer missing from some pages, link only on homepage
**How to avoid:** Add Footer to BaseLayout.astro (not individual pages)
**Warning signs:** Any page without visible Impressum/Datenschutz links in footer

### Pitfall 5: Auto-Reply Email in Impressum
**What goes wrong:** Impressum email only sends auto-replies directing to contact form
**Why it happens:** Attempt to reduce spam while appearing compliant
**How to avoid:** Provide functional email that allows "direct and unrestricted communication" (Munich Regional Court ruling)
**Warning signs:** Auto-reply systems or redirects to contact forms

### Pitfall 6: Cookie Consent Without Script Blocking
**What goes wrong:** Banner shows but analytics scripts load before consent
**Why it happens:** Only implementing UI, not actual blocking mechanism
**How to avoid:** Use `type="text/plain" data-category="analytics"` on script tags; scripts only activate after consent
**Warning signs:** Analytics showing visitors who never consented

## Code Examples

Verified patterns for Astro implementation:

### Impressum Page Structure
```astro
---
// src/pages/impressum.astro
import BaseLayout from '../layouts/BaseLayout.astro';
---

<BaseLayout title="Impressum | Bambini-Fussball Deutschland">
  <main class="container mx-auto px-4 py-8 max-w-3xl">
    <h1 class="text-3xl font-bold mb-8">Impressum</h1>

    <section class="prose prose-gray">
      <h2>Angaben gemaess SS 5 DDG</h2>
      <p>
        [Vollstaendiger Name / Firmenname]<br />
        [Strasse und Hausnummer]<br />
        [PLZ und Ort]
      </p>

      <h2>Kontakt</h2>
      <p>
        E-Mail: [email@example.com]<br />
        Telefon: [optional but recommended]
      </p>

      <h2>Umsatzsteuer-ID</h2>
      <p>
        Umsatzsteuer-Identifikationsnummer gemaess SS 27a UStG: [DE123456789]
        <!-- Or if no VAT ID: Wirtschafts-Identifikationsnummer (W-IdNr.): [number] -->
      </p>

      <h2>Verantwortlich fuer den Inhalt nach SS 18 Abs. 2 MStV</h2>
      <p>
        [Name]<br />
        [Adresse]
      </p>
    </section>
  </main>
</BaseLayout>
```

### Datenschutz Page Structure
```astro
---
// src/pages/datenschutz.astro
import BaseLayout from '../layouts/BaseLayout.astro';
---

<BaseLayout title="Datenschutzerklaerung | Bambini-Fussball Deutschland">
  <main class="container mx-auto px-4 py-8 max-w-3xl">
    <h1 class="text-3xl font-bold mb-8">Datenschutzerklaerung</h1>

    <section class="prose prose-gray">
      <h2>1. Datenschutz auf einen Blick</h2>
      <h3>Allgemeine Hinweise</h3>
      <p>
        Die folgenden Hinweise geben einen einfachen Ueberblick darueber,
        was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen.
      </p>

      <h2>2. Verantwortlicher</h2>
      <p>
        Verantwortlich fuer die Datenverarbeitung auf dieser Website ist:<br />
        [Name/Firma]<br />
        [Adresse]<br />
        E-Mail: [email]
      </p>

      <h2>3. Datenerfassung auf dieser Website</h2>
      <h3>Cookies</h3>
      <p>
        Diese Website verwendet Cookies. [Description of cookie usage]
      </p>

      <h3>Server-Log-Dateien</h3>
      <p>
        Der Provider der Seiten erhebt und speichert automatisch Informationen
        in so genannten Server-Log-Dateien...
      </p>

      <h2>4. Ihre Rechte</h2>
      <p>
        Sie haben jederzeit das Recht auf unentgeltliche Auskunft ueber Ihre
        gespeicherten personenbezogenen Daten, deren Herkunft und Empfaenger
        und den Zweck der Datenverarbeitung sowie ein Recht auf Berichtigung
        oder Loeschung dieser Daten.
      </p>

      <!-- Additional sections as needed based on generator output -->
    </section>
  </main>
</BaseLayout>
```

### Script Blocking Pattern (Future Analytics)
```html
<!-- Only loads when user consents to "analytics" category -->
<script type="text/plain" data-category="analytics">
  // Google Analytics or similar
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Reference SS5 TMG | Reference SS5 DDG (or omit) | May 2024 | Update all Impressum pages |
| Accept/Necessary buttons | Accept all/Reject all equal weight | March 2025 court ruling | Cookie banners need "Reject all" button |
| TTDSG for cookies | TDDDG (renamed) | May 2024 | Same requirements, new name |
| Any VAT/tax ID | W-IdNr required if no VAT ID | November 2024 | Small businesses need business ID |

**Deprecated/outdated:**
- TMG (Telemediengesetz): Replaced by DDG as of May 14, 2024
- TTDSG: Renamed to TDDDG but requirements unchanged
- Implied consent: Germany strictly requires opt-in consent for non-essential cookies

## Open Questions

Things that couldn't be fully resolved:

1. **Exact Impressum Content for This Site**
   - What we know: Required fields are name, address, email, optional phone, tax ID
   - What's unclear: The specific business entity information for this project
   - Recommendation: User must provide business details; use generator at e-recht24.de or datenschutz-generator.de

2. **Analytics Plans**
   - What we know: Current site has no analytics
   - What's unclear: Whether analytics will be added later
   - Recommendation: Implement cookie consent infrastructure now; add analytics category when needed

3. **Third-Party Embeds**
   - What we know: YouTube, social widgets require consent before loading
   - What's unclear: Whether such embeds will be used
   - Recommendation: If embeds are added, implement consent-gated loading with placeholder

## Sources

### Primary (HIGH confidence)
- vanilla-cookieconsent v3.1.0 GitHub - https://github.com/orestbida/cookieconsent - Configuration, script blocking, version info
- Astro Documentation - https://docs.astro.build/en/guides/client-side-scripts/ - Client-side JavaScript patterns
- CookieConsent Official Docs - https://cookieconsent.orestbida.com - Configuration reference, language setup

### Secondary (MEDIUM confidence)
- All About Berlin Guide - https://allaboutberlin.com/guides/website-compliance-germany - Comprehensive German compliance overview
- IONOS Digital Guide - https://www.ionos.com/digitalguide/websites/digital-law/a-case-for-thinking-global-germanys-impressum-laws/ - Impressum requirements 2025/2026
- MTH Partner (German Law Firm) - https://www.mth-partner.de/en/internet-law-imprint-obligation-according-to-the-german-gdpr-create-a-legally-compliant-imprint/ - DDG SS5 requirements

### Tertiary (LOW confidence)
- @jop-software/astro-cookieconsent - https://github.com/jop-software/astro-cookieconsent - Astro integration (limited docs, but functional)
- WebSearch for court rulings - March 2025 Hanover ruling on reject button (verified via multiple sources)

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - vanilla-cookieconsent well-documented, widely used
- Architecture: HIGH - Astro layout/slot pattern is standard
- Legal requirements: MEDIUM - Requirements verified but specific content needs user input
- Pitfalls: HIGH - Multiple authoritative sources confirm common issues

**Research date:** 2026-01-22
**Valid until:** 2026-04-22 (legal requirements may change; re-verify before implementation)
