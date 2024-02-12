define([
    'Magento_Ui/js/dynamic-rows/record'
], function (Record) {
    'use strict';

    return Record.extend({

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe('error');

            return this;
        }
    });
});
