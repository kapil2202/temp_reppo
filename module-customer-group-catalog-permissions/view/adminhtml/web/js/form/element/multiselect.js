define([
    'underscore',
    'Magento_Ui/js/form/element/multiselect'
], function (_, Select) {
    'use strict';

    return Select.extend({

        /**
         * @inheritdoc
         */
        getInitialValue: function () {
            var value = this._super();

            return ( _.size(value) === 1 && !_.first(value)) ? [this.default] : value;
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            var value = this.value();

            if (_.contains(value, this.default) && _.size(value) !== 1) {
                this.value([this.default]);
                this.bubble('update', false);
            } else {
                this.bubble('update', this.hasChanged());
            }
            this.validate();
        }
    });
});
