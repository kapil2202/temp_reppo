define([
    'jquery',
    'underscore',
    'Aheadworks_CustGroupCatPermissions/js/bulk/wizard/update/steps/select-entity-ids',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    return Component.extend({
        nextLabelText: $t('Delete'),

        /**
         * {@inheritdoc}
         */
        force: function (wizard) {
            var entityType = wizard.data.entityType;

            _.extend(wizard.data, this._getSelectedIdsDataForEntityType(entityType));
            this._deletePermissions(wizard);
        },

        /**
         * Apply permissions configured in the wizard
         * @private
         */
        _deletePermissions: function (wizard) {
            var self = this;

            $('body').trigger('processStart');
            $.ajax({
                url: this.deletePermissionsUrl,
                type: 'POST',
                dataType: 'json',
                data: wizard.data,

                /**
                 * Success callback
                 * @param {Object} response
                 * @returns {Boolean}
                 */
                success: function(response) {
                    if (response.error) {
                        self.onError(response);
                        return true;
                    } else {
                        self.onSuccess(response);
                    }
                    return false;
                },

                /**
                 * Complete callback
                 */
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        }
    });
});
