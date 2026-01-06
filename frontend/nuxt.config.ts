import { readFile, writeFile } from 'node:fs/promises'
import { resolve } from 'node:path'
import { writeAmplifyFiles } from 'nitropack/presets/aws-amplify/utils'
import svgLoader from 'vite-svg-loader'
import routes from './lang/routes.json'
export default defineNuxtConfig({
  extends: [
    '@shopware-pwa/composables-next/nuxt-layer'
  ],

  app: {
    head: {
      link: [
        {
          rel: 'preconnect',
          href: process.env.SHOPWARE_CHECKOUT_URL ?? ''
        },
        {
          rel: 'preconnect',
          href: 'https://a.storyblok.com'
        }
      ],
      meta: [
        {
          name: 'google-site-verification',
          content: 'ilOrWvWFn1SsuGju3SG3Ud1fvcW0Vi2yrUo5DTWXsGY'
        }
      ]
    }
  },

  css: [
    '@/assets/css/font.pcss',
    '@/assets/css/all.pcss'
  ],

  runtimeConfig: {
    storyblok: {
      apiToken: process.env.STORYBLOK_API_TOKEN
    },
    cleverreach: {
      clientId: process.env.CLEVERREACH_CLIENT_ID,
      clientSecret: process.env.CLEVERREACH_CLIENT_SECRET
    },
    redis: {
      host: process.env.REDIS_HOST,
      port: process.env.REDIS_PORT,
      username: process.env.REDIS_USERNAME,
      password: process.env.REDIS_PASSWORD
    },
    public: {
      algolia: {
        apiKey: process.env.ALGOLIA_API_KEY,
        applicationId: process.env.ALGOLIA_APPLICATION_ID
      },
      cleverreach: {
        tokenUrl: process.env.CLEVERREACH_TOKEN_URL
      },
      baseWebsiteHandle: process.env.BASE_WEBSITE_HANDLE,
      shopwareCheckoutUrl: process.env.SHOPWARE_CHECKOUT_URL,
      storyblokVersion: process.env.STORYBLOK_VERSION || 'draft',
      storyblokSitemapExclude: process.env.STORYBLOK_SITEMAP_EXCLUDE,
      shopwareBaseUrl: process.env.SHOPWARE_BASE_URL,
      shopwareSitemapUrl: process.env.SHOPWARE_SITEMAP_URL
    }
  },

  shopware: {
    endpoint: process.env.SHOPWARE_ENDPOINT,
    accessToken: process.env.SHOPWARE_ACCESS_TOKEN,
    useUserContextInSSR: true
  },

  modules: [
    '@storyblok/nuxt',
    '@shopware-pwa/nuxt3-module',
    '@pinia/nuxt',
    '@nuxt/image',
    '@nuxtjs/i18n',
    '@nuxtjs/sitemap',
    'nuxt-multi-cache',
    '@atoms-studio/nuxt-swiftsearch'
  ],

  storyblok: {
    accessToken: process.env.STORYBLOK_API_TOKEN,
    apiOptions: {
      cache: {
        clear: 'auto',
        type: 'memory'
      }
    }
  },

  pinia: {
    storesDirs: [
      '@/stores/**'
    ]
  },

  sitemap: {
    sitemaps: {
      pages: {
        sources: [
          '/api/storyblok/sitemap/pages'
        ]
      }
    }
  },

  multiCache: {
    api: {
      enabled: true,
      prefix: '/__redis_cache',
      authorization: 'rKzC8QnVCMrTDTX5cswQsObzsP8td37K',
      cacheTagInvalidationDelay: 60000
    },
    cdn: {
      enabled: true,
      cacheControlHeader: 'Cache-Control'
    },
    data: {
      enabled: true
    }
  },

  image: {
    screens: {
      xs: 320,
      sm: 640,
      md: 768,
      lg: 1024,
      xl: 1280
    },
    quality: 80,
    densities: [1, 2],
    format: ['avif', 'webp'],
    storyblok: {
      baseURL: 'https://a.storyblok.com'
    },
    gumlet: {
      baseURL: process.env.GUMLET_BASE_URL || ''
    }
  },

  i18n: {
    strategy: 'prefix_except_default',
    langDir: './lang',
    lazy: true,
    defaultLocale: 'de-DE',
    detectBrowserLanguage: false,
    vueI18n: './config/i18n.config',
    locales: [
      {
        code: 'en-US',
        file: 'en-US.json'
      },
      {
        code: 'de-DE',
        file: 'de-DE.json'
      }
    ],
    customRoutes: 'config',
    pages: routes
  },

  components: [
    {
      path: '@/components',
      pathPrefix: false
    }
  ],

  postcss: {
    plugins: {
      'tailwindcss/nesting': {},
      tailwindcss: {},
      autoprefixer: {}
    }
  },

  nitro: {
    preset: 'aws-amplify',
    compressPublicAssets: {
      brotli: true
    },
    hooks: {
      compiled: async (nitro) => {
        if (!nitro?.options?.output?.dir) {
          nitro.logger?.warn('Unable to locate Nitro output directory to update deploy-manifest runtime')
          return
        }

        const manifestPath = resolve(nitro.options.output.dir, 'deploy-manifest.json')

        try {
          await writeAmplifyFiles(nitro)

          const manifest = JSON.parse(await readFile(manifestPath, 'utf-8'))

          if (Array.isArray(manifest.computeResources)) {
            manifest.computeResources = manifest.computeResources.map((resource) => ({
              ...resource,
              runtime: 'nodejs22.x'
            }))

            await writeFile(manifestPath, JSON.stringify(manifest, null, 2))
            nitro.logger?.info('Updated Amplify deploy-manifest runtime to nodejs22.x')
          }
        }
        catch (error) {
          nitro.logger?.warn(`Unable to update Amplify deploy-manifest runtime: ${error}`)
        }
      }
    }
  },

  telemetry: false,

  vite: {
    plugins: [
      svgLoader()
    ]
  },

  experimental: {
    componentIslands: true,
    asyncContext: true
  },

  compatibilityDate: '2025-01-24'
})
