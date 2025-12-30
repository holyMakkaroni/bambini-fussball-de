<script setup lang="ts">
const { notifications, removeNotification } = useNotification()
const { sidebarCartDisplayStatus } = useSidebarCart()
const { sidebarLoginDisplayStatus } = useSidebarLogin()
const { sidebarWishlistDisplayStatus } = useSidebarWishlist()

const iconClass = computed(() => {
  return 'size-3 text-white'
})

const isSidebarOpen = computed(() => sidebarCartDisplayStatus.value || sidebarLoginDisplayStatus.value || sidebarWishlistDisplayStatus.value)
</script>

<template>
  <div
    class="fixed w-full xs:max-w-[400px] top-0 right-0 left-0 xs:left-auto xs:top-3 z-[60]"
    :class="{ 'min-[812px]:right-[412px]': isSidebarOpen, 'xs:right-3': !isSidebarOpen }">
    <TransitionGroup
      name="notification"
      tag="div">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="relative w-full mb-3 py-8 xs:py-10 px-4 xs:pl-6 xs:pr-11 bg-white shadow-[0_10px_20px_0px_rgba(0,0,0,0.25)] flex items-center justify-between border-l-4 gap-x-5"
        :class="{
          'border-success': notification.type === 'success',
          'border-primary': notification.type === 'error',
          'border-warning': notification.type === 'warning',
          'border-info': notification.type === 'info',
        }"
        role="alert">
        <button class="absolute top-3 right-3 group">
          <BaseIcon
            name="close"
            class="size-3 text-gray-500 transition-default group-hover:text-primary"
            @click="removeNotification(notification.id)" />
        </button>
        <div
          class="size-8 flex justify-center items-center rounded-full"
          :class="{
            'bg-success': notification.type === 'success',
            'bg-primary': notification.type === 'error',
            'bg-warning': notification.type === 'warning',
            'bg-info': notification.type === 'info',
          }">
          <BaseIcon
            v-if="notification.type === 'success'"
            name="check"
            :class="[iconClass]" />
          <BaseIcon
            v-else-if="notification.type === 'error'"
            name="close"
            :class="[iconClass]" />
          <BaseIcon
            v-else-if="notification.type === 'warning'"
            name="exclamation"
            :class="[iconClass]" />
          <BaseIcon
            v-else-if="notification.type === 'info'"
            name="exclamation"
            :class="[iconClass, 'transform rotate-180']" />
        </div>
        <div class="flex flex-col flex-1 text-xs">
          <div
            v-if="notification.title"
            class="font-semibold">
            {{ notification.title }}
          </div>
          <div>{{ notification.message }}</div>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>

</style>
