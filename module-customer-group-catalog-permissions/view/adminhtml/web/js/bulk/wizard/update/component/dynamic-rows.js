define([
    'underscore',
    'Aheadworks_CustGroupCatPermissions/js/form/component/dynamic-rows',
    'mage/translate'
], function (_, DynamicRows, $t) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            noPermissionsErrorMessage: $t('The configuration can\'t be applied because you didn\'t set any permissions')
        },

        /**
         * @inheritdoc
         */
        validateRows: function () {
            if (this.isAtLeastOneRowCreated()) {
                return this._super();
            } else {
                throw new Error(this.noPermissionsErrorMessage);
            }
        },

        /**
         * Check if at least one row with permissions has been set up
         *
         * @returns {boolean}
         */
        isAtLeastOneRowCreated: function () {
            return (_.size(this.getChildItems()) > 0);
        }
    });
});
