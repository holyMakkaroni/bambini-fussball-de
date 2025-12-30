import StoryblokClient, { type ISbLinks, type ISbLinksResult } from 'storyblok-js-client'
import type { SitemapUrlInput } from '#sitemap/types'
import { defineSitemapEventHandler } from '#sitemap/server/composables/defineSitemapEventHandler'
import micromatch from 'micromatch'
import { cleanUrl } from '~/utils/storyblok'

export default defineSitemapEventHandler(async () => {
  const { public: config } = useRuntimeConfig()

  const response = await getLinks()

  const exclude = config.storyblokSitemapExclude.split(',')
  const pages = response ? Object.values(response) : []

  return [
    ...pages
      .filter((page) => {
        const path = page.real_path ?? ''
        return !micromatch.isMatch(path, exclude)
      })
      .map(page => asSitemapUrl({
        loc: cleanUrl(page.real_path ?? '', config.baseWebsiteHandle),
        lastmod: new Date().toISOString(),
        changefreq: 'daily',
        priority: 1,
        _sitemap: 'pages'
      }))
  ] as SitemapUrlInput[]
})

const getLinks = async (): Promise<ISbLinks['links']> => {
  const { public: config, storyblok } = useRuntimeConfig()

  const Storyblok = new StoryblokClient({
    accessToken: storyblok.apiToken,
    cache: {
      clear: 'auto',
      type: 'memory'
    }
  })

  const links = {}
  const perPage = 100
  let currentPage = 1

  while (true) {
    const response: ISbLinksResult = await Storyblok.get('cdn/links/', {
      per_page: perPage,
      page: currentPage++,
      version: (config.storyblokVersion as 'draft' | 'published') || 'draft'
    })

    Object.assign(links, response.data.links)
    if (response.total <= (currentPage - 1) * perPage) {
      break
    }
  }

  return links
}
