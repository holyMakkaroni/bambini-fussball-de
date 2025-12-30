import { apiClient } from '~/server/apiBuilder'

export default defineNitroPlugin((nitroApp) => {
  nitroApp.hooks.hook('sitemap:index-resolved', async (ctx) => {
    const { public: config } = useRuntimeConfig()
    const response = await apiClient.invoke('readSitemap get /sitemap')

    const filteredFiles = response.data.filter(item =>
      item.filename.startsWith(config.shopwareSitemapUrl)
    )

    if (!filteredFiles.length) {
      return
    }

    filteredFiles.forEach((file) => {
      ctx.sitemaps.push({
        sitemap: file.filename.replace(config.shopwareSitemapUrl, config.shopwareBaseUrl),
        lastmod: file.created
      })
    })
  })
})
