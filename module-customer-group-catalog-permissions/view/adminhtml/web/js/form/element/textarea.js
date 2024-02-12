define([
    'Magento_Ui/js/form/element/textarea',
    'mageUtils'
], function (Textarea, utils) {
    'use strict';

    return Textarea.extend({
        defaults: {
            hiddenComponentName: '${ $.name }.permissions_changed'
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isDisabled: false
                });

            return this;
        },

        /**
         * @inheritdoc
         */
        toggleUseDefault: function (state) {
            this.isDisabled(state);
        },

        /**
         * @inheritdoc
         */
        userChanges: function () {
            this._super();
            this.bubble('updateDefault', true);
        },

        /**
         * Retrieve service input name
         *
         * @returns {String}
         */
        getServiceInputName: function () {
            var name = this.parentScope.split('.').slice(1);

            return utils.serializeName(name.join('.'))
        },

        /**
         * @inheritdoc
         */
        setInitialValue: function () {
            this._super()
                .isUseDefault(!this.value());

            return this;
        }
    });
});
