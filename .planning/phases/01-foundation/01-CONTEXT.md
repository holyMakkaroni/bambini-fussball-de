# Phase 1: Foundation - Context

**Gathered:** 2025-01-19
**Status:** Ready for planning

<domain>
## Phase Boundary

Establish a working Astro project with Tailwind CSS, clean URL structure, and HTTPS-ready configuration for Cloudflare Pages deployment. This phase creates the technical foundation — no content, no styling beyond basic setup.

</domain>

<decisions>
## Implementation Decisions

### Projektstruktur
- Neuer Ordner im LIA2-Verzeichnis (Claude wählt Namen)
- Content-Organisation: `content/trainer/`, `content/eltern/`, `content/vereine/` (flache Struktur, ein Ordner pro Zielgruppe)
- Dateinamen: Slug-basiert ohne Datum (z.B. `ballbeherrschung-ueben.md`)

### URL-Design
- Trailing Slash: Mit Slash (`/trainer/ballbeherrschung/`)
- Sprache: Deutsche URLs für deutsche Keywords
- Tiefe: Flach, maximal 2 Ebenen (`/kategorie/artikel/`)
- Umlaute: Umschreiben (ä→ae, ö→oe, ü→ue, ß→ss)

### Deployment
- Plattform: Cloudflare Pages
- Domain: Erstmal Cloudflare-Subdomain, Custom Domain später
- Deployment-Methode: GitHub verbinden (Push → automatischer Build)
- Repository: Neues GitHub-Repository erstellen

### Claude's Discretion
- Exakter Projektordner-Name
- Astro-Konfigurationsdetails
- Tailwind-Setup-Spezifika
- Build-Optimierungen

</decisions>

<specifics>
## Specific Ideas

- URLs sollen wie Verzeichnisse aussehen (mit Trailing Slash)
- Deutsche Sprache in URLs für besseres deutsches SEO
- Umlaute technisch sicher umschreiben (keine URL-Encoding-Probleme)

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope

</deferred>

---

*Phase: 01-foundation*
*Context gathered: 2025-01-19*
