define([
    'jquery',
    'underscore',
    'Aheadworks_CustGroupCatPermissions/js/bulk/wizard/abstract-step',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                categoryPermissionsForm:                '${ $.categoryPermissionsForm }',
                categoryPermissionsFormDataProvider:    '${ $.categoryPermissionsFormDataProvider }',
                productPermissionsForm:                 '${ $.productPermissionsForm }',
                productPermissionsFormDataProvider:     '${ $.productPermissionsFormDataProvider }',
                cmsPagePermissionsForm:                 '${ $.cmsPagePermissionsForm }',
                cmsPagePermissionsFormDataProvider:     '${ $.cmsPagePermissionsFormDataProvider }'
            }
        },
        nextLabelText: $t('Apply'),

        /**
         * {@inheritdoc}
         */
        render: function (wizard) {
            var entityType = wizard.data.entityType;

            this._updateComponentsVisibility(entityType);
        },

        /**
         * {@inheritdoc}
         */
        force: function (wizard) {
            var entityType = wizard.data.entityType,
                permissionsForm = this._getPermissionsForm(entityType),
                permissionsFormDataProvider = this._getPermissionsFormDataProvider(entityType);

            wizard.cleanErrorNotificationMessage();
            this._validatePermissionsForm(permissionsForm);
            if (this._isPermissionsFormValid(permissionsFormDataProvider)) {
                _.extend(wizard.data, this._getPermissionsDataToApply(wizard));
                this._applyPermissions(wizard);
            } else {
                wizard.setNotificationMessage(
                    $t('Please, double-check the permissions: Some of them overlap each other.'),
                    true
                );
            }
        },

        /**
         * Retrieve permissions form of specified entity type
         *
         * @param {String} entityType
         * @returns {Object}
         * @private
         */
        _getPermissionsForm: function (entityType) {
            var permissionsForm;

            if (entityType === 'category') {
                permissionsForm = this.categoryPermissionsForm();
            } else if (entityType === 'product') {
                permissionsForm = this.productPermissionsForm();
            } else if (entityType === 'cms_page') {
                permissionsForm = this.cmsPagePermissionsForm();
            }
            return permissionsForm;
        },

        /**
         * Retrieve permissions form data provider of specified entity type
         *
         * @param {String} entityType
         * @returns {Object}
         * @private
         */
        _getPermissionsFormDataProvider: function (entityType) {
            var permissionsFormDataProvider;

            if (entityType === 'category') {
                permissionsFormDataProvider = this.categoryPermissionsFormDataProvider();
            } else if (entityType === 'product') {
                permissionsFormDataProvider = this.productPermissionsFormDataProvider();
            } else if (entityType === 'cms_page') {
                permissionsFormDataProvider = this.cmsPagePermissionsFormDataProvider();
            }
            return permissionsFormDataProvider;
        },

        /**
         * Validate form with entity type permissions to apply
         *
         * @param {Object} permissionsForm
         * @private
         */
        _validatePermissionsForm: function (permissionsForm) {
            if (_.isObject(permissionsForm)) {
                permissionsForm.validate();
            }
        },

        /**
         * Check if form with entity type permissions is valid
         *
         * @param {Object} permissionsFormDataProvider
         * @returns {boolean}
         * @private
         */
        _isPermissionsFormValid: function (permissionsFormDataProvider) {
            var isPermissionsFormValid = false;

            if (_.isObject(permissionsFormDataProvider)) {
                if (!_.isNull(permissionsFormDataProvider.params.invalid)
                    && !_.isUndefined(permissionsFormDataProvider.params.invalid)
                ) {
                    isPermissionsFormValid = !permissionsFormDataProvider.params.invalid;
                }
            }
            return isPermissionsFormValid;
        },

        /**
         * Apply permissions configured in the wizard
         * @private
         */
        _applyPermissions: function (wizard) {
            var self = this;

            $('body').trigger('processStart');
            $.ajax({
                url: this.applyPermissionsUrl,
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
        },

        /**
         * Retrieve permissions data to apply
         *
         * @param wizard
         * @returns {Object}
         * @private
         */
        _getPermissionsDataToApply : function (wizard) {
            var permissionsData = {},
                permissionsFormDataProvider = this._getPermissionsFormDataProvider(wizard.data.entityType);

            if (_.isObject(permissionsFormDataProvider)) {
                permissionsData = permissionsFormDataProvider.data;
            }
            return permissionsData;
        }
    });
});
