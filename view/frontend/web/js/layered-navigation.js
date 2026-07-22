define([
    'Buhmann_Catalog/js/layered-navigation',
    'jquery',
    'ko',
    'underscore'
], function (Component, $, ko, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Buhmann_CatalogSmile/layer/wrapper',
        },

        /**
         * Initialize root navigation components and map reactive tracking loops
         */
        initialize: function () {
            this._super();

            console.log(this);

            return this;
        },

        /**
         * Check if there is at least one filter that actually contains selectable options or an active slider
         *
         * @returns {Boolean}
         */
        hasFilters: function () {
            const groups = this.filtersData() || [];

            return groups.some(group => {
                // Check standard filters that have selectable items available
                if (group.items && Array.isArray(group.items) && group.items.length > 0) {
                    return true;
                }

                // Check slider filters using the exact configuration boundary logic from active list
                if (group.type === 'slider' && group.sliderConfig && group.sliderConfig.currentValue) {
                    const val = group.sliderConfig.currentValue;
                    const min = group.sliderConfig.minValue;
                    const max = group.sliderConfig.maxValue;

                    if (val && min && max && (parseFloat(val.from) > parseFloat(min) || parseFloat(val.to) < parseFloat(max))) {
                        return true;
                    }
                }

                return false;
            });
        },
    });
});
