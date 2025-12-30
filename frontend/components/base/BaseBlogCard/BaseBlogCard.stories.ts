import type { Meta, StoryObj } from '@storybook/vue3'

import BaseBlogCard from './BaseBlogCard.vue'
import { BaseBlogCardData } from '~/.storybook/data/BaseBlogCard'

const meta = {
  title: 'Design System/Base/BaseBlogCard',
  component: BaseBlogCard,
  tags: ['autodocs']
} satisfies Meta<typeof BaseBlogCard>

export default meta
type Story = StoryObj<typeof meta>

export const Default: Story = {
  args: BaseBlogCardData
}
