define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'mage/translate'
], function (_, DynamicRows, $t) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            errorMessage: $t('You already have a permission with this scope.'),
            allStoreViewsValue: '0',
            allGroupsValue: '32000',
            listens: {
                '${ $.provider }:data.validate': 'validateRows'
            }
        },

        /**
         * @inheritdoc
         */
        onChildrenUpdate: function () {
            this._super();
            this.validateRows();
        },

        /**
         * @inheritdoc
         */
        initHeader: function () {
            this._super();
            this.showCellLabels();
        },

        /**
         * @inheritdoc
         */
        initElement: function (elem) {
            this._super();
            elem.on({
                'updateDefault': this.onChildrenUpdateDefault.bind(this)
            });

            return this;
        },

        /**
         * On service click callback
         *
         * @param {Boolean} state
         */
        onChildrenUpdateDefault: function (state) {
            this.changed(state);
        },

        /**
         * Show cell labels
         */
        showCellLabels: function () {
            _.each(this.childTemplate.children, function (cell) {
                cell.config.labelVisible = true;
            });
        },

        /**
         * Rows data validation
         */
        validateRows: function () {
            var rowsIndexes = this.getInvalidRowIndexes(),
                isValid = _.isEmpty(rowsIndexes);

            this.clearErrors();
            if (!isValid) {
                this.source.set('params.invalid', true);
                this.displayErrorsForRows(rowsIndexes);
            }
        },

        /**
         * Retrieve invalid rows data indexes
         *
         * @returns {Array}
         */
        getInvalidRowIndexes: function () {
            var rowIndexes = [],
                rowsToProcess = this.getChildItems(),
                processedIndex,
                processedItem,
                me = this;

            _.each(rowsToProcess, function (item, index) {
                processedIndex = index;
                processedItem = item;

                _.each(rowsToProcess, function (item, index) {
                    if (index > processedIndex
                        && me.hasIntersectValues(processedItem, item)) {
                        rowIndexes.push(processedIndex, index);
                    }
                });
            });


            return _.uniq(rowIndexes);
        },

        /**
         * Check if objects have intersect values
         *
         * @param {Object} obj1
         * @param {Object} obj2
         * @returns {boolean}
         */
        hasIntersectValues: function (obj1, obj2) {
            if (!this.hasIntersectionByStores(obj1, obj2)) {
                return false;
            }

            return this.hasIntersectionByCustomerGroup(obj1, obj2);
        },

        /**
         * Check if objects have intersection by stores
         *
         * @param {Object} obj1
         * @param {Object} obj2
         * @returns {boolean}
         */
        hasIntersectionByStores: function (obj1, obj2) {
            if (!_.isEmpty(_.intersection(obj1.store_ids, obj2.store_ids))) {
                return true;
            }

            if (_.contains(obj1.store_ids, this.allStoreViewsValue)) {
                return true;
            }

            return _.contains(obj2.store_ids, this.allStoreViewsValue);
        },

        /**
         * Check if objects have intersection by customer groups
         *
         * @param {Object} obj1
         * @param {Object} obj2
         * @returns {boolean}
         */
        hasIntersectionByCustomerGroup: function (obj1, obj2) {
            if (!_.isEmpty(_.intersection(obj1.customer_group_ids, obj2.customer_group_ids))) {
                return true;
            }
            if (_.contains(obj1.customer_group_ids, this.allGroupsValue)) {
                return true;
            }

            return _.contains(obj2.customer_group_ids, this.allGroupsValue);
        },

        /**
         * Display errors
         *
         * @param {Array} rowIndexes
         */
        displayErrorsForRows: function (rowIndexes) {
            var me = this;

            _.each(this.elems(), function (elem, index) {
                if (_.contains(rowIndexes, index)) {
                    elem.error(me.errorMessage);
                }
            });
        },

        /**
         * Clear errors
         */
        clearErrors: function () {
            _.each(this.elems(), function (elem) {
                elem.error(null);
            });
        }
    });
});
