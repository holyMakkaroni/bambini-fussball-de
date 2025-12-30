import { defineConfig } from 'vitepress'
import { config } from './config'
import { en } from './en'

export default defineConfig({
  ...config,
  locales: {
    root: { label: 'English', ...en }
  }
})