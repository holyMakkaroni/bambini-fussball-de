import './module/algolia-filter-page';

const { Module } = Shopware;

Module.register('lia-algolia-filter-page', {
    type: 'plugin',
    name: 'Algolia Filter Pages',
    title: 'Algolia Filter Pages',
    description: 'Verwalte SEO-optimierte Filterseiten für Algolia',
    color: '#ff3c38',
    icon: 'regular-shopping-bag',
    routes: {
        index: {
            component: 'lia-algolia-filter-page-list',
            path: 'index',
        },
        detail: {
            component: 'lia-algolia-filter-page-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'lia.algolia.filter.page.index'
            }
        },
        create: {
            component: 'lia-algolia-filter-page-detail',
            path: 'create',
            meta: {
                parentPath: 'lia.algolia.filter.page.index'
            }
        },
    },
    navigation: [{
        id: 'lia-algolia-filter-page',
        label: 'Algolia Filter Pages',
        color: '#ff3c38',
        path: 'lia-algolia-filter-page.index',
        icon: 'regular-filter',
        parent: null,
        position: 100
    }]
});
