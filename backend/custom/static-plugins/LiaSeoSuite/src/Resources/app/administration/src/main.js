import './mixin/slugify.mixin'
import './acl'
import './component/sw-property-detail-seo-suite'
import './component/sw-property-option-detail'
import './page/sw-property-detail'
import './component/sw-category-view'
import './view/sw-category-filter-properties'
import './component/sw-category-filter-properties-form'

import enGB from './snippet/en-GB.json';
import deDE from './snippet/de-DE.json';

Shopware.Component.register('sw-property-detail-seo-suite', () => import('./component/sw-property-detail-seo-suite'));
Shopware.Component.register('sw-category-filter-properties', () => import('./view/sw-category-filter-properties'));
Shopware.Component.register('sw-category-filter-properties-form', () => import('./component/sw-category-filter-properties-form'));

Shopware.Module.register('sw-category-tab-filter-properties', {
  routeMiddleware(next, currentRoute) {
    const customRouteName = 'sw.category.detail.filterProperties';

    if (
      currentRoute.name === 'sw.category.detail'
      && currentRoute.children.every((currentRoute) => currentRoute.name !== customRouteName)
    ) {
      currentRoute.children.push({
        name: customRouteName,
        path: '/sw/category/index/:id/filter-properties',
        component: 'sw-category-filter-properties',
        meta: {
          parentPath: 'sw.category.index'
        }
      });
    }
    next(currentRoute);
  }
});