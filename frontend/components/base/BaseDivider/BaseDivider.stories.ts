import type { Meta, StoryObj } from '@storybook/vue3'

import BaseDivider from './BaseDivider.vue'

const meta = {
  title: 'Design System/Base/BaseDivider',
  component: BaseDivider,
  tags: ['autodocs'],
  argTypes: {
    variant: {
      control: 'select'
    }
  }
} satisfies Meta<typeof BaseDivider>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: {
    variant: 'solid'
  }
}
