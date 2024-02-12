define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'prototype'
], function ($, alert) {
    'use strict';

    $.widget('awcp.selectWithAlert', {
        options: {
            selectId: '',
            selectInitialValue: '',
            alertMessage: '',
            enabledValue: '1',
            disabledValue: '0'
        },

        /**
         * @private
         */
        _create: function () {
            this.saveInitialValue();
            this.bindHandlers();
        },

        /**
         * Save initial value of the select element
         */
        saveInitialValue: function () {
            this.selectInitialValue = this.getSelect().val();
        },

        /**
         * Retrieve select, connected to the current widget
         *
         * @returns {*|jQuery|HTMLElement}
         */
        getSelect: function() {
            return ($('#' + this.options.selectId));
        },

        /**
         * Adding necessary event handlers
         */
        bindHandlers: function() {
            var widget = this;
            this.getSelect().change(function() {
                if (widget.isNeedToShowAlert(this.value)) {
                    widget.showAlert();
                }
            });
        },

        /**
         * Check if need to show alert modal
         *
         * @param selectCurrentValue
         * @returns {boolean}
         */
        isNeedToShowAlert: function(selectCurrentValue) {
            return (
                (selectCurrentValue === this.options.enabledValue)
                && (selectCurrentValue !== this.selectInitialValue)
            );
        },

        /**
         * Show alert modal
         */
        showAlert: function() {
            alert({
                content: this.options.alertMessage,
                actions: {
                    always: function(){
                        this.onAlertShowing();
                    }.bind(this)
                }
            });
        },

        /**
         * Processing showing of the alert modal
         */
        onAlertShowing: function() {
            this.rollbackSelectToDisabledState();
            this.fireEventForElement(this.options.selectId, 'change');
        },

        /**
         * Reset select value to the disabled state
         */
        rollbackSelectToDisabledState: function() {
            this.getSelect().val(this.options.disabledValue);
        },

        /**
         * Fires event with exact name for the element with specified id
         *
         * @param elementId
         * @param eventName
         * @returns {boolean}
         */
        fireEventForElement: function(elementId, eventName) {
            var element = document.getElementById(elementId);
            if (document.createEventObject) {
                var eventObjectForIE = document.createEventObject();
                return element.fireEvent('on' + event, eventObjectForIE);
            } else {
                var eventObject = document.createEvent("HTMLEvents");
                eventObject.initEvent(eventName, true, true);
                return !element.dispatchEvent(eventObject);
            }
        }

    });

    return $.awcp.selectWithAlert;
});