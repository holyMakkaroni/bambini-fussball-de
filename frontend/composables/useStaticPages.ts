import type { StaticPages } from '~/types/components/base'

export const useStaticPages = (group: string) => {
  const { t } = useI18n()

  const links : StaticPages = {
    account: [
      {
        name: t('components.base.accountMenu.overview.name'),
        description: t('components.base.accountMenu.overview.description'),
        url: 'my-account',
        actionLabel: t('components.base.accountMenu.overview.actionLabel'),
        showOverview: false
      },
      {
        name: t('components.base.accountMenu.orders.name'),
        description: t('components.base.accountMenu.orders.description'),
        url: 'my-account-orders',
        actionLabel: t('components.base.accountMenu.orders.actionLabel'),
        showOverview: true
      },
      {
        name: t('components.base.accountMenu.profile.name'),
        description: t('components.base.accountMenu.profile.description'),
        url: 'my-account-profile',
        actionLabel: t('components.base.accountMenu.profile.actionLabel'),
        showOverview: true
      },
      {
        name: t('components.base.accountMenu.addresses.name'),
        description: t('components.base.accountMenu.addresses.description'),
        url: 'my-account-addresses',
        actionLabel: t('components.base.accountMenu.addresses.actionLabel'),
        showOverview: true
      },
      {
        name: t('components.base.accountMenu.paymentMethods.name'),
        description: t('components.base.accountMenu.paymentMethods.description'),
        url: 'my-account-payment-methods',
        actionLabel: t('components.base.accountMenu.paymentMethods.actionLabel'),
        showOverview: true
      },
      {
        name: t('components.base.accountMenu.wishlist.name'),
        description: t('components.base.accountMenu.wishlist.description'),
        url: 'my-account-wishlist',
        actionLabel: t('components.base.accountMenu.wishlist.actionLabel'),
        showOverview: true,
        openWishlist: true
      },
      {
        name: t('components.base.accountMenu.myReviews.name'),
        description: t('components.base.accountMenu.myReviews.description'),
        url: 'my-account-reviews',
        actionLabel: t('components.base.accountMenu.myReviews.actionLabel'),
        showOverview: true
      }
    ]
  }

  return links[group]
}
