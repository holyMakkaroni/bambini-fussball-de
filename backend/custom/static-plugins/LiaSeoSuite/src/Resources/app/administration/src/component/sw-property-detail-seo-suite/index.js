import template from './sw-property-detail-seo-suite.html.twig';
const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('lia-slugify'),
    ],

    props: {
        propertyGroup: {
            type: Object,
            required: true,
            default() {
                return {};
            },
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    data() {
        return {
            filterName: "",
            filterPriority: 0
        }
    },

    watch: {
        "propertyGroup.customFields.filterName": function (newFilterName, oldFilterName) {
            this.propertyGroup.customFields.filterName = this.slugify(newFilterName);
        }
    },

    computed: {
        propertyGroupRepository() {
            return this.repositoryFactory.create('property_group');
        },

        propertyGroupCriteria() {
            const criteria = new Criteria();

            criteria.setLimit(10);
            criteria.setTotalCountMode(1);
            if (this.propertyGroup.id) {
                criteria.addFilter(Criteria.not('and', [
                    Criteria.equals('id', this.propertyGroup.id)
                ]));
            }

            return criteria;
        },

        shouldInitialiseCustomFields: function () {
            return this.propertyGroup.customFields === null || typeof this.propertyGroup.customFields !== 'object';
        },
    },

    created() {
        if (this.shouldInitialiseCustomFields) {
            this.initialiseCustomFields();
        }

        if (!this.propertyGroup.customFields.hasOwnProperty('filterName')) {
            this.filterName = this.propertyGroup.name;

            this.propertyGroup.customFields = {
                filterName: this.filterName,
            }
        } else {
            this.filterName = this.propertyGroup.customFields.filterName;
            this.filterPriority = this.propertyGroup.customFields.filterPriority
        }
    },

    updated() {
        this.propertyGroup.customFields = this.propertyGroup.customFields || {};
    },

    methods: {
        initialiseCustomFields() {
            this.propertyGroup.customFields = {};
        }
    }
};
