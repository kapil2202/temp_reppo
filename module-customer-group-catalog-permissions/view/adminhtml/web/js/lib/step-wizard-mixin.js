define(
    [
        'underscore'
    ],
    function (_) {
        'use strict';

        return function (component) {
            return component.extend({

                /**
                 * {@inheritdoc}
                 */
                initialize: function () {
                    this._super();
                    this.initializeStepsNames();
                },

                /**
                 * Prepare stepsNames array for the further correct usage
                 */
                initializeStepsNames: function () {
                    if (_.isArray(this.stepsNames)) {
                        this.stepsNames.first = function() {
                            return _.first(this);
                        }
                    }
                }
            });
        }
    }
);