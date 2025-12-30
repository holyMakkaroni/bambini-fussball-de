---
title: Overview
---

# {{ $frontmatter.title }}

Our frontend architecture is built using Nuxt 3 combined with Tailwind CSS to deliver a modern, responsive, and highly performant user experience. The design is enhanced by the use of the 'Hind' font family, available in Light, Regular, Medium, SemiBold, and Bold weights, ensuring a clean and professional appearance across all devices.

To integrate with our backend, we utilize the [@shopware-pwa/nuxt3-module](https://frontends.shopware.com) on the frontend, which seamlessly connects with @shopware/api-client and @shopware-pwa/composables-next. This setup allows us to efficiently manage data and interactions between the frontend and backend, providing a robust foundation for our e-commerce platform.

Additionally, we've integrated Storyblok as our content management system, enabling us to easily manage and update content such as landing pages, blog posts, wiki entries, and FAQs. This setup empowers our team to deliver dynamic and engaging content across our platform.

## Project structure
```
.
└── frontend/
    ├── assets/
    │   ├── css/
    │   │   ├── all.pcss
    │   │   ├── base.pcss
    │   │   ├── font.pcss
    │   │   └── marquee.pcss
    │   ├── fonts/
    │   │   └── Hind/
    │   │       ├── Hind-Light.woff2
    │   │       ├── Hind-Regular.woff2
    │   │       ├── Hind-Medium.woff2
    │   │       ├── Hind-SemiBold.woff2
    │   │       └── Hind-Bold.woff2
    │   └── icons/
    │       ├── partner/
    │       │   ├── dhl.svg
    │       │   └── ...
    │       ├── payment/
    │       │   ├── creditcard.svg
    │       │   └── ...
    │       ├── bars.svg
    │       ├── building.svg
    │       └── ...
    ├── components/
    │   ├── app/
    │   │   ├── AppHeader/
    │   │   │   ├── AppHeader.pcss // optional
    │   │   │   └── AppHeader.vue
    │   │   ├── AppFooter/
    │   │   │   ├── AppFooter.pcss // optional
    │   │   │   └── AppFooter.vue
    │   │   └── ...
    │   ├── base/
    │   │   ├── BaseHeadline/
    │   │   │   ├── BaseHeadline.pcss // optional
    │   │   │   └── BaseHeadline.vue
    │   │   └── ...
    │   ├── form/
    │   │   ├── FormSelect/
    │   │   │   ├── FormSelect.pcss // optional
    │   │   │   └── FormSelect.vue
    │   │   └── ...
    │   ├── shopware/
    │   │   ├── FrontendDetailPage/
    │   │   │   └── FrontendDetailPage.global.vue
    │   │   ├── FrontendNavigationPage/
    │   │   │   └── FrontendNavigationPage.global.vue
    │   │   ├── ShopwareListing/
    │   │   │   ├── Filter/
    │   │   │   │   ├── Type/
    │   │   │   │   │   ├── ShopwareListingFilterTypeColor.vue
    │   │   │   │   │   ├── ShopwareListingFilterTypeMedia.vue
    │   │   │   │   │   ├── ShopwareListingFilterTypeSelect.vue
    │   │   │   │   │   └── ShopwareListingFilterTypeText.vue
    │   │   │   │   ├── ShopwareListingFilter.vue
    │   │   │   │   └── ShopwareListingFilterProperties.vue
    │   │   │   └── ShopwareListing.vue
    │   │   └── ErrorRoutingNotFound.global.vue
    │   └── wiki/
    │       ├── WikiCard/
    │       │   ├── WikiCard.pcss // optional
    │       │   └── WikiCard.vue
    │       ├── WikiHead/
    │       │   ├── WikiHead.pcss // optional
    │       │   └── WikiHead.vue
    │       └── ...
    ├── composables/
    │   ├── useBreadcrumbHelper.ts
    │   ├── useProductReviewHelper.ts
    │   ├── useShopwareHelper.ts
    │   ├── useStoryblokHelper.ts
    │   ├── useStoryblokResolver.ts
    │   └── useUtilities.ts
    ├── config/
    │   └── i18n.config.ts
    ├── lang/
    │   ├── de-DE.json
    │   ├── en-US.json
    │   └── ....
    ├── layouts/
    │   └── Default.vue
    ├── pages/
    │   └── [...all].vue
    ├── public/
    │   ├── illustrations/
    │   │   ├── filename.svg
    │   │   └── ...
    │   ├── favicon.ico
    │   └── robots.txt
    ├── server/
    │   ├── plugins/
    │   │   └── lazyHydration.ts
    │   └── tsconfig.json
    ├── stores/
    │   ├── breadcrumb.ts
    │   └── config.ts
    ├── storyblok/
    │   ├── components/
    │   │   ├── CTeaser/
    │   │   │   ├── CTeaser.pcss
    │   │   │   └── CTeaser.vue
    │   │   └── ...
    │   └── types/
    │       ├── TypePage.vue
    │       └── ...
    ├── types/
    │   ├── shopware.d.ts
    │   └── storyblok.config.d.ts
    ├── .env.example
    ├── .eslintrc
    ├── .gitignore
    ├── app.config.ts
    ├── app.vue
    ├── nuxt.config.ts
    ├── package,json
    ├── shopware.d.ts
    ├── tailwind.config.js
    └── tsconfig.json
```