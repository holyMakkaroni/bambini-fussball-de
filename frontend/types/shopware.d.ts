import type { Schemas } from "#shopware";

export interface ShopwareAsset {
  id?: string,
  filename: string,
  title?: string,
  alt?: string,
  name?: string
}

export type Breadcrumb = {
  name: string;
  path?: string;
};

export type ListingFilters = {
  id: string,
  code: string,
  label: string,
  name: string,
  displayType: string,
  customFields?: Record<string, never>,
  translated?: Record<string, never>,
  options: Array<Schemas['PropertyGroupOption'] & { count: number }>,
  entities: Array<Schemas['ProductManufacturer'] & { count: number }>
}

export interface Option {
  id: string,
  optionValue: string
}

export interface Property {
  id: string,
  filterName: string|null,
  filterPriority: number,
  options: Option[]
}

export interface Properties {
  [key: string]: Property
}

export interface PropertyMapping {
  properties: Properties;
}