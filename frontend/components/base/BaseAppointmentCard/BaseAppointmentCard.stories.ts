import type { Meta, StoryObj } from '@storybook/vue3'

import BaseAppointmentCard from './BaseAppointmentCard.vue'
import { BaseAppointmentCardData } from '~/.storybook/data/BaseAppointmentCard'

const meta = {
  title: 'Design System/Base/BaseAppointmentCard',
  component: BaseAppointmentCard,
  tags: ['autodocs'],
  parameters: {
    backgrounds: {
      default: 'secondary-light'
    }
  }
} satisfies Meta<typeof BaseAppointmentCard>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: BaseAppointmentCardData
}
