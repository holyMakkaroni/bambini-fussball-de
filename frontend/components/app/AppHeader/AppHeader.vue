<script setup lang="ts">
const { count } = useCart()
const { getWishlistProducts, items: wishlistProducts } = useWishlist()
const { sidebarCartDisplayStatus, open: openCartSidebar, close: closeCartSidebar } = useSidebarCart()
const { sidebarLoginDisplayStatus, open: openLoginSidebar, close: closeLoginSidebar } = useSidebarLogin()
const { sidebarWishlistDisplayStatus, open: openWishlistSidebar, close: closeWishlistSidebar } = useSidebarWishlist()
const { isLoggedIn } = useUser()
const appConfig = useAppConfig()
const { t } = useI18n()
const { open } = useMainNavigation()

getWishlistProducts()
</script>

<template>
  <header class="c-app-header relative z-40">
    <AppTopbar />
    <div class="bg-white border-b border-secondary-light flex relative z-10">
      <div class="flex-1 container big flex gap-x-5 items-center py-5 md:py-0 xl:pl-0 xl:pr-4">
        <div class="hidden md:flex flex-1 max-w-[300px] -ml-4 xl:ml-0">
          <button
            role="button"
            class="flex size-[70px] md-lg:size-[100px] bg-primary items-center justify-center text-white"
            :title="t('components.base.navigationItem.label')"
            @click="open">
            <BaseIcon
              name="bars"
              class="size-[70px] md-lg:size-[100px]" />
          </button>
        </div>
        <NuxtLink
          to="/"
          :title="appConfig.name"
          class="w-full flex-1 md:max-w-[300px] hover:text-inherit !text-current">
          <BaseLogo
            class="w-fit"
            logomark-class="text-primary w-9 md-lg:w-12"
            logo-class="w-28 md-lg:w-36" />
        </NuxtLink>
        <div class="hidden md:flex w-full max-w-[660px]">
          <div class="w-full mx-auto max-w-[640px]">
            <AppSearch instance-key="search-autocomplete-desktop" />
          </div>
        </div>

        <div class="w-full md-lg:max-w-[115px] text-[10px] leading-tight hidden justify-center md-lg:flex">
          <NuxtLink to="/faq">
            FAQ & Hilfe
          </NuxtLink>
        </div>

        <div class="flex-1 flex items-center justify-end md:justify-start gap-x-6 md:gap-x-8">
          <AppWidgetIcon
            icon="user"
            :label="t('components.app.header.user.label')"
            @click="openLoginSidebar" />
          <Teleport to="#teleports">
            <AppSidebar
              class="login-sidebar"
              position="right"
              :show="sidebarLoginDisplayStatus"
              close-position="inside"
              @close-sidebar="closeLoginSidebar">
              <template #default>
                <BaseLoginForm v-if="!isLoggedIn" />
                <BaseAccountMenu v-else />
              </template>
            </AppSidebar>
          </Teleport>
          <AppWidgetIcon
            icon="hearth-outline"
            :badge-count="wishlistProducts.length"
            :label="t('components.app.header.wishlist.label')"
            @click="openWishlistSidebar" />
          <Teleport to="#teleports">
            <AppSidebar
              class="wishlist-sidebar"
              position="right"
              close-position="inside"
              :show="sidebarWishlistDisplayStatus"
              @close-sidebar="closeWishlistSidebar">
              <template #default>
                <BaseSidebarWishlist :wishlist-products="wishlistProducts" />
              </template>
            </AppSidebar>
          </Teleport>
          <AppWidgetIcon
            icon="cart"
            :badge-count="count"
            :label="t('components.app.header.cart.label')"
            @click="openCartSidebar" />
          <Teleport to="#teleports">
            <AppSidebar
              class="cart-sidebar"
              position="right"
              close-position="inside"
              :show="sidebarCartDisplayStatus"
              @close-sidebar="closeCartSidebar">
              <template #default>
                <BaseSidebarCart />
              </template>
            </AppSidebar>
          </Teleport>
        </div>
      </div>
    </div>

    <div class="bg-gray-100 flex md:hidden py-[1px]">
      <button
        role="button"
        class="w-[70px] h-[50px] bg-white flex justify-center"
        :title="t('components.base.navigationItem.label')"
        @click="open">
        <BaseIcon
          name="bars"
          class="w-[50px] h-[50px]" />
      </button>
      <AppSearch
        class="flex-1 p-1"
        instance-key="search-autocomplete-mobile" />
    </div>

    <div class="hidden container justify-center md:flex z-[1]">
      <AppTopnavigation class="py-2 px-5 border-l border-b border-r rounded-bl-[6px] rounded-br-[6px] border-secondary-light overflow-x-hidden" />
    </div>
  </header>
</template>

<style scoped>

</style>
