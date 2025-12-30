---
title: useBreadcrumbHelper
---

# {{ $frontmatter.title }}
The `useBreadcrumbHelper` composable is a utility function in a Nuxt 3 application designed to generate and manage breadcrumb navigation data. It helps in creating a breadcrumb trail based on the current route path or a specified path.

## Functions

### isPenultimate()
`true|false`

#### Parameters
- `breadcrumb`
- **Default**: `''` `required`
- **Type**: `Breadcrumb`
- **Description**: Checks if a given breadcrumb is the second-to-last in the breadcrumb trail

```ts
export type Breadcrumb = {
  name: string;
  path?: string;
};
```

## Usage

### isPenultimate()
```vue
<script setup>
  const breadcrumbStore = useBreadcrumbStore()
  const breadcrumbs: Breadcrumb[] = breadcrumbStore.breadcrumb
  const { isPenultimate } = useBreadcrumbHelper()
</script>

<template>
  <div v-for="(breadcrumb, index) in breadcrumbs" :key="index">
    <span v-if="isPenultimate(breadcrumb)">Icon</span>
    <span>{{ breadcrumb.name }}</span>
  </div>
</template>
```