import './sw-property-option-detail.scss'
import template from './sw-property-option-detail.html.twig';

const { Component, Mixin } = Shopware;

Component.override('sw-property-option-detail', {
    template,

    inject: [
        'acl'
    ],

    mixins: [
        Mixin.getByName('lia-slugify'),
    ],

    watch: {
        "currentOption.customFields.optionValue": function (newOptionName, oldOptionName) {
            this.currentOption.customFields.optionValue = this.slugify(newOptionName);
        }
    },

    computed: {
        shouldInitialiseCustomFields: function () {
            return this.currentOption.customFields === null || typeof this.currentOption.customFields !== 'object';
        },
    },

    created() {
        if (this.shouldInitialiseCustomFields) {
            this.initialiseCustomFields();
        }

        if (!this.currentOption.customFields.hasOwnProperty('optionValue')) {
            this.currentOption.customFields = {
                optionValue: this.slugify(this.currentOption.name),
            }
        }
    },

    updated() {
        this.currentOption.customFields = this.currentOption.customFields || {};
    },

    methods: {
        initialiseCustomFields() {
            this.currentOption.customFields = {};
        }
    }
});
