import { defineConfig } from 'vitepress'
import {generateSidebar} from "vitepress-sidebar";

const vitepressSidebarOptions = {
  scanStartPath: 'src/en',
  useTitleFromFrontmatter: true,
  collapsed: true,
  collapseDepth: 2,
  hyphenToSpace: true,
  capitalizeFirst: true,
  sortMenusByFrontmatterOrder: true,
  manualSortFileNameByPriority: [
    'system-architecture',
    'shopware-6',
    'nuxt-3',
    'storyblok',
    'vitepress',
    'overview.md',
    'hosting.md',
  ]
};

export const en = defineConfig({
  lang: 'en-US',
  description: 'Vite & Vue powered static site generator.',
  themeConfig: {
    sidebar: generateSidebar(vitepressSidebarOptions)
  }
})

console.log(generateSidebar(vitepressSidebarOptions)[2])