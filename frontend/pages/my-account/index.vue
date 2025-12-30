<script setup lang="ts">
import BaseAccountSubHeader from '~/components/base/BaseAccountSubHeader/BaseAccountSubHeader.vue'

const { user } = useUser()
const { t } = useI18n()
const sidebarLinks = useStaticPages('account')
const localePath = useLocalePath()

const newsletterAgree = ref(false)

const cards = computed(() => {
  return sidebarLinks.filter(link => link.showOverview)
})
</script>

<template>
  <div class="type-my-account-index">
    <BaseAccountHeader
      :title="t('pages.myAccount.index.headline', {
        firstName: user?.firstName,
        lastName: user?.lastName
      })"
      :description="t('pages.myAccount.index.description')" />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <BaseCard
        v-for="(card, index) in cards"
        :key="index"
        :title="card.name"
        :description="card.description">
        <template #default>
          <BaseButton
            variant="primary"
            outline
            :title="card.name"
            :href="localePath(card.url)">
            {{ card.actionLabel }}
          </BaseButton>
        </template>
      </BaseCard>
    </div>

    <div class="flex flex-col">
      <BaseAccountSubHeader :title="t('pages.myAccount.index.newsletter.headline')" />
      <div class="max-w-[860px]">
        <FormCheckbox
          id="newsletterAgree"
          v-model="newsletterAgree"
          :value="newsletterAgree"
          :label="t('pages.myAccount.index.newsletter.label')" />
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
