---
title: useUtilities
---

# {{ $frontmatter.title }}
The `useUtilities` composable provides a set of utility functions. This composable leverages the `useI18n` composable from Vue I18n to obtain the current locale and format dates and times accordingly.

## Functions

### formatDate()
`string` Formats a timestamp into a full date string (e.g., "January 1, 2022")

#### Parameters
- `timestamp`
- **Default**: `''` `required`
- **Type**: String
- **Description**: A string representing the date and time.

### dayNumber()
`string` Extracts the day number of the month from a timestamp based on the current locale.

#### Parameters
- `timestamp`
- **Default**: `''` `required`
- **Type**: String
- **Description**: A string representing the date and time.

### monthName()
`string` Extracts the month name from a timestamp based on the current locale.

#### Parameters
- `timestamp`
- **Default**: `''` `required`
- **Type**: String
- **Description**: A string representing the date and time.

### time()
`string` Formats a timestamp into a time string (e.g., "14:30")

#### Parameters
- `timestamp`
- **Default**: `''` `required`
- **Type**: String
- **Description**: A string representing the date and time.

## Usage

### formatDate()
```vue
<script setup>
  const { formatDate } = useUtilities()
</script>

<template>
  <div>formatDate('2024-01-01T23:50:21.817Z')</div>
</template>
```
::: info Output
January 1, 2022
:::

### dayNumber()
```vue
<script setup>
  const { dayNumber } = useUtilities()
</script>

<template>
  <div>dayNumber('2024-01-01T23:50:21.817Z')</div>
</template>
```
::: info Output
1
:::

### monthName()
```vue
<script setup>
  const { monthName } = useUtilities()
</script>

<template>
  <div>monthName('2024-01-01T23:50:21.817Z')</div>
</template>
```
::: info Output
January
:::

### time()
```vue
<script setup>
  const { time } = useUtilities()
</script>

<template>
  <div>time('2024-01-01T23:50:21.817Z')</div>
</template>
```
::: info Output
23:50
:::