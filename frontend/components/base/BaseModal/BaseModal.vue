<script setup lang="ts">
import { onClickOutside } from '@vueuse/core'

const show = ref<Boolean>(false)
const { t } = useI18n()

const emit = defineEmits([
  'openModal',
  'closeModal'
])

const modalRef = ref(null)
const open = () => {
  show.value = true
  emit('openModal')
}

const close = () => {
  show.value = false
  emit('closeModal')
}

defineExpose({
  open,
  close
})

onClickOutside(
  modalRef,
  () => {
    close()
  }
)
</script>

<template>
  <div class="c-base-modal">
    <BaseOverlay
      v-if="show"
      :show-loading-indicator="false" />
    <Transition name="fade-in-out">
      <div
        v-if="show"
        ref="modalRef"
        class="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 container w-full h-full overflow-y-scroll [@media_((min-height:767px)_and_(min-width:767px))]:h-auto bg-white z-50 py-20">
        <button
          class="absolute top-4 right-4"
          :title="t('components.base.modal.close')"
          @click="close">
          <BaseIcon
            name="close"
            class="w-5 h-5 md:w-7 md:h-7 hover:text-primary" />
        </button>
        <div class="px-6">
          <slot />
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>

</style>
