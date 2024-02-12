define([
    'jquery',
    'Aheadworks_CustGroupCatPermissions/js/bulk/wizard/abstract-step',
    'mage/translate',
    'underscore',
    'uiRegistry'
], function ($, Component, $t, _, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            modules: {
                select: '${ $.selectName }'
            },
            predefinedEntityType: ''
        },

        /**
         * {@inheritdoc}
         */
        render: function (wizard) {
            if (this.isNeedToApplyPredefinedEntityType(this.predefinedEntityType)) {
                this.selectNextStep(wizard, this.predefinedEntityType);
            }
        },

        /**
         * Check if need to apply predefined entity type to skip current step of the wizard
         *
         * @param {String} predefinedEntityType
         * @returns {boolean}
         */
        isNeedToApplyPredefinedEntityType: function (predefinedEntityType) {
            var isNeedToApplyPredefinedEntityType = false;

            if (!_.isEmpty(predefinedEntityType)) {
                isNeedToApplyPredefinedEntityType = this.isEntityTypeValid(predefinedEntityType);
            }
            return isNeedToApplyPredefinedEntityType;
        },

        /**
         * Check if entity type value valid
         *
         * @param {String} entityType
         * @returns {boolean}
         */
        isEntityTypeValid: function (entityType) {
            var entityTypeOptions = this.select().options;
            var entityTypeIndex = _.findIndex(entityTypeOptions, function(item) {
                return (item.value === entityType);
            });

            return (entityTypeIndex >= 0);
        },

        /**
         * Move to the next step of the wizard
         *
         * @param {Object} wizard
         * @param {String} entityType
         */
        selectNextStep: function (wizard, entityType) {
            var stepWizardComponent = registry.get(this.appendTo);

            this.select().value(entityType);
            if (_.isObject(stepWizardComponent)) {
                stepWizardComponent.selectedStep(wizard.next());
            }
        },

        /**
         * {@inheritdoc}
         */
        force: function (wizard) {
            wizard.data.entityType = this.select().value();
            wizard.data.totalIds = 0;

            if (!wizard.data.entityType) {
                throw new Error($t('Please, select catalog element type for which you want to set permissions in bulk.'));
            }
        }
    });
});
