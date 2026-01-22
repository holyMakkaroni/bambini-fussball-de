// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import cookieconsent from '@jop-software/astro-cookieconsent';
import sitemap from '@astrojs/sitemap';

// https://astro.build/config
export default defineConfig({
  site: 'https://bambini-fussball.pages.dev',
  output: 'static',
  trailingSlash: 'always',
  image: {
    // Enable responsive image optimization with CLS prevention
    // All <Image /> components will automatically get srcset and proper dimensions
    layout: 'constrained',
    responsiveStyles: true,
  },
  vite: {
    plugins: [tailwindcss()],
  },
  integrations: [
    cookieconsent({
      guiOptions: {
        consentModal: {
          layout: 'box',
          position: 'bottom center',
          equalWeightButtons: true, // March 2025 court ruling compliance
          flipButtons: false,
        },
        preferencesModal: {
          layout: 'box',
        },
      },
      categories: {
        necessary: {
          enabled: true,
          readOnly: true,
        },
        analytics: {
          enabled: false, // Disabled by default - user must opt-in
        },
      },
      language: {
        default: 'de',
        translations: {
          de: {
            consentModal: {
              title: 'Cookie-Einstellungen',
              description:
                'Diese Website verwendet Cookies, um Ihre Praferenzen zu speichern. Sie konnen wahlen, welche Cookies Sie zulassen mochten.',
              acceptAllBtn: 'Alle akzeptieren',
              acceptNecessaryBtn: 'Nur notwendige',
              showPreferencesBtn: 'Einstellungen',
            },
            preferencesModal: {
              title: 'Cookie-Einstellungen',
              acceptAllBtn: 'Alle akzeptieren',
              acceptNecessaryBtn: 'Nur notwendige',
              savePreferencesBtn: 'Einstellungen speichern',
              sections: [
                {
                  title: 'Notwendige Cookies',
                  description:
                    'Diese Cookies sind fur die Grundfunktionen der Website erforderlich.',
                  linkedCategory: 'necessary',
                },
                {
                  title: 'Analyse-Cookies',
                  description:
                    'Diese Cookies helfen uns zu verstehen, wie Besucher die Website nutzen.',
                  linkedCategory: 'analytics',
                },
              ],
            },
          },
        },
      },
    }),
    sitemap(),
  ],
});
