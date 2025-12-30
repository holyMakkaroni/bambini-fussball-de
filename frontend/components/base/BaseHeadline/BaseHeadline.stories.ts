import type { Meta, StoryObj } from '@storybook/vue3'
import BaseHeadline from './BaseHeadline.vue'
import { BaseHeadlineData } from '~/.storybook/data/BaseHeadline'

const meta = {
  title: 'Design System/Base/BaseHeadline',
  component: BaseHeadline,
  tags: ['autodocs'],
  argTypes: {
    tag: {
      control: 'select'
    },
    primary: {
      control: 'boolean'
    }
  },
  args: {}
} satisfies Meta<typeof BaseHeadline>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: BaseHeadlineData
}
