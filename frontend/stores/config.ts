import type { ISbStoryData } from 'storyblok-js-client'
import type { StoryblokConfiguration } from '~/types/storyblok.config'

export const useConfigStore = defineStore('config', () => {
  const defaultConfig = ref<ISbStoryData|null>(null)
  const { locale } = useI18n()
  const { public: config } = useRuntimeConfig()

  async function fetchStoryblokConfig (config: StoryblokConfiguration) {
    const { query } = useRoute()
    const { isPreview, version } = useStoryblokHelper()

    return await useAsyncStoryblok(`${config.baseWebsiteHandle}/config/${config.type}`, {
      version: isPreview(query) ? 'draft' : version.value,
      resolve_links: 'url',
      language: locale.value
    })
  }

  async function fetchConfigByType (config: StoryblokConfiguration) {
    const data = await fetchStoryblokConfig(config)

    return data.value
  }

  async function fetchConfig () {
    defaultConfig.value = await fetchConfigByType({
      baseWebsiteHandle: config.baseWebsiteHandle,
      type: 'default'
    })
  }

  return {
    defaultConfig,
    fetchConfig
  }
})
