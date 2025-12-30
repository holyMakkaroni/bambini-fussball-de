import type { Meta, StoryObj } from '@storybook/vue3'

import BaseBorderedCard from './BaseBorderedCard.vue'

const meta = {
  title: 'Design System/Base/BaseBorderedCard',
  component: BaseBorderedCard,
  tags: ['autodocs']
} satisfies Meta<typeof BaseBorderedCard>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: {}
}
