define([
    'underscore',
    'Aheadworks_CustGroupCatPermissions/js/bulk/wizard/abstract-step',
    'mage/translate'
], function (_, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                selectCategoryIdsComponent:     '${ $.selectCategoryIdsComponentName }',
                selectProductIdsComponent:      '${ $.selectProductIdsComponentName }',
                selectCmsPageIdsComponent:      '${ $.selectCmsPageIdsComponentName }'
            }
        },

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
            var entityType = wizard.data.entityType;

            _.extend(wizard.data, this._getSelectedIdsDataForEntityType(entityType));
        },

        /**
         * Retrieve selected entity ids for specified entity type
         *
         * @param {String} entityType
         * @returns {Object}
         * @private
         */
        _getSelectedIdsDataForEntityType: function (entityType) {
            var idsData = {};
            
            if (entityType === 'category') {
                idsData = {
                    entityIds: this.selectCategoryIdsComponent().value(),
                    totalIds: this.selectCategoryIdsComponent().value().length
                };
            } else if (entityType === 'product') {
                idsData = this._prepareSelections(this.selectProductIdsComponent().getSelections());
            } else if (entityType === 'cms_page') {
                idsData = this._prepareSelections(this.selectCmsPageIdsComponent().getSelections());
            }

            if (!idsData.totalIds) {
                throw new Error($t('Please, specify catalog elements to continue.'));
            }

            return idsData;
        },

        /**
         * Prepare selections
         *
         * @param data
         * @returns {{Object}}
         * @private
         */
        _prepareSelections: function (data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                result = {
                    isGrid: true,
                    totalIds: data.total
                };

            result[itemsType] = data[itemsType];
            if (!result[itemsType].length) {
                result[itemsType] = false;
            }
            _.extend(result, data.params || {});

            return result;
        }
    });
});
