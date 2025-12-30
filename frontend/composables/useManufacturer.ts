import type { GroupedManufacturers, Manufacturer } from '~/types/manufacturers'

export function useManufacturer () {
  const { apiClient } = useShopwareContext()

  // Extract constant for readability
  const LIMIT = 100

  // Helper function to map response into Manufacturer structure
  const mapManufacturer = (manufacturer: any): Manufacturer => {
    const name = manufacturer.translated?.name || manufacturer.name

    return {
      name,
      media: manufacturer.media
        ? {
            url: manufacturer.media.url,
            alt: manufacturer.media.translated?.alt || name,
            title: manufacturer.media.translated?.title || name
          }
        : null
    }
  }

  const fetchManufacturers = async () => {
    const { data } = await useCachedAsyncData(
      'fetchAllManufacturers',
      async () => {
        const manufacturers: Manufacturer[] = []
        let page = 1
        let total: number = 0

        do {
          const response = await apiClient.invoke('readProductManufacturer post /product-manufacturer', {
            body: {
              limit: LIMIT,
              page,
              associations: { media: {} },
              'total-count-mode': 'exact'
            }
          })

          if (page === 1) {
            total = response.data.total ?? 0
          }

          manufacturers.push(
            ...response.data.elements.map(mapManufacturer)
          )

          page++
        } while (manufacturers.length < total)

        return manufacturers
      },
      {
        serverMaxAge: 3600,
        serverCacheTags: ['shopware:product_manufacturers']
      }
    )

    return data.value ?? []
  }

  const formatManufacturersLexicon = (manufacturers: Manufacturer[]) => {
    const groupedManufacturers: GroupedManufacturers = {}

    manufacturers.forEach(({ name, media }) => {
      const initialLetter = name.charAt(0).toUpperCase()
      const group = /^[A-Z]$/.test(initialLetter) ? initialLetter : '0-9'

      if (!groupedManufacturers[group]) {
        groupedManufacturers[group] = { brands: [], media: [] }
      }

      groupedManufacturers[group].brands.push({ name })

      const mediaLength = groupedManufacturers[group]?.media?.length ?? 0

      if (media && mediaLength < 6) {
        groupedManufacturers[group].media?.push({ name, media })
      }
    })

    Object.keys(groupedManufacturers).forEach((key) => {
      groupedManufacturers[key].brands.sort((a, b) => a.name.localeCompare(b.name))
    })

    return Object.fromEntries(
      Object.entries(groupedManufacturers)
        .sort(([a], [b]) => a.localeCompare(b))
    )
  }

  return {
    fetchManufacturers,
    formatManufacturersLexicon
  }
}
