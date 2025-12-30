import type { Hit } from "instantsearch.js";

interface AlgoliaPrice {
  net: number,
  gross: number,
}

interface AlgoliaPricing extends AlgoliaPrice {
  listPrice?: AlgoliaPrice,
  percentage?: AlgoliaPrice,
  regulationPrice?: AlgoliaPrice,
  inSale?: boolean
}
export interface AlgoliaItem extends Hit<Record<string, any>> {
  defaultPricing: AlgoliaPricing;
  advancedPricing?: Record<string, AlgoliaPricing>;
}