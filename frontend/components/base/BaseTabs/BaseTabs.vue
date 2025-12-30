<script setup>
const props = defineProps({
  tabs: {
    type: Array,
    required: true
  },
  tabClass: {
    type: String,
    default: ''
  },
  tabHeadClass: {
    type: String,
    default: ''
  },
  contentClass: {
    type: String,
    default: ''
  },
  activeInitTab: {
    type: String,
    default: ''
  }
})

const activeTab = ref(props.activeInitTab || props.tabs[0]?.name)

const setActiveTab = (name) => {
  activeTab.value = name
}
</script>

<template>
  <div class="c-base-tabs flex flex-col">
    <div :class="[tabClass]">
      <div
        class="flex gap-x-8"
        :class="[tabHeadClass]">
        <div
          v-for="(tab, index) in tabs"
          :key="index">
          <slot
            :name="`tabHead-${tab.name}`"
            :active-tab="activeTab"
            :tab="tab"
            :set-active-tab="setActiveTab">
            <button
              class="transition-default text-sm font-semibold underline decoration-1 underline-offset-4 hover:decoration-current"
              :class="[{ 'decoration-black': tab.name === activeTab, 'decoration-transparent': tab.name !== activeTab }]"
              @click="setActiveTab(tab.name)">
              {{ tab.label }}
            </button>
          </slot>
        </div>
      </div>
    </div>

    <div :class="[contentClass]">
      <div
        v-for="(tab, index) in tabs"
        :key="index">
        <div v-show="tab.name === activeTab">
          <slot :name="tab.name" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
