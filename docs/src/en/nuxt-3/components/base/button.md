---
title: BaseButton
---

# `<BaseButton>`

The `BaseButton` component is a versatile button element that can be rendered either as a button or as a link, depending on the presence of the `href` prop. It supports various customization options for appearance and behavior.

## Props

### variant
- **Type**: `('primary'|'secondary'|'green'|'white')`
- **Default**: `primary`
- **Required**: `true`
- **Description**: Defines the visual style of the button.


### size
- **Type**: `('small'|'normal'|'big')`
- **Default**: `normal`
- **Required**: `true`
- **Description**: Determines the size of the button.


### outline
- **Type**: `boolean`
- **Default**: `false`
- **Required**: `false`
- **Description**: Defines whether the button should have an outline style. If &#x60;true&#x60;, the button will have an outlined border instead of a filled background


### icon
- **Type**: `string`
- **Default**: `null`
- **Required**: `false`
- **Description**: Specifies an icon to be displayed on the button. This should be identifier for the icon to be used.


### iconClass
- **Type**: `string`
- **Default**: `w-6 h-6`
- **Required**: `false`
- **Description**: Defines the CSS classes for styling the icon&#x27;s size. This is useful for adjusting the icon&#x27;s appearance


### iconPosition
- **Type**: `('left'|'right')`
- **Default**: `left`
- **Required**: `false`
- **Description**: Determines the position of the icon relative to the button text


### href
- **Type**: `string`
- **Default**: `null`
- **Required**: `false`
- **Description**: The URL to navigate to when the button is clicked. If provided, the button will render as a link &#x60;&lt;NuxtLink&gt;&#x60; instead of a standard button


### target
- **Type**: `string`
- **Default**: `_self`
- **Required**: `false`
- **Description**: Specifies where to open the linked document when &#x60;href&#x60; is provided


### title
- **Type**: `string`
- **Default**: `null`
- **Required**: `false`
- **Description**: Provides additional information about the button. This text will appear as a tooltip when the user hovers over the button.


## Slots

### `default`
The text on the button

