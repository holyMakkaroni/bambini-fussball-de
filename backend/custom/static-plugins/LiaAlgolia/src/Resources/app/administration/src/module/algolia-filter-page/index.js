Shopware.Module.register('lia-algolia-filter-page', {
    type: 'plugin',
    name: 'FilterPage',
    title: 'Algolia Filter Pages',
    routes: {
        index: {
            component: 'lia-algolia-filter-page-list',
            path: 'index',
        },
        detail: {
            component: 'lia-algolia-filter-page-detail',
            path: 'detail/:id',
        },
        create: {
            component: 'lia-algolia-filter-page-detail',
            path: 'create',
        }
    }
});
