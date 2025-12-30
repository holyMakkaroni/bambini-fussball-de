<script setup lang="ts">
const { t } = useI18n()
const { updateEmail } = useUser()

const emit = defineEmits(['email-changed-success'])

const securityCheck = ref<{ password: string }>({
  password: ''
})

const newEmail = ref<{ email: string, emailConfirmation: string}>({
  email: '',
  emailConfirmation: ''
})

const invokeUpdateEmail = async () => {
  await updateEmail({
    email: newEmail.value?.email ?? '',
    emailConfirmation: newEmail.value?.emailConfirmation ?? '',
    password: securityCheck.value.password ?? ''
  })

  newEmail.value.email = ''
  newEmail.value.emailConfirmation = ''
  securityCheck.value.password = ''

  emit('email-changed-success')
}
</script>

<template>
  <form
    class="c-base-change-email-form"
    @submit.prevent="invokeUpdateEmail">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <FormInput
        id="newEmail"
        v-model="newEmail.email"
        name="newEmail"
        type="email"
        :label="t('components.app.general.labels.newEmail')"
        required />
      <FormInput
        id="emailConfirmation"
        v-model="newEmail.emailConfirmation"
        name="emailConfirmation"
        type="email"
        :label="t('components.app.general.labels.newEmailConfirmation')"
        required />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 mb-10">
      <FormInput
        id="password"
        v-model="securityCheck.password"
        name="securityCheckPassword"
        type="password"
        :label="t('components.app.general.labels.password')"
        required />
    </div>
    <BaseButton
      outline
      variant="primary"
      type="submit">
      {{ t('components.app.general.labels.saveChange') }}
    </BaseButton>
  </form>
</template>

<style scoped>

</style>
