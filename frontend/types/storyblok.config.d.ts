import type { ISbRichtext, ISbStoryData } from "storyblok-js-client";
import type { ISbComponentType } from "storyblok-js-client/src/interfaces";
import type { ShopwareAsset } from "@/types/shopware";

export interface StoryblokConfiguration {
  baseWebsiteHandle: string
  type: 'default'
}

export interface StoryblokAsset extends ShopwareAsset {
  focus: string
}

export interface StoryblokFocalPointCoordinates {
  x: number,
  y: number
}

export interface StoryblokLink {
  cached_url: string,
  fieldtype: string,
  id: string,
  linktype: 'story' | 'url',
  story: ISbStoryData,
  url?: string,
  target?: '_blank'
}

export interface StoryblokDefaultConfigTopbarUsp extends ISbComponentType {
  icon?: string,
  title: ISbRichtext
}

export interface StoryblokDefaultConfigMenuItem extends ISbComponentType {
  link: StoryblokLink,
  title: string
}

export interface StoryblokDefaultConfigFooterColumn extends ISbComponentType {
  title: string,
  links: StoryblokDefaultConfigMenuItem[]
}

export interface StoryblokCIconTeaser extends ISbComponentType {
  headline: string,
  description?: string,
  icon: string,
  link?: StoryblokLink
}

export interface StoryblokCIconTeaser extends ISbComponentType {
  headline: string,
  description?: string,
  icon: string,
  link?: StoryblokLink
}

export interface StoryblokCIconTeaserSmall extends ISbComponentType {
  headline: string,
  icon: string
}

export interface StoryblokDefaultConfig {
  content?: {
    topbar_usps: StoryblokDefaultConfigTopbarUsp[],
    topnavigation: StoryblokDefaultConfigMenuItem[],
    about_navigation: string,
    service_navigation: string,
    teaser_headline?: string,
    teaser: StoryblokCIconTeaser[],
    payment_headline?: string,
    payment: StoryblokCIconTeaserSmall[],
    partner_headline?: string,
    partner: StoryblokCIconTeaserSmall[],
    footer_columns: StoryblokDefaultConfigFooterColumn[],
    footer_links: StoryblokDefaultConfigMenuItem[],
    notice?: ISbRichtext
  }
}

export interface StoryblokShopwareCategoryResponse {
  description?: string
  id: string
  name?: string
}

export interface StoryblokShopwareIntegrationItem {
  type: 'product' | 'productStream' | 'category',
  id: string,
  name: string,
}

export interface StoryblokShopwareProductResponse {
  description?: string
  id: string
  name?: string
}

export interface StoryblokShopwareProductStreamResponse {
  description?: string
  id: string
  name?: string
}

export interface Datasource {
  cv: number,
  datasource_entries: DatasourceEntry[],
}

export interface DatasourceEntry {
  id: string
  name: string
  value: string
  dimension_value: string|null
}

export interface DefaultStoryblokComponentProps<T> {
  blok: T
}