<script setup lang="ts">
const props = defineProps({
  productId: {
    type: String,
    required: true
  }
})

const { t } = useI18n()
const { addNotification } = useNotification()
const { addToWishlist, isInWishlist, removeFromWishlist } = useProductWishlist(props.productId)

const proxyAddToWishlist = async () => {
  await addToWishlist()
  addNotification(t('notifications.title.success'), t('notifications.wishlist.add.success'), 'success')
}

const proxyRemoveFromWishlist = async () => {
  await removeFromWishlist()
  addNotification(t('notifications.title.success'), t('notifications.wishlist.remove.success'), 'success')
}
</script>

<template>
  <button
    type="button"
    class="c-base-wishlist group/wishlist cursor-pointer rounded-full border w-11 h-11 flex justify-center items-center"
    @click="isInWishlist ? proxyRemoveFromWishlist() : proxyAddToWishlist()">
    <ClientOnly>
      <BaseIcon
        :name="isInWishlist ? 'hearth' : 'hearth-outline'"
        class="w-4 group-hover/wishlist:text-primary"
        :class="{'text-primary': isInWishlist}" />
    </ClientOnly>
  </button>
</template>

<style scoped>

</style>
