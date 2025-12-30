import { defineConfig } from 'vitepress'
import path from 'path';
import { mkGenerator } from "../plugins/mkGenerator";

// https://vitepress.dev/reference/site-config
export const config = defineConfig({
  title: "LivingActive",
  description: "LivingActive documentation about Frontend, Backend and Storyblok",
  srcDir: 'src',
  cleanUrls: true,
  lastUpdated: true,
  rewrites: {
    'en/:splat(.*)': ':splat'
  },
  ignoreDeadLinks: true,
  themeConfig: {
    siteTitle: false,
    logo: '/images/logo.svg',
    outline: {
      level: [2, 3]
    },
    search: {
      provider: 'local'
    },
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Shopware Administration', link: 'https://checkout.livingactive-headless.powered-by-rackspeed.nrw:4433/admin', target: '_blank' },
      { text: 'Storyblok Editor', link: 'https://app.storyblok.com', target: '_blank' }
    ]
  },
  vite: {
    plugins: [
      mkGenerator({
        dirs: [
          {
            sourceDir: path.resolve(__dirname, '../../../frontend/components/base'),
            outputDir: path.resolve(__dirname, '../../src/en/nuxt-3/components/base'),
            templatePath: path.resolve(__dirname, './templates/component-template.hbs'),
          },
          {
            sourceDir: path.resolve(__dirname, '../../../frontend/components/app'),
            outputDir: path.resolve(__dirname, '../../src/en/nuxt-3/components/app'),
            templatePath: path.resolve(__dirname, './templates/component-template.hbs'),
          }
        ],
      })
    ]
  }
})
