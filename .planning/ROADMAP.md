# Roadmap: Bambini-Fussball Content Website

## Overview

This roadmap transforms a blank Astro project into Germany's premier information resource for Bambini-Fussball. The journey progresses from technical foundation through legal compliance, content architecture, SEO infrastructure, content creation across three target audiences (Trainer, Eltern, Vereine), and culminates with performance optimization and launch readiness. Each phase delivers a verifiable capability that builds toward the core value: becoming the best and most complete information source for Bambini-Fussball in Germany.

## Phases

**Phase Numbering:**
- Integer phases (1, 2, 3): Planned milestone work
- Decimal phases (2.1, 2.2): Urgent insertions (marked with INSERTED)

Decimal phases appear between their surrounding integers in numeric order.

- [x] **Phase 1: Foundation** - Astro project setup with Tailwind, clean URLs, and dev environment
- [x] **Phase 2: Technical Infrastructure** - Mobile responsiveness and Core Web Vitals baseline
- [x] **Phase 3: Legal Compliance** - Impressum, Datenschutz, and Cookie consent (DSGVO)
- [x] **Phase 4: Content Architecture** - Pillar-cluster structure with category pages and navigation
- [x] **Phase 5: SEO Infrastructure** - Meta tags, JSON-LD schemas, and author/about pages
- [x] **Phase 6: Content Creation - Trainer** - 8-10 articles targeting trainers/coaches
- [x] **Phase 7: Content Creation - Eltern** - 8-10 articles targeting parents
- [ ] **Phase 8: Content Creation - Vereine** - 6-8 articles targeting clubs/associations
- [ ] **Phase 9: Related Content System** - Related articles feature with cross-linking
- [ ] **Phase 10: Performance Optimization** - Image optimization (WebP, lazy-load, responsive)
- [ ] **Phase 11: Launch Preparation** - Sitemap, robots.txt, final verification

## Phase Details

### Phase 1: Foundation
**Goal**: Establish a working Astro project with Tailwind CSS, clean URL structure, and HTTPS-ready configuration for Cloudflare Pages deployment
**Depends on**: Nothing (first phase)
**Requirements**: TECH-05, TECH-06
**Success Criteria** (what must be TRUE):
  1. Astro dev server runs locally with Tailwind CSS styling applied
  2. URLs follow clean pattern without file extensions (e.g., /trainer/ballbeherrschung/)
  3. Project deploys to Cloudflare Pages with HTTPS enabled
  4. Folder structure supports future content categories (trainer, eltern, vereine)
**Plans**: 2 plans

Plans:
- [x] 01-01-PLAN.md — Astro project scaffolding with Tailwind CSS 4
- [x] 01-02-PLAN.md — Cloudflare Pages deployment configuration

### Phase 2: Technical Infrastructure
**Goal**: Website passes Google Mobile-Friendly Test and establishes Core Web Vitals baseline
**Depends on**: Phase 1
**Requirements**: TECH-01, TECH-02
**Success Criteria** (what must be TRUE):
  1. Website passes Google Mobile-Friendly Test
  2. Lighthouse mobile score shows LCP < 2.5s on sample page
  3. Layout shifts are minimal (CLS < 0.1) during page load
  4. Touch targets and text sizing are appropriate for mobile devices
**Plans**: 2 plans

Plans:
- [x] 02-01-PLAN.md — Configure image optimization and touch-friendly interactive elements
- [x] 02-02-PLAN.md — Core Web Vitals baseline measurement and verification

### Phase 3: Legal Compliance
**Goal**: Website is legally compliant for German market with Impressum, Datenschutzerklarung, and Cookie consent
**Depends on**: Phase 2
**Requirements**: LEGAL-01, LEGAL-02, LEGAL-03
**Success Criteria** (what must be TRUE):
  1. Impressum page is accessible from footer on every page
  2. Datenschutzerklarung page exists with DSGVO-required content
  3. Cookie consent banner appears on first visit
  4. User can accept/reject cookies, choice is remembered
  5. Privacy links are accessible from every page via footer
**Plans**: 2 plans

Plans:
- [x] 03-01-PLAN.md — Footer component with Impressum and Datenschutz pages
- [x] 03-02-PLAN.md — Cookie consent banner implementation

### Phase 4: Content Architecture
**Goal**: Pillar-cluster content structure is in place with category overview pages and breadcrumb navigation
**Depends on**: Phase 3
**Requirements**: CONT-01, CONT-02, CONT-03
**Success Criteria** (what must be TRUE):
  1. Three pillar pages exist: /trainer/, /eltern/, /vereine/
  2. Each pillar page shows category overview with space for article listings
  3. Breadcrumb navigation appears on all article pages (Home > Category > Article)
  4. Navigation menu provides access to all three target audience categories
  5. Markdown content files render correctly as article pages
**Plans**: 3 plans

Plans:
- [x] 04-01-PLAN.md — Content config migration and Header navigation component
- [x] 04-02-PLAN.md — Pillar category pages and ArticleCard component
- [x] 04-03-PLAN.md — ArticleLayout with breadcrumbs and dynamic article routes

### Phase 5: SEO Infrastructure
**Goal**: Every page has unique meta tags, articles have JSON-LD schema markup, and author/about pages establish E-E-A-T
**Depends on**: Phase 4
**Requirements**: SEO-01, SEO-02, SEO-03, SEO-04, SEO-05, SEO-06
**Success Criteria** (what must be TRUE):
  1. Every page has unique meta title (< 60 chars) and description (< 160 chars)
  2. Article pages include Article schema (JSON-LD) in page source
  3. Author schema links articles to author entity
  4. Author page exists with bio, credentials, and photo
  5. About page explains website purpose and operator
**Plans**: 4 plans

Plans:
- [x] 05-01-PLAN.md — Meta tag system with canonical URL and Open Graph tags
- [x] 05-02-PLAN.md — Author collection with reference() and Article JSON-LD
- [x] 05-03-PLAN.md — Author profile page and About page with structured data
- [x] 05-04-PLAN.md — Gap closure: Add missing author photo image (from verification)

### Phase 6: Content Creation - Trainer
**Goal**: 8-10 high-quality articles published for the Trainer target audience covering coaching topics
**Depends on**: Phase 5
**Requirements**: CONT-05 (partial - Trainer portion)
**Success Criteria** (what must be TRUE):
  1. 8-10 articles exist under /trainer/ category
  2. Each article has unique meta title and description
  3. Each article includes Article schema markup
  4. Articles cover diverse Trainer topics (Ubungen, Spielformen, Trainingsplanung)
  5. Articles are accessible from Trainer category overview page
**Plans**: 3 plans

Plans:
- [x] 06-01-PLAN.md — Core methodology articles (beginner guide, DFB formats, session structure)
- [x] 06-02-PLAN.md — Practical training activities (chase games, dribbling, shooting)
- [x] 06-03-PLAN.md — Trainer development (common mistakes, communication, rituals)

### Phase 7: Content Creation - Eltern
**Goal**: 8-10 high-quality articles published for the Eltern target audience covering parent topics
**Depends on**: Phase 6
**Requirements**: CONT-05 (partial - Eltern portion)
**Success Criteria** (what must be TRUE):
  1. 8-10 articles exist under /eltern/ category
  2. Each article has unique meta title and description
  3. Each article includes Article schema markup
  4. Articles cover diverse Eltern topics (Forderung, Ausrustung, Vereinswahl)
  5. Articles are accessible from Eltern category overview page
**Plans**: 2 plans

Plans:
- [x] 07-01-PLAN.md — Core parent articles (first day, equipment, club selection, sideline, pressure-free support)
- [x] 07-02-PLAN.md — Supporting parent articles (motivation, DFB formats, training quality, nutrition, FAQ)

### Phase 8: Content Creation - Vereine
**Goal**: 6-8 high-quality articles published for the Vereine target audience covering club/association topics
**Depends on**: Phase 7
**Requirements**: CONT-05 (partial - Vereine portion)
**Success Criteria** (what must be TRUE):
  1. 6-8 articles exist under /vereine/ category
  2. Each article has unique meta title and description
  3. Each article includes Article schema markup
  4. Articles cover diverse Vereine topics (Organisation, Jugendarbeit, Spielbetrieb)
  5. Articles are accessible from Vereine category overview page
**Plans**: 2 plans

Plans:
- [ ] 08-01-PLAN.md — Core club articles (trainer recruitment, DFB reform implementation, Spielfest organization, parent management)
- [ ] 08-02-PLAN.md — Supporting club articles (FairPlayLiga, DFB-Mobil, child protection, building Bambini program)

### Phase 9: Related Content System
**Goal**: Every article displays 3-4 related article links at the end, enabling content discovery
**Depends on**: Phase 8
**Requirements**: CONT-04
**Success Criteria** (what must be TRUE):
  1. Related articles section appears at bottom of every article
  2. Each article shows 3-4 contextually relevant links
  3. Related articles include title and brief description
  4. Links work and lead to actual articles
**Plans**: TBD

Plans:
- [ ] 09-01: Related articles component and logic
- [ ] 09-02: Cross-linking configuration for all content

### Phase 10: Performance Optimization
**Goal**: All images are optimized with WebP format, lazy loading, and responsive sizing
**Depends on**: Phase 9
**Requirements**: PERF-01, PERF-02, PERF-03
**Success Criteria** (what must be TRUE):
  1. Images below the fold lazy-load (not loaded until needed)
  2. Images are served in WebP format
  3. Images are served in appropriate sizes for device viewport
  4. Lighthouse performance score remains above 90 on mobile
**Plans**: TBD

Plans:
- [ ] 10-01: Astro Image optimization configuration
- [ ] 10-02: Image component with lazy loading and responsive sizes

### Phase 11: Launch Preparation
**Goal**: Technical SEO elements verified, sitemap and robots.txt working, site ready for search engine indexing
**Depends on**: Phase 10
**Requirements**: TECH-03, TECH-04
**Success Criteria** (what must be TRUE):
  1. XML sitemap is auto-generated and includes all pages
  2. Sitemap is accessible at /sitemap.xml
  3. robots.txt exists and allows search engine crawling
  4. All pages are reachable from sitemap
  5. Site passes final pre-launch checklist
**Plans**: TBD

Plans:
- [ ] 11-01: Sitemap generation verification
- [ ] 11-02: Robots.txt and final SEO verification
- [ ] 11-03: Pre-launch checklist and smoke testing

## Progress

**Execution Order:**
Phases execute in numeric order: 1 -> 2 -> 3 -> ... -> 11

| Phase | Plans Complete | Status | Completed |
|-------|----------------|--------|-----------|
| 1. Foundation | 2/2 | Complete | 2026-01-22 |
| 2. Technical Infrastructure | 2/2 | Complete | 2026-01-22 |
| 3. Legal Compliance | 2/2 | Complete | 2026-01-22 |
| 4. Content Architecture | 3/3 | Complete | 2026-01-22 |
| 5. SEO Infrastructure | 4/4 | Complete | 2026-01-22 |
| 6. Content - Trainer | 3/3 | Complete | 2026-01-22 |
| 7. Content - Eltern | 2/2 | Complete | 2026-01-22 |
| 8. Content - Vereine | 0/2 | Not started | - |
| 9. Related Content | 0/2 | Not started | - |
| 10. Performance | 0/2 | Not started | - |
| 11. Launch Prep | 0/3 | Not started | - |
