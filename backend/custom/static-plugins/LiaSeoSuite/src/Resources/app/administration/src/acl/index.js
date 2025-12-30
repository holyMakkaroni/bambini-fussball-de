
Shopware.Service('privileges')
    .addPrivilegeMappingEntry({
        category: 'additional_permissions',
        parent: null,
        key: 'liaSeoSuite',
        roles: {
            propertyEditFilterName: {
                privileges: ['lia_seoSuite_property_group_filter:update'],
                dependencies: []
            },
            propertyEditOptionValue: {
                privileges: ['lia_seoSuite_property_group_option:update'],
                dependencies: []
            }
        }
    });
