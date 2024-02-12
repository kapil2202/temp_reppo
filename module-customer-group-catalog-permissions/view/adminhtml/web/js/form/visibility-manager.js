define([
    'Magento_Ui/js/form/components/html',
    'uiRegistry'
], function (Html, registry) {
    'use strict';

    return Html.extend({
        visibilityCombinations: {
            111: false,
            112: '.hidden_add_to_cart_message',
            121: '.hidden_price_message',
            122: '.hidden_price_message',
            211: false,
            212: false,
            221: false,
            222: false
        },
        inputNames: [
            '.hidden_add_to_cart_message',
            '.hidden_price_message'
        ],

        /**
         * Show needed input
         *
         * @param {String} rowPath
         */
        showNeededInput: function (rowPath) {
            var neededInputName = this.getInputNameToDisplay(rowPath);

            this.hideAll(rowPath);
            if (neededInputName) {
                registry.get(rowPath + neededInputName).show();
            }
        },

        /**
         * Retrieve needed input name to display
         *
         * @param {String} rowPath
         * @returns {String}|{Boolean}
         */
        getInputNameToDisplay: function (rowPath) {
            var viewMode = registry.get(rowPath + '.view_mode'),
                priceMode = registry.get(rowPath + '.price_mode'),
                checkoutMode = registry.get(rowPath + '.checkout_mode'),
                combinedKey = '';

            if (!(viewMode && priceMode && checkoutMode)) {
                return false;
            }
            combinedKey = viewMode.value() + priceMode.value() + checkoutMode.value();

            return this.visibilityCombinations[combinedKey];
        },

        /**
         * Hide all inputs
         *
         * @param {String} rowPath
         */
        hideAll: function (rowPath) {
            this.inputNames.forEach(function (inpitName) {
                var input = registry.get(rowPath + inpitName);
                if (input) {
                    input.hide();
                }
            });
        }
    });
});
