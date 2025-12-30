<script setup lang="ts">
const { t } = useI18n()
const { updatePassword } = useCustomerPassword()

const emit = defineEmits(['password-changed-success'])

const securityCheck = ref<{ password: string }>({
  password: ''
})

const newPassword = ref<{ password: string, passwordConfirmation: string}>({
  password: '',
  passwordConfirmation: ''
})

const invokeUpdatePassword = async () => {
  await updatePassword({
    newPassword: newPassword.value.password ?? '',
    newPasswordConfirm: newPassword.value.passwordConfirmation ?? '',
    password: securityCheck.value.password ?? ''
  })

  newPassword.value.password = ''
  newPassword.value.passwordConfirmation = ''

  emit('password-changed-success')
}
</script>

<template>
  <form
    class="c-base-change-password-form"
    @submit.prevent="invokeUpdatePassword">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <FormInput
        id="password"
        v-model="newPassword.password"
        name="password"
        type="password"
        :label="t('components.app.general.labels.newPassword')"
        required />
      <FormInput
        id="passwordConfirmation"
        v-model="newPassword.passwordConfirmation"
        name="passwordConfirmation"
        type="password"
        :label="t('components.app.general.labels.newPasswordConfirmation')"
        required />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 mb-10">
      <FormInput
        id="currentPassword"
        v-model="securityCheck.password"
        name="currentPassword"
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
