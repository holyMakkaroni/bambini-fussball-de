import { useCDNHeaders } from '#nuxt-multi-cache/composables'
export default defineEventHandler((event) => {
  useCDNHeaders((helper) => {
    helper
      .public()
      .setNumeric('maxAge', 0)
      .setNumeric('sharedMaxAge', 120)
      .setNumeric('staleWhileRevalidate', 86400)
  }, event)
})
