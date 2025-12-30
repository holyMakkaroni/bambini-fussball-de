# Components
In this project, components are categorized into three types: **App components**, **Base components**, and **Form components**. Each type follows a consistent naming and folder structure to maintain organization and readability. Below are the guidelines and examples for creating each type of component.

## Create a new component

### App
App components are stored inside the `/components/app` folder.

1. Inside the `/components/app` folder, create a new folder with the component name, e.g., `AppHeader`.
2. Inside the newly created folder `/components/app/AppHeader`, create the component file named `<ComponentName>.vue`, e.g., `AppHeader.vue`.
3. Optionally, create a separate stylesheet for the component with the name `<ComponentName>.pcss`, e.g., `AppHeader.pcss`.
4. In the root tag of the component, use a class name that is the kebab-case version of the component name, e.g., `app-header`.

#### Create component
Instructions on how to create a new component

##### File Structure
```
app/
└── AppHeader/
    ├── AppHeader.vue
    └── AppHeader.pcss (optional)
```

##### Component
::: code-group
```vue [components/app/AppHeader/AppHeader.vue]
<script setup>
</script>

<template>
  <div class="app-header">
    <!-- Component content goes here -->
  </div>
</template>

<style scoped>
</style>
```
:::

##### Custom styles (optional)
::: code-group
```css [components/app/AppHeader/AppHeader.pcss]
.app-header {
  /* Styles for the AppHeader component */
}
```
:::

and import the new style

::: code-group
```css [assets/css/all.pcss]
...

/* Import app components */
@import "../../components/app/AppHeader/AppHeader.pcss"; // [!code ++]

...
```
:::

##### Usage
::: info
The component is automatically available globally.
```vue
<AppHeader />
```
:::

<hr>

### Base
Base components are stored inside the `/components/base` folder. The process of creating a base component is the same as for [app components](/nuxt-3/components/overview/#app).

#### Create component
Instructions on how to create a new component

##### File structure
```
base/
└── BaseButton/
    ├── BaseButton.vue
    └── BaseButton.pcss (optional)
```

##### Component

::: code-group
```vue [components/base/BaseButton/BaseButton.vue]
<script setup>
</script>

<template>
  <div class="base-button">
    <!-- Button content goes here -->
  </div>
</template>

<style scoped>
</style>
```
:::

##### Custom styles (optional)

::: code-group
```css [components/base/BaseButton/BaseButton.pcss]
.base-button {
  /* Styles for the AppHeader component */
}
```
:::

and import the new style

::: code-group
```css [assets/css/all.pcss]
...

/* Import base components */
@import "../../components/base/BaseButton/BaseButton.pcss"; // [!code ++]

...
```
:::

##### Usage
::: info
The component is automatically available globally.
```vue
<BaseButton />
```
:::

<hr>

### Form
Form components are stored inside the `/components/form` folder. The process of creating a form component is the same as for [app components](/nuxt-3/components/overview#app).

#### Create component
Instructions on how to create a new component

##### File structure
```
form/
└── FormInput/
    ├── FormInput.vue
    └── FormInput.pcss (optional)
```

##### Component

::: code-group
```vue [components/form/FormInput/FormInput.vue]
<script setup>
</script>

<template>
  <div class="form-input">
    <input type="text" />
  </div>
</template>

<style scoped>
</style>
```
:::

##### Custom styles (optional)

::: code-group
```css [components/form/FormInput/FormInput.pcss]
.form-input {
  /* Styles for the FormInput component */
}
```
:::

and import the new style

::: code-group
```css [assets/css/all.pcss]
...

/* Import form components */
@import "../../components/form/FormInput/FormInput.pcss"; // [!code ++]

...
```
:::

##### Usage
::: info
The component is automatically available globally.
```vue
<FormInput />
```
:::