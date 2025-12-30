interface ManufacturerMedia {
  url: string
  alt: string
  title: string
}
export interface Manufacturer {
  name: string
  media: ManufacturerMedia | null
}

export interface ManufacturerLexicon {
  brands: {
    name: string
  }[],
  media?: {
    name: string
    media: ManufacturerMedia
  }[]
}

export interface GroupedManufacturers extends Record<string, ManufacturerLexicon> {}