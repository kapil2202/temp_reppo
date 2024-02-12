
define([
    'Magento_Catalog/js/components/new-category'
], function (Category) {
    'use strict';

    return Category.extend({

        categoryIds: [],

        /**
         * {@inheritdoc}
         */
        initConfig: function (config) {
            this._super()
                .fillCategoryIds();
        },

        /**
         * Fill category ids
         */
        fillCategoryIds: function () {
            var me = this;

            this.cacheOptions.plain.forEach(function (option) {
                me.categoryIds.push(option.value);
            });
        },

        /**
         * Toggle select all categories
         *
         * @param {Object} itemData
         * @param {Event} event
         */
        toggleAllCategories: function (itemData, event) {
            var isChecked = event.currentTarget.checked;

            if (isChecked) {
                this.value(this.categoryIds);
            } else {
                this.value([]);
            }
        }
    });
});
