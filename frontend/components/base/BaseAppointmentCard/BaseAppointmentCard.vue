<script setup lang="ts">
import type { BaseAppointmentCard } from '../../../types/components/base'

const props = defineProps<BaseAppointmentCard>()

const { t } = useI18n()

const { dayNumber, monthName, time } = useUtilities()
</script>

<template>
  <div class="c-base-appointment-card bg-white p-5 lg:p-10">
    <div class="flex flex-col text-primary">
      <div class="font-semibold text-4xl lg:text-6xl">
        {{ dayNumber(props.startAt) }}.
      </div>
      <div class="font-light text-xl">
        {{ monthName(props.startAt) }}
      </div>
      <div class="flex font-light text-xl">
        <div>
          {{ t('components.base.appointmentCard.time', {
            startAt: time(props.startAt),
            endAt: time(props.endAt)
          }) }}
        </div>
      </div>
    </div>
    <div class="flex flex-col mt-6">
      <div class="font-semibold text-xl lg:text-2xl">
        {{ title }}
      </div>
      <div
        v-if="description"
        class="text-wrap line-clamp-2 md:text-xl">
        {{ description }}
      </div>
    </div>
    <BaseButton
      v-if="url"
      :href="url"
      outline
      :title="t('components.base.appointmentCard.label')"
      class="mt-9">
      {{ t('components.base.appointmentCard.label') }}
    </BaseButton>
  </div>
</template>

<style scoped>

</style>
