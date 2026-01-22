// @ts-check
import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';

// https://astro.build/config
export default defineConfig({
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
});
