# Phase 2 Core Web Vitals Baseline

**Test Date:** 2026-01-22
**Test URL:** https://bambini-fussball.pages.dev/
**Test Tool:** Lighthouse 13.0.1 (via CLI)
**Device Emulation:** Mobile (Moto G Power)
**Network Throttling:** 150ms TCP RTT, 1,638.4 Kbps throughput

## Summary

| Metric | Score | Status |
|--------|-------|--------|
| **Performance** | 100/100 | PASS |
| **Accessibility** | 90/100 | PASS |

## Core Web Vitals

| Metric | Value | Target | Status |
|--------|-------|--------|--------|
| **LCP** (Largest Contentful Paint) | 1.0s | < 2.5s | PASS |
| **CLS** (Cumulative Layout Shift) | 0 | < 0.1 | PASS |
| **TBT** (Total Blocking Time) | 0ms | < 200ms | PASS |

**Note:** TBT (Total Blocking Time) is a proxy for INP (Interaction to Next Paint) in Lighthouse. TBT of 0ms indicates excellent responsiveness.

## Additional Performance Metrics

| Metric | Value | Rating |
|--------|-------|--------|
| First Contentful Paint (FCP) | 1.0s | Good |
| Speed Index | 1.0s | Good |
| Time to Interactive (TTI) | 1.0s | Good |
| Server Response Time (TTFB) | 70ms | Excellent |

## Page Weight

| Resource | Size |
|----------|------|
| Total Transfer Size | 5 KiB |
| CSS (index.VWzQWGNb.css) | 2.9 KiB |
| HTML Document | 1.3 KiB |
| Favicon (SVG) | 0.9 KiB |

**Note:** Extremely lightweight page - no images loaded on homepage currently.

## Mobile-Friendliness

| Criterion | Status | Notes |
|-----------|--------|-------|
| Viewport configured | PASS | meta viewport present |
| Touch targets | PASS | All links meet 44px minimum |
| Responsive layout | PASS | Cards stack vertically on mobile |
| Text readable | PASS | No horizontal scrolling required |

## Accessibility Highlights

- **Score:** 90/100
- **Touch targets:** Sufficient size and spacing (no issues)
- **Document title:** Present ("Bambini-Fussball.de")
- **HTML lang:** Set to "de"
- **Landmark regions:** Main content uses semantic HTML

## Requirements Verification

| Requirement | Target | Actual | Status |
|-------------|--------|--------|--------|
| TECH-01: Mobile-first | Responsive design | Verified | MET |
| TECH-01: Touch targets | 44px minimum | All pass | MET |
| TECH-02: LCP | < 2.5s | 1.0s | MET |
| TECH-02: CLS | < 0.1 | 0 | MET |

## Recommendations for Future

1. **Images:** When hero images are added (Phase 4), ensure they:
   - Have explicit width/height or aspect-ratio
   - Use `loading="lazy"` for below-fold images
   - Use modern formats (WebP/AVIF)

2. **Fonts:** Current system fonts are excellent for performance. If custom fonts are added:
   - Use `font-display: swap`
   - Preload critical fonts
   - Consider variable fonts

3. **JavaScript:** Currently zero JS. When adding interactivity:
   - Keep TBT under 200ms
   - Use code splitting for non-critical features

## Test Environment

```
Lighthouse CLI: 13.0.1
Chrome: Headless
Emulated Device: Moto G Power
CPU Throttling: 4x slowdown
Network: Slow 4G (150ms RTT, 1.6 Mbps)
```

---

*This baseline was established during Phase 2 (Technical Infrastructure) as proof of TECH-01 and TECH-02 requirement compliance.*
