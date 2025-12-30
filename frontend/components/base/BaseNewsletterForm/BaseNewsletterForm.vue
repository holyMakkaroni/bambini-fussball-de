<script setup lang="ts">
import type { BaseNewsletterForm } from '@/types/components/base'

const props = withDefaults(defineProps<BaseNewsletterForm>(), {
  headline_type: 'div'
})

const email = ref<string>('')
const agree = ref<boolean>(false)
const selectedInterests = ref<string[]>([])

const subscribe = async () => {
  if (!agree.value) {
    return
  }

  const { data } = await useFetch('/api/cleverreach/add-subscriber', {
    method: 'post',
    body: {
      email: email.value,
      listId: props.mailingListId,
      attributes: {
        interessen: selectedInterests.value.join(',')
      }
    }
  })
}
</script>

<template>
  <div
    v-if="mailingListId"
    class="bg-primary p-7 md:p-14 grid grid-cols-12 divide-y divide-white/25 md:divide-y-0 md:divide-x">
    <div class="col-span-12 md:col-span-6 pb-4 md:pb-0">
      <BaseHeadline
        v-if="title"
        :tag="headline_type || 'div'"
        custom-class="text-white text-3xl font-semibold"
        :title="title" />
      <div
        v-if="description"
        class="text-white text-xl">
        {{ description }}
      </div>
      <div
        v-if="interests?.length"
        class="flex flex-col mt-10">
        <div class="text-white text-xl">
          Bitte Interessengebiet auswählen:
        </div>
        <div class="text-xs text-white mt-2 flex flex-col md:flex-row md:flex-wrap space-y-2.5 md:space-y-0 md:space-x-5">
          <FormCheckbox
            v-for="(interest, index) in interests"
            :id="`${interest}_${index}`"
            :key="index"
            v-model="selectedInterests"
            :value="interest"
            :label="interest" />
        </div>
      </div>
    </div>
    <div class="col-span-12 md:col-span-6 pt-4 md:pt-0 md:pl-14">
      <div class="flex h-full items-center">
        <form
          class="flex-1 flex flex-col max-w-[400px]"
          @submit.prevent="subscribe">
          <FormInput
            id="email"
            v-model="email"
            type="email"
            class="max-w-[310px]"
            placeholder="Ihre E-Mail-Adresse"
            required />

          <div class="flex items-start text-xs text-white mt-5">
            <FormCheckbox
              id="newsletter-checkbox"
              v-model="agree"
              :value="agree"
              required>
              Ich habe die <a
                href="#"
                target="_blank"
                class="hover:text-white hover:decoration-white">Datenschutzhinweise</a> gelesen und akzeptiere diese. Eine Abmeldung vom Newsletter ist jederzeit möglich.
            </FormCheckbox>
          </div>

          <BaseButton
            class="mt-4 md:mt-8"
            outline
            size="normal"
            variant="white"
            title="Jetzt abonnieren">
            Jetzt abonnieren
          </BaseButton>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
