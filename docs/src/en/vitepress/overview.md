---
title: Overview
---

# {{ $frontmatter.title }}

## Project structure
```
.
└── docs/
    ├── .vitepress/
    │   ├── config/
    │   │   ├── config.ts
    │   │   ├── index.ts
    │   │   ├── en.ts
    │   │   └── ...
    │   └── theme/
    │       ├── custom.css
    │       └── index.ts
    ├── src/
    │   ├── public/
    │   │   └── images/
    │   │       ├── system-architecture/
    │   │       │   └── example.svg
    │   │       └── logo.svg
    │   ├── en/
    │   │   ├── system-architecture/
    │   │   │   ├── overview.md
    │   │   │   └── ...
    │   │   └── index.md
    │   └── ...
    ├── .gitignore
    └── package.json
```