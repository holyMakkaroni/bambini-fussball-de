import template from './sw-category-filter-properties.html.twig';

/**
 * @package inventory
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
  template,

  inject: ['acl'],

  props: {
    isLoading: {
      type: Boolean,
      required: true,
    },
  },

  computed: {
    category() {
      return Shopware.State.get('swCategoryDetail').category;
    },
  },
};
