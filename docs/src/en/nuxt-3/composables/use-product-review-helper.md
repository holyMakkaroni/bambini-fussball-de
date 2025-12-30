---
title: useProductReviewHelper
---

# {{ $frontmatter.title }}
The `useProductReviewHelper` composable is a utility designed to manage and analyze product reviews in a Nuxt 3 application. It processes a list of product reviews and organizes them into a matrix, grouping reviews by their rating points and calculating the percentage of each rating.

```vue
<script setup>
  const productReviews = [
    {
      "id": "string",
      "productId": "string",
      "productVersionId": "string",
      "salesChannelId": "string",
      "languageId": "string",
      "title": "string",
      "content": "string",
      "points": -3.402823669209385e+38,
      "status": true,
      "comment": "string",
      "customFields": {},
      "createdAt": "2019-08-24T14:15:22Z",
      "updatedAt": "2019-08-24T14:15:22Z"
    },
    {
      "id": "string",
      "productId": "string",
      "productVersionId": "string",
      "salesChannelId": "string",
      "languageId": "string",
      "title": "string",
      "content": "string",
      "points": -3.402823669209385e+38,
      "status": true,
      "comment": "string",
      "customFields": {},
      "createdAt": "2019-08-24T14:15:22Z",
      "updatedAt": "2019-08-24T14:15:22Z"
    },
    {
      "id": "string",
      "productId": "string",
      "productVersionId": "string",
      "salesChannelId": "string",
      "languageId": "string",
      "title": "string",
      "content": "string",
      "points": -3.402823669209385e+38,
      "status": true,
      "comment": "string",
      "customFields": {},
      "createdAt": "2019-08-24T14:15:22Z",
      "updatedAt": "2019-08-24T14:15:22Z"
    }
  ]
  
  const { productReviewMatrix, groupByPoints, maxPoints } = useProductReviewHelper(productReviews)
</script>

<template>
  <ul>
    <li
      v-for="(matrix, index) in productReviewMatrix"
      :key="index">
      {{ matrix.points }} star // `100% - ${matrix.percent}%` / {{ matrix.elements.length ?? 0 }} reviews
    </li>
  </ul>
</template>
```
::: info Output
- 1 star // 0% // 0 reviews
- 2 star // 0% // 0 reviews
- 3 star // 100% // 3 reviews
- 4 star // 0% // 0 reviews
- 5 star // 0% // 0 reviews
:::

## Functions

### groupByPoints()
`array` An array where each index corresponds to a rating point, and the value at that index is an array of reviews that received that rating.

#### Parameters
- `values`
- **Default**: `null` `required`
- **Type**: [ProductReview[]](https://shopware.stoplight.io/docs/store-api/2bb8a8207b41c-product-review) | `null`
- **Description**: Groups the product reviews by their rating points.

### productReviewMatrix()
`array|null` A matrix where each element contains the rating points, the percentage of total reviews, and the list of reviews for that rating.

#### Parameters
- `reviews`
- **Default**: `null` `required`
- **Type**: [ProductReview[]](https://shopware.stoplight.io/docs/store-api/2bb8a8207b41c-product-review) | `null`
- **Description**: Creates a matrix that organizes reviews by rating points and calculates the percentage of each rating.

### maxPoints()
`number` A constant representing the maximum possible points.