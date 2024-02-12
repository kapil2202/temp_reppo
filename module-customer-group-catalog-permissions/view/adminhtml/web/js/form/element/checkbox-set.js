define([
    'Magento_Ui/js/form/element/checkbox-set',
    'uiRegistry'
], function (CheckboxSet, registry) {
    'use strict';

    return CheckboxSet.extend({
        defaults: {
            hideValue: '2',
            rowPath: '${ $.parentName }',
            visibilityManagerName: '',
            visibilityManager: false
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .visibilityManager = registry.get(this.visibilityManagerName);

            return this;
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
        getInitialValue: function () {
            var value = this._super();

            return value ? value : this.default;
        },

        /**
         * @inheritdoc
         */
        destroy: function (skipUpdate) {
            this._super();

            registry.remove(this.name + '_switcher');
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            this._super();
            this.visibilityManager.showNeededInput(this.rowPath);
        },

        /**
         * Enable element
         */
        enable: function () {
            this.visibilityManager.showNeededInput(this.rowPath);
            this.isDisabled(false);
        },

        /**
         * Disable element and set value to "Hide"
         */
        disableAndValueToHide: function () {
            this.value(this.hideValue);
            this.isDisabled(true);
        }
    });
});
