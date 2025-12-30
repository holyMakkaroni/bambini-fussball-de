import template from './sw-category-filter-properties-form.html.twig';

const { Component, Context } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;
const { mapState, mapGetters } = Component.getComponentHelper();

Shopware.Component.register('sw-category-filter-properties-form', {
  template,

  inject: ['repositoryFactory', 'acl'],

  props: {
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    isAssociation: {
      type: Boolean,
      required: false,
      // eslint-disable-next-line vue/no-boolean-default
      default: true,
    },
    showInheritanceSwitcher: {
      type: Boolean,
      required: false,
      // eslint-disable-next-line vue/no-boolean-default
      default: true,
    },
  },

  data() {
    return {
      groupIds: [],
      properties: [],
      categoryProperties: null,
      isPropertiesLoading: false,
      searchTerm: null,
      showAddPropertiesModal: false,
      newProperties: [],
      propertiesAvailable: true,
    };
  },

  computed: {
    propertyGroupRepository() {
      return this.repositoryFactory.create('property_group');
    },


    propertyOptionRepository() {
      return this.repositoryFactory.create('property_group_option');
    },

    propertyGroupCriteria() {
      const criteria = new Criteria(1, 10);

      criteria.addSorting(
        Criteria.sort('name', 'ASC', false),
      );
      criteria.addFilter(
        Criteria.equalsAny('id', this.groupIds),
      );

      if (this.searchTerm) {
        criteria.setTerm(this.searchTerm);
      }

      const optionIds = this.categoryProperties.getIds();

      criteria.getAssociation('options').addFilter(Criteria.equalsAny('id', optionIds));
      criteria.addFilter(Criteria.equalsAny('options.id', optionIds));

      return criteria;
    },

    propertyColumns() {
      return [
        {
          property: 'name',
          label: 'sw-product.properties.columnProperty',
          sortable: false,
          routerLink: 'sw.property.detail',
        },
        {
          property: 'values',
          label: 'sw-product.properties.columnValue',
          sortable: false,
        },
      ];
    },

    category() {
      return Shopware.State.get('swCategoryDetail').category;
    },

    assetFilter() {
      return Shopware.Filter.getByName('asset');
    },

    categoryHasProperties() {
      return this.category?.id && this.groupIds.length > 0;
    },
  },

  watch: {
    categoryProperties: {
      immediate: true,
      handler(newValue) {
        if (!newValue) {
          return;
        }
        this.getGroupIds();
        this.getProperties();
        this.setCustomFieldProperties()
      },
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.checkIfPropertiesExists();
      this.fetchCategoryProperties();
    },

    fetchCategoryProperties() {
      const propertyIds = this.category?.customFields?.properties || [];

      if(!propertyIds.length > 0) {
        this.categoryProperties = [];
        return propertyIds
      }

      this.isPropertiesLoading = true;

      const propertyOptionCriteria = new Criteria(1, 10);

      propertyOptionCriteria.addSorting(
        Criteria.sort('name', 'ASC', false),
      );
      propertyOptionCriteria.addFilter(
        Criteria.equalsAny('id', propertyIds),
      );

      return this.propertyOptionRepository.search(propertyOptionCriteria, Context.api)
        .then((properties) => {
          this.categoryProperties = properties;
        })
        .catch(() => {
          this.categoryProperties = [];
        })
        .finally(() => {
          this.isPropertiesLoading = false;
        });
    },

    getGroupIds() {
      if (!this.category?.id) {
        return;
      }

      this.groupIds = this.categoryProperties.reduce((accumulator, { groupId }) => {
        if (accumulator.indexOf(groupId) < 0) {
          accumulator.push(groupId);
        }

        return accumulator;
      }, []);
    },

    getProperties() {
      if (!this.category?.id || this.groupIds.length <= 0) {
        this.properties = [];
        this.searchTerm = null;
        return Promise.resolve();
      }

      this.isPropertiesLoading = true;
      return this.propertyGroupRepository.search(this.propertyGroupCriteria, Context.api)
        .then((properties) => {
          this.properties = properties;
        })
        .catch(() => {
          this.properties = [];
        })
        .finally(() => {
          this.isPropertiesLoading = false;
        });
    },

    onDeletePropertyValue(propertyValue) {
      this.categoryProperties.remove(propertyValue.id);
    },

    onDeleteProperty(property) {
      this.$refs.entityListing.deleteId = null;

      this.$nextTick(() => {
        this.categoryProperties
          .filter(({ groupId }) => {
            return groupId === property.id;
          })
          .forEach(({ id }) => {
            this.categoryProperties.remove(id);
          });

        this.$refs.entityListing.resetSelection();
      });
    },

    onDeleteProperties() {
      this.$refs.entityListing.showBulkDeleteModal = false;

      this.$nextTick(() => {
        const properties = { ...this.$refs.entityListing.selection };

        Object.values(properties).forEach((property) => {
          property.options.forEach((value) => {
            this.categoryProperties.remove(value.id);
          });
        });
        this.$refs.entityListing.resetSelection();
      });
    },

    onChangeSearchTerm(searchTerm) {
      this.searchTerm = searchTerm;
      return this.getProperties();
    },

    turnOnAddPropertiesModal() {
      if (!this.propertiesAvailable) {
        this.$router.push({ name: 'sw.property.index' });
      } else {
        this.updateNewProperties();
        this.showAddPropertiesModal = true;
      }
    },

    turnOffAddPropertiesModal() {
      this.showAddPropertiesModal = false;
      this.updateNewProperties();
    },

    updateNewProperties() {
      this.newProperties = new EntityCollection(
        this.categoryProperties.source,
        this.categoryProperties.entity,
        this.categoryProperties.context,
        Criteria.fromCriteria(this.categoryProperties.criteria),
        this.categoryProperties,
        this.categoryProperties.total,
        this.categoryProperties.aggregations,
      );
    },

    onSaveAddPropertiesModal(newProperties, callbackUpdateCurrentValues) {
      this.turnOffAddPropertiesModal();

      if (newProperties.length <= 0) {
        return;
      }

      this.categoryProperties = newProperties
    },

    closeAddPropertiesModal() {
      this.showAddPropertiesModal = false;
      this.updateNewProperties();
    },

    onCancelAddPropertiesModal() {
      this.closeAddPropertiesModal();
    },

    setCustomFieldProperties() {
      if(this.categoryProperties.length > 0) {
        const optionIds = this.categoryProperties.getIds();

        this.category.customFields.properties = optionIds
      } else {
        this.category.customFields = {}
        this.category.customFields.properties = []
      }
    },

    checkIfPropertiesExists() {
      this.propertyOptionRepository.search(new Criteria(1, 1)).then((res) => {
        this.propertiesAvailable = res.total > 0;
      });
    },
  }
});