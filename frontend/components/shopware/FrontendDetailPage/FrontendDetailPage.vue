<script setup lang="ts">
import { getProductRoute } from '@shopware-pwa/helpers-next'
import { useLazyAsyncData } from '#app'
import { useBreadcrumbStore } from '~/stores/breadcrumb'
import type { Schemas } from '#shopware'

const props = defineProps({
  navigationId: {
    type: String,
    required: true
  }
})

const { params, query } = useRoute()
const router = useRouter()
const { apiClient } = useShopwareContext()
const { t, locale } = useI18n()
const localePath = useLocalePath()
const { isPreview, version } = useStoryblokHelper()
const { addNotification } = useNotification()

const { data: productResponse } = await useAsyncData(
  'productDetail-' + props.navigationId,
  async () => {
    const { data } = await apiClient.invoke('readProductDetail post /product/{productId}', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        productId: props.navigationId
      },
      body: {
        associations: {
          properties: {
            associations: {
              group: {}
            }
          },
          options: {
            associations: {
              group: {}
            }
          },
          manufacturer: {},
          media: {}
        }
      }
    })

    return data
  },
  {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  })

if (!productResponse.value) {
  throw new Error('No product found for navigationId: ' + props.navigationId)
}

if (productResponse.value.product.seoCategory?.seoUrls?.length) {
  const { breadcrumbs } = useBreadcrumbHelper(productResponse.value.product.seoCategory.seoUrls[0].seoPathInfo)
  const { setBreadcrumb } = useBreadcrumbStore()

  breadcrumbs.push({
    name: productResponse.value.product.translated.name ?? '',
    path: ''
  })

  setBreadcrumb(breadcrumbs)
}

const { product } = useProduct(
  productResponse.value.product,
  productResponse.value.configurator
)

const { addToCart, quantity } = useAddToCart(product)
const ratingAverage = computed(() => product?.value.ratingAverage ?? 0)
const productName = computed(() => product?.value.translated.name)
const manufacturerName = computed(() => product?.value.manufacturer?.translated?.name)
const productImages = computed(() => {
  return product?.value.media?.map((image: Schemas['ProductMedia']) => {
    return {
      id: image.id,
      name: image.media?.translated?.title,
      title: image.media?.translated?.title,
      alt: image.media?.translated?.alt,
      filename: image.media?.path
    }
  })
})

const handleVariantChange = (val: Schemas['Product']) => {
  const newRoute = getProductRoute(val)
  router.push(newRoute)
}

const { data: crossSellingProducts, status: crossSellingProductsStatus } = useLazyAsyncData(
  'crossSellingProducts' + props.navigationId,
  async () => {
    const { data } = await apiClient.invoke('readProductCrossSellings post /product/{productId}/cross-selling', {
      headers: {
        'sw-include-seo-urls': true
      },
      pathParams: {
        productId: props.navigationId
      }
    })

    return data
  }
)

const { data: relatedProducts } = await useLazyAsyncData(
  'relatedProducts' + props.navigationId,
  async () => {
    const { data } = await apiClient.invoke('readProduct post /product', {
      headers: {
        'sw-include-seo-urls': true
      },
      body: {
        limit: 10,
        filter: [
          {
            type: 'not',
            operator: 'or',
            queries: [
              {
                type: 'equals',
                field: 'id',
                value: product.value?.parentId ?? null
              }
            ]
          },
          {
            type: 'multi',
            operator: 'and',
            queries: [
              {
                type: 'equalsAny',
                field: 'categoryIds',
                value: product.value?.categoryIds ?? ''
              },
              {
                type: 'equals',
                field: 'parentId',
                value: null
              }
            ]
          }
        ],
        includes: {
          product: ['name', 'id', 'parentId', 'translated', 'cover', 'calculatedPrice', 'calculatedPrices', 'ratingAverage', 'seoUrls']
        }
      }
    })

    return data.elements || []
  },
  {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  })

const { data: productReviews, status: productReviewsStatus } = await useLazyAsyncData(
  `product-reviews-${props.navigationId}-${locale.value}`,
  async () => {
    const { data } = await apiClient.invoke('readProductReviews post /product/{productId}/reviews', {
      pathParams: {
        productId: product.value.parentId ?? product.value.id
      },
      body: {
        filter: [
          {
            type: 'equals',
            field: 'status',
            value: true
          }
        ]
      }
    })

    return data.elements || []
  },
  {
    // TODO: Remove if bug https://github.com/nuxt/nuxt/issues/18405 is fixed in Vue 3.5
    getCachedData (key, nuxt) {
      return nuxt.payload.data[key] || nuxt.static.data[key]
    }
  })

useHead({
  title: product.value.translated.metaTitle ?? product.value.translated.name,
  meta: [
    {
      name: 'description',
      content: product.value.translated.metaDescription ?? ''
    },
    {
      name: 'keywords',
      content: product.value.translated.keywords ?? ''
    }
  ]
})

const story = await useAsyncStoryblok(`product/${params.all[0]}`, {
  version: isPreview(query) ? 'draft' : version.value,
  resolve_links: 'url',
  language: locale.value
})

const invokeAddToCart = async () => {
  await addToCart()
  addNotification(t('notifications.title.success'), t('notifications.cart.add.success', {
    productName: product.value.translated.name
  }), 'success')
}

const { price, isListPrice } = useProductPrice(ref(product.value))

const percentDiscount = computed(() => price.value?.listPrice?.percentage?.toFixed(0))
</script>

<template>
  <AppBreadcrumb />

  <div class="product-detail-page mt-4 md:mt-9">
    <div class="container">
      <div class="flex flex-col md:flex-row">
        <div class="min-w-0 flex-1">
          <div class="flex flex-col sticky top-4">
            <div
              v-if="isListPrice"
              class="flex-1 flex gap-x-2 absolute -top-2 left-5 z-10">
              <BasePill
                color="red"
                size="base">
                -{{ percentDiscount }} %
              </BasePill>
              <BasePill
                color="black"
                size="small">
                {{ t('components.base.productBox.campaignLabel') }}
              </BasePill>
            </div>
            <BaseImageGallery
              v-if="productImages"
              :show-thumbnails="true"
              :images="productImages" />

            <div v-if="crossSellingProductsStatus === 'success'">
              <BaseCrossSelling
                v-for="(crossSelling, index) in crossSellingProducts"
                :key="index"
                class="hidden md:flex md:flex-col"
                :element="crossSelling" />
            </div>
          </div>
        </div>
        <div class="flex-1 pl-0 pt-5 md:pt-0 md:pl-5">
          <div class="flex flex-col sticky top-4">
            <div class="flex mb-5 items-center">
              <div class="flex-1 flex gap-x-2">
                <BaseWishlist
                  :product-id="product.id"
                  class="border-secondary-light bg-white" />
                <div class="group cursor-pointer rounded-full border border-secondary-light w-11 h-11 flex justify-center items-center">
                  <BaseIcon
                    name="share"
                    class="w-4 group-hover:text-primary" />
                </div>
                <a
                  href="#reviews"
                  class="inline-flex ml-2">
                  <BaseReviewStars
                    :average="ratingAverage"
                    suffix-class="text-xs pt-0.5 ml-1.5"
                    icon-class="w-4">
                    {{ t('components.app.topbar.review.label', {
                      reviewCount: productReviews?.length ?? 0
                    }) }}
                  </BaseReviewStars>
                </a>
              </div>
              <NuxtLink
                v-if="manufacturerName"
                :to="localePath({
                  name: 'brand-slug',
                  params: {
                    slug: (manufacturerName).toLowerCase()
                  }
                })"
                :title="manufacturerName"
                class="text-xs">
                {{ t('components.shopware.detail.manufacturerLabel', {
                  name: manufacturerName
                }) }}
              </NuxtLink>
            </div>
            <BaseHeadline
              v-if="productName"
              tag="h1"
              custom-class="!font-medium text-2xl"
              :title="productName" />
            <BasePrice :product="product" />
            <BaseVariantConfigurator @change="handleVariantChange" />
            <BaseDeliveryInformation />
            <BaseDivider
              variant="solid"
              class="my-5" />
            <div class="flex gap-x-5">
              <FormSelect
                v-model="quantity"
                name="quantity-select"
                :rounded="false"
                select-class="text-right text-xl"
                class="min-w-[75px] max-w-[90px] py-0.5"
                :options="Array.from({ length: 10 }, (_, i) => i + 1).map((key) => {
                  return {
                    label: key,
                    value: key
                  }
                })" />
              <BaseButton
                class="lg:min-w-[310px]"
                variant="green"
                size="big"
                icon="cart"
                icon-position="left"
                icon-class="size-5"
                @click="invokeAddToCart()">
                {{ t('components.shopware.general.labels.cart') }}
              </BaseButton>
            </div>
            <BaseDivider
              variant="solid"
              class="my-5" />
            <ul
              role="list"
              class="text-xs space-y-3">
              <li
                role="listitem"
                class="flex items-start xxs:items-center gap-x-2.5">
                <BaseIcon
                  name="check"
                  class="w-3 h-3 text-success" />
                <span class="flex-1"><strong>{{ t('components.shopware.detail.shipping.feature1.label') }}</strong> {{ t('components.shopware.detail.shipping.feature1.value') }}</span>
              </li>
              <li
                role="listitem"
                class="flex items-center gap-x-2.5">
                <BaseIcon
                  name="check"
                  class="w-3 h-3 text-success" />
                <span class="flex-1"><strong>{{ t('components.shopware.detail.shipping.feature2.label') }}</strong> {{ t('components.shopware.detail.shipping.feature2.value') }}</span>
              </li>
              <li
                role="listitem"
                class="flex items-center gap-x-2.5">
                <BaseIcon
                  name="check"
                  class="w-3 h-3 text-success" />
                <span class="flex-1"><strong>{{ t('components.shopware.detail.shipping.feature3.label') }}</strong> {{ t('components.shopware.detail.shipping.feature3.value') }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <BaseProductDescription :product="product" />

  <BaseRelatedProducts
    v-if="relatedProducts?.length"
    :products="relatedProducts" />

  <div v-if="story">
    <div class="container">
      <BaseDivider
        variant="solid"
        class="pt-6 md:pt-14 mt-6 md:mt-14" />
      <BaseHeadline
        tag="h3"
        custom-class="h2-styling"
        :title="t('components.shopware.detail.additionalInformation', {
          productName: productName
        })" />
    </div>

    <StoryblokComponent :blok="story.content" />

    <div class="container">
      <BaseDivider
        variant="solid"
        class="pt-6 md:pt-14 mt-6 md:mt-14" />
    </div>
  </div>

  <BaseProductReviews
    v-if="productReviewsStatus === 'success'"
    :product="product"
    :product-reviews="productReviews" />
</template>

<style scoped>

</style>
