import type { LocationQuery } from 'vue-router'
import type { ISbStoriesParams } from 'storyblok-js-client'
import type { StoryblokAsset, StoryblokFocalPointCoordinates, StoryblokLink } from '~/types/storyblok.config'
import { cleanUrl as cleanUrlUtility, removeTrailingSlash as removeTrailingSlashUtility } from '@/utils/storyblok'

export const useStoryblokHelper = () => {
  const { public: config } = useRuntimeConfig()
  const version = ref<ISbStoriesParams['version']>('draft')
  const websiteHandle = ref<string>('')

  websiteHandle.value = config.baseWebsiteHandle

  // @ts-expect-error TODO: Bug from Nuxt 3. Typescript Issue:
  version.value = config.storyblokVersion

  const isPreview = (query: LocationQuery) => {
    return query?._storyblok || false
  }

  const focalPosition = (image: StoryblokAsset) : StoryblokFocalPointCoordinates => {
    if (!image.filename) {
      return {
        x: 0,
        y: 0
      }
    }

    const dimensions = {
      w: Number(image.filename.split('/')[5].split('x')[0]),
      h: Number(image.filename.split('/')[5].split('x')[1])
    }

    const focus = {
      x: Number(image.focus.split(':')[0].split('x')[0]),
      y: Number(image.focus.split(':')[0].split('x')[1])
    }

    return {
      x: Number(((focus.x / dimensions.w) * 100).toFixed(2)),
      y: Number(((focus.y / dimensions.h) * 100).toFixed(2))
    }
  }

  const storyblokUrl = (link: StoryblokLink) => {
    const url = ref<string|undefined>(undefined)

    switch (link.linktype) {
      case 'story':
        url.value = removeTrailingSlash(`/${link.cached_url}`)
        break
      case 'url':
        url.value = link.url
        break
      default:
    }

    return url.value
  }

  const removeTrailingSlash = (path: string) => {
    return removeTrailingSlashUtility(path)
  }

  const cleanUrl = (path: string) => {
    return cleanUrlUtility(path, websiteHandle.value)
  }

  return {
    version,
    isPreview,
    focalPosition,
    storyblokUrl,
    removeTrailingSlash,
    websiteHandle,
    cleanUrl
  }
}
