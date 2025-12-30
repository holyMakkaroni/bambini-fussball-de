import type { Meta, StoryObj } from '@storybook/vue3'

import BaseButton from './BaseButton.vue'

const meta = {
  title: 'Design System/Base/BaseButton',
  component: BaseButton,
  tags: ['autodocs'],
  argTypes: {
    variant: {
      control: 'select'
    },
    size: {
      control: 'select'
    },
    icon: {
      control: 'select'
    },
    iconPosition: {
      control: 'select'
    },
    target: {
      control: 'select'
    }
  }
} satisfies Meta<typeof BaseButton>

export default meta
type Story = StoryObj<typeof meta>

export const Primary: Story = {
  args: {
    variant: 'primary',
    size: 'normal'
  }
}

export const Secondary: Story = {
  args: {
    variant: 'secondary',
    size: 'normal'
  }
}
