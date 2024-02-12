define([
    'Magento_Ui/js/form/components/fieldset',
    'uiRegistry'
], function (Fieldset, registry) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            hiddenComponentName: '${ $.name }.permissions_changed'
        },

        /**
         * @inheritdoc
         */
        onChildrenUpdate: function (hasChanged) {
            var hiddenComponent = registry.get(this.hiddenComponentName);

            this._super();
            if (hiddenComponent) {
                hiddenComponent.value(this.changed() ? 1 : 0);
            }
        }
    });
});
