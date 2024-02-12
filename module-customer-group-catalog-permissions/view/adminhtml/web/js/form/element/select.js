define([
    'Magento_Ui/js/form/element/select'
], function (Select) {
    'use strict';

    return Select.extend({
        webConfigUrlIdentifier: 'web_config_url',

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .prepareTooltip();

            return this;
        },

        /**
         * Prepare tooltip
         */
        prepareTooltip: function () {
            this.tooltip.link = this.source.data['aw_cgcp_cms_page_permissions'][this.webConfigUrlIdentifier];
        }
    });
});
