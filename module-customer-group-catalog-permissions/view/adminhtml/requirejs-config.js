var config = {
    map: {
        '*': {
            awCpSelectWithAlert: 'Aheadworks_CustGroupCatPermissions/js/aw-cp-select-with-alert'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/lib/step-wizard': {
                'Aheadworks_CustGroupCatPermissions/js/lib/step-wizard-mixin': true
            }
        }
    }
};