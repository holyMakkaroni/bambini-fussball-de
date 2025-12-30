<script setup lang="ts">
import type { operations, Schemas } from '#shopware'

const { apiClient } = useShopwareContext()

const props = defineProps({
  wishlistProducts: {
    type: Array as PropType<string[]>,
    default: () => []
  }
})

const wishlist = toRefs(props)

const { t } = useI18n()
const { addNotification } = useNotification()
const { clearWishlist } = useWishlist()
const { addProducts } = useCart()

const productData = ref<{ id: string, imagePath?: string, label?: string, options?: Schemas['LineItem']['payload']['options'], itemTotalPrice: number }[]>([])
const totalPrice = ref<number>(0)
const isEmpty = computed(() => wishlist.wishlistProducts.value.length === 0)
const products = computed(() => [...productData.value].reverse())

const loadProductsByItemIds = async (itemIds: string[]): Promise<void> => {
  if (!itemIds.length) {
    return
  }

  try {
    const { data } = await apiClient.invoke('readProduct post /product', {
      body: {
        ids: itemIds,
        associations: {
          options: {
            associations: {
              group: {}
            }
          }
        },
        includes: {
          product: ['name', 'id', 'translated', 'cover', 'calculatedPrice', 'options']
        }
      }
    })

    if (data?.elements) {
      productData.value = data.elements.map((product) => {
        return {
          id: product.id,
          imagePath: product.cover?.media.path,
          label: product.translated.name,
          options: product.options
            ?.sort((a, b) => a.group.name.localeCompare(b.group.name))
            .map((option) => {
              return {
                group: option.group.name,
                option: option.name,
                translated: {
                  group: option.group.translated?.name,
                  option: option.translated.name
                }
              }
            }),
          itemTotalPrice: product.calculatedPrice.unitPrice
        }
      })

      calculateTotalPrice()
    }
  } catch (error) {
    console.error('[wishlist][loadProductsByItemIds]', error)
  }
}

const calculateTotalPrice = () => {
  totalPrice.value = 0

  productData.value.forEach((product) => {
    totalPrice.value += product.itemTotalPrice
  })
}

const addAllProductsToCart = async () => {
  const items: operations['addLineItem post /checkout/cart/line-item']['body']['items'] = productData.value.map((product) => {
    return {
      id: product.id ?? undefined,
      referencedId: product.id,
      quantity: 1,
      type: 'product'
    }
  })

  await addProducts(items)
  clearWishlist()
}

onMounted(() => {
  loadProductsByItemIds(wishlist.wishlistProducts.value)
})

const removeProductFromWishlist = (removeFromWishlist: () => Promise<void>) => {
  removeFromWishlist()

  addNotification(t('notifications.title.success'), t('notifications.wishlist.remove.success'), 'success')
}

watch(wishlist.wishlistProducts, async (items, oldItems) => {
  if (items.length !== oldItems?.length) {
    productData.value = productData.value.filter(({ id }) => items.includes(id))
  }
  if (!items.length) {
    return
  }
  await loadProductsByItemIds(items)
},
{
  deep: true,
  immediate: true
}
)
</script>

<template>
  <div class="base-sidebar-wishlist flex flex-col h-full">
    <ClientOnly>
      <AppSidebarPartialsHeader
        icon="hearth-outline"
        :icon-badge="wishlistProducts.length"
        :show-badge="!isEmpty"
        :headline="!isEmpty ? t('components.base.sidebarWishlist.headline') : t('components.base.sidebarWishlist.headlineEmpty')" />

      <BaseSidebarCartWrapper
        :total-price="totalPrice"
        :is-empty="isEmpty"
        :action-button-label="t('components.base.sidebarWishlist.checkout')"
        @action-button="addAllProductsToCart">
        <template #default>
          <ul
            v-if="!isEmpty"
            class="flex flex-col divide-y divide-solid divide-secondary-light">
            <li
              v-for="product in products"
              :key="product.id"
              class="flex py-5 px-10">
              <BaseSidebarProductItem
                :product-id="product.id"
                :item-options="product.options"
                :item-total-price="product.itemTotalPrice"
                :label="product.label"
                :image-path="product.imagePath"
                @remove-item="removeProductFromWishlist" />
            </li>
          </ul>
          <BaseIcon
            v-else
            name="hearth-outline"
            class="size-64 text-secondary-light" />
        </template>
      </BaseSidebarCartWrapper>
    </ClientOnly>
  </div>
</template>

<style scoped>

</style>
