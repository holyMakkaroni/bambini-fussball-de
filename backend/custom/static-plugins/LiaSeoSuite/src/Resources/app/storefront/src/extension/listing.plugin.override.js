import ListingPlugin from 'src/plugin/listing/listing.plugin';
import ElementReplaceHelper from 'src/helper/element-replace.helper';
import HistoryUtil from 'src/utility/history/history.util';

/*
 * We need to override the Listing Plugin to hot reload the Faceted Navigation content
 * on filter change.
 */
export default class ListingPluginOverride extends ListingPlugin {
    static regexSeoFilter() {
        return new RegExp('\\/[^\\/]+--[^\\/]+.*$');
    }

    static paramPrefix() {
        return 'lia'
    }

    init() {
        super.init();

        this.filterMap = this._createFilterMapFromUrlAppliedFilters();
    }

    /**
     * @param filterItem
     * @public
     */
    registerFilter(filterItem) {
        if (filterItem.options.name !== 'properties' && filterItem.options.name !== 'manufacturer') {
            return super.registerFilter(filterItem);
        }
        const filterKey = filterItem.options.filterName;

        if (!this.filterMap.hasOwnProperty(filterKey) || !this.filterMap[filterKey]) {
            return super.registerFilter(filterItem);
        }

        // Read the filter option ID's from the elements inside the filter item based on their names
        const optionIds = []
        const manufacturerIds = []

        for (const optionKey of this.filterMap[filterKey]) {
            let optionEl = filterItem.el.querySelector(`[data-option-value="${optionKey}" i]`);

            if (!optionEl) {
                continue;
            }

            const optionId = optionEl.dataset.id;

            if (optionId) {
                if (filterKey === 'manufacturer') {
                    manufacturerIds.push(optionId);
                } else {
                    optionIds.push(optionId)
                }
            }
        }

        if (optionIds.length > 0) {
            this._urlFilterParams['properties'] = optionIds.join('|');
        }

        if (manufacturerIds.length > 0) {
            this._urlFilterParams['manufacturer'] = manufacturerIds.join('|');
        }

        super.registerFilter(filterItem);
    }

    /**
     * Inject the HTML of the updated faceted navigation content and the new listing products to the page.
     *
     * @param {String} response - HTML of filtered product data.
     */
    renderResponse(response) {
        ElementReplaceHelper.replaceFromMarkup(response, this.options.cmsProductListingSelector, false);

        this._registry.forEach((item) => {
            if (typeof item.afterContentChange === 'function') {
                item.afterContentChange();
            }
        });

        window.PluginManager.initializePlugins();

        this.$emitter.publish('Listing/afterRenderResponse', { response });
    }

    /**
     * Replace the default properties query string consisting of optionIds separated by a '|'
     * with a SEO friendly query pathname
     *
     * @param query
     * @private
     */
    _updateHistory(query) {
        query = query.replace(/properties=[\w\d|%]+/, '');
        query = query.replace(/manufacturer=[\w\d|%]+/, '');
        query = query.replace(/^&/, '?');
        query = query.replace(/&$/, '');

        // Build a new SEO friendly filter path
        const filter = this._getFilterUrlPathOfRegisteredFilters();
        const filterPath = filter.path;
        const filterQuery = filter.query;

        query = filterQuery.length > 0 ? filterQuery.join('&') + '&' + query : query

        if (filterPath === null) {
            // Remove filters from the url
            const urlPathname = HistoryUtil.getLocation().pathname.replace(ListingPluginOverride.regexSeoFilter(), '/');

            return HistoryUtil.push(urlPathname, query, {});
        }

        // Remove the trailing '/' or any existing filter path from the current pathname
        const urlPathname = HistoryUtil.getLocation().pathname.replace(ListingPluginOverride.regexSeoFilter(), '').replace(/\/+$/, '');

        HistoryUtil.push(urlPathname + filterPath, query, {});
    }

    /**
     * Build URL from selected filters
     *
     * @returns {string|null}
     * @private
     */
    _getFilterUrlPathOfRegisteredFilters() {
        let filters = [];
        let activeFilters = [];

        this._registry.forEach((filterPlugin) => {
            if (
                (
                    filterPlugin.options.name !== "properties"
                    && filterPlugin.options.name !== "manufacturer"
                ) || filterPlugin.selection.length < 1
            ) {
                return;
            }

            for (const optionId of filterPlugin.selection) {
                const optionEl = filterPlugin.el.querySelector(`[data-id="${optionId}"]`);
                if (!optionEl) {
                    continue;
                }

                const filterName = filterPlugin.options.filterName;
                const filterPriority = filterPlugin.options.filterPriority;
                const optionValue = optionEl.dataset.optionValue;

                filters.push({
                    name: filterName,
                    priority: filterPriority,
                    value: optionValue
                });

                // Sort filter by priority
                filters = this._sortByPriority(filters);

                activeFilters = filters.reduce(function (r, a) {
                    r[a.name] = r[a.name] || [];
                    r[a.name].push(a.value);
                    return r;
                }, Object.create(null));
            }
        });

        return this._createSeoUrl(activeFilters);
    }

    /**
     * Executed to parse URL filter query select filters when the page gets loaded
     *
     * @returns {{}}
     * @private
     */
    _createFilterMapFromUrlAppliedFilters() {
        const filterUrl = location.pathname.match(ListingPluginOverride.regexSeoFilter());
        const queryString = location.search;

        if (filterUrl === null) {
            return {};
        }

        const filterString = filterUrl[0] || "";
        const filterParts = filterString.split('/');

        const filterMap = {}

        if(queryString) {
            const queries = queryString.replace('?', '').split('&');
            const filterQueries = {}
            for (let i = 0; i < queries.length; i++) {
                const filter = queries[i].split('=');
                const identifierKey = filter[0].replace('[]', '')
                const key = identifierKey.replace(ListingPluginOverride.paramPrefix() + '-', '')
                const value = filter[1]

                if(identifierKey.includes(ListingPluginOverride.paramPrefix() + '-')) {
                    filterMap[key] = filterMap.hasOwnProperty(key) ? [...filterMap[key], value] : [value];
                }
            }
        }

        for (let i = 0; i < filterParts.length; i++) {
            if(!filterParts[i]) {
                continue;
            }

            const [key, value] = filterParts[i].split('--');

            const decodedValue = value;

            filterMap[key] = filterMap.hasOwnProperty(key) ? [...filterMap[key], decodedValue] : [decodedValue];
        }

        return filterMap;
    }

    _createSeoUrl(activeFilters) {
        let filterUrl = null;
        let filterString = [];
        let filterQueries = [];

        Object.keys(activeFilters).forEach(key => {
            Object.keys(activeFilters[key]).forEach(index => {
                if(index === '0') {
                    filterString.push(`${key}--${activeFilters[key][index]}`)
                } else {
                    filterQueries.push(`${ListingPluginOverride.paramPrefix()}-${key}[]=${activeFilters[key][index]}`)
                }
            })
        });

        if(Object.keys(activeFilters).length > 0) {
            filterUrl = '/' + filterString.join('/')
        } else {
            filterUrl = null
        }

        return {
            path: filterUrl,
            query: filterQueries
        };
    }

    _sortByPriority(filters) {
        return filters.sort((a, b) => b.priority - a.priority);
    }
}
