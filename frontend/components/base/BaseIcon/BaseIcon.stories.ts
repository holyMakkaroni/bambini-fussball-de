import type { Meta, StoryObj } from '@storybook/vue3'
import BaseIcon from './BaseIcon.vue'

const icons = Object.entries(import.meta.glob('~/assets/icons/**/*.svg', { query: '?raw' })).map(
  ([key]) => {
    return key.split('/').pop()!.split('.').shift()
  }
)

const meta = {
  title: 'Design System/Base/BaseIcon',
  component: BaseIcon,
  argTypes: {
    name: {
      description: 'Defines the icon',
      table: {
        category: 'Props'
      },
      control: 'select',
      options: icons
    }
  },
  args: {}
} satisfies Meta<typeof BaseIcon>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: {
    name: 'close'
  }
}
