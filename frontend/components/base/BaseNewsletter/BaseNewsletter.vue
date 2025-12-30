<script setup lang="ts">
import Deer from './assets/illustrations/deer.svg?component'
</script>

<template>
  <div class="c-base-newsletter bg-primary overflow-hidden">
    <div class="container">
      <Deer class="absolute left-0 -top-5 opacity-30" />
      <div class="md:max-w-lg md:ml-auto md:mr-[15%]">
        <div class="py-14 flex flex-col text-white">
          <div class="text-2xl md:text-3xl font-semibold mb-2 md:mb-1 leading-7">
            Der LivingActive Newsletter
          </div>
          <div class="text-xl md:text-2xl leading-6">
            Jetzt abonnieren und keine Aktionen  & Angebote mehr verpassen!
          </div>
          <BaseButton
            variant="white"
            href="#"
            outline
            title="Zur Anmeldung"
            class="mt-5">
            Zur Anmeldung
          </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
