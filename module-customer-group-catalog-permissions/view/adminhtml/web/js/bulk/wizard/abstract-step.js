define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'underscore',
    'uiRegistry'
], function ($, Component, alert, confirm, _, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            notificationMessage: {
                text: null,
                error: null
            },
            componentsSwitcherConfig: {}
        },

        /**
         * Render step
         *
         * @param {Object} wizard
         */
        render: function (wizard) {
            this.wizard = wizard;
        },

        /**
         * Back action
         */
        back: function () {},

        /**
         * Next action
         *
         * @param {Object} wizard
         */
        force: function (wizard) {},

        /**
         * Ajax request error handler
         *
         * @param {Object} response
         */
        onError: function (response) {
            alert({content: response.error});
        },

        /**
         * Ajax request success handler
         *
         * @param {Object} response
         */
        onSuccess: function (response) {
            var modalOptions = {
                autoOpen: true,
                responsive: true,
                content: response.message,
                actions: {
                    always: function(){
                        location.reload();
                    }
                },
                buttons: [{
                    text: $.mage.__('OK'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            };

            confirm(modalOptions);
        },

        /**
         * Update components visibility according to selected entity type
         *
         * @param {String} entityType
         * @private
         */
        _updateComponentsVisibility: function (entityType) {
            var visibilityConfig = this.componentsSwitcherConfig[entityType];

            if (_.isArray(visibilityConfig)) {
                visibilityConfig.forEach(
                    function (item) {
                        var target = registry.get(item.target);
                        if (_.isObject(target)) {
                            target.visible(item.visible);
                        }
                    }
                );
            }
        }
    });
});
