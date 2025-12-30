import type { StoryblokAsset } from "~/types/storyblok.config";
import type { Schemas } from "#shopware";
import type { TypeAuthorStoryblok } from "~/types/storyblok";
import type {EmblaOptionsType} from "embla-carousel";

type AnchorTarget =
  | "_self"
  | "_blank"
  | "_parent"
  | "_top"

export type Icon =
  | 'bars'
  | 'building'
  | 'cart'
  | 'chat'
  | 'chat-2'
  | 'check'
  | 'chevron'
  | 'chevron-double'
  | 'chevron-thin'
  | 'close'
  | 'dhl'
  | 'document'
  | 'document-2'
  | 'download'
  | 'easy-credit'
  | 'hearth'
  | 'hearth-outline'
  | 'help-center'
  | 'help-center-2'
  | 'invoice'
  | 'klarna'
  | 'paypal'
  | 'prepayment'
  | 'question-mark'
  | 'search'
  | 'share'
  | 'star'
  | 'star-empty'
  | 'star-half'
  | 'transfer'
  | 'truck'
  | 'truck-2'
  | 'user'
  | 'vdb'
  | 'visa'
  | 'exit'
  | 'garbage'
  | 'exclamation'
  | 'filter';

type HeadlineType =
  | 'h1'
  | 'h2'
  | 'h3'
  | 'h4'
  | 'h5'
  | 'h6'
  | 'div'

type NewsletterInterests =
  | 'forst'
  | 'jagd'
  | 'jungjäger'
  | 'sportschießen'
  | 'wiederladen'

export interface BaseButton {
  /**
   * Defines the visual style of the button
   */
  variant?: 'primary' | 'secondary' | 'green' | 'white',

  /**
   * Determines the size of the button
   */
  size?: 'small' | 'normal' | 'big',

  /**
   * Defines whether the button should have an outline style. If `true`, the button will have an outlined border instead of a filled background
   */
  outline?: boolean,

  /**
   * Specifies an icon to be displayed on the button. This should be identifier for the icon to be used
   */
  icon?: Icon,

  /**
   * Defines the CSS classes for styling the icon size. This is useful for adjusting the icon appearance
   */
  iconClass?: string,

  /**
   * Determines the position of the icon relative to the button text
   */
  iconPosition?: 'left' | 'right',

  /**
   * The URL to navigate to when the button is clicked. If provided, the button will render as a link &#x60;&lt;NuxtLink&gt;&#x60; instead of a standard button
   */
  href?: string,

  /**
   * Specifies where to open the linked document when `href` is provided
   */
  target?: AnchorTarget,

  /**
   * Provides additional information about the button. This text will appear as a tooltip when the user hovers over the button
   */
  title?: string,

  fullWidth?: boolean,

  /**
   * An optional string property used to apply custom CSS classes. This can be utilized
   * to override default styles or to provide additional styling for the associated element.
   */
  customClass?: string,
}

export interface BaseAppointmentCard {
  /**
   * Defines the title of the card
   */
  title: string,

  /**
   * Defines the description of the card
   */
  description?: string,

  /**
   * Defines the starting time of the appointment the card represents
   */
  startAt: string,

  /**
   * Defines the ending time of the appointment the card represents
   */
  endAt: string,

  /**
   * Defines an optional URL related to the appointment the card represents. For instance, it can be a link to further details or actions connected to the appointment.
   */
  url?: string
}

export interface BaseBlogCard {

  /**
   * Represents an image file retrieved from Storyblok CMS.
   */
  image: StoryblokAsset | undefined,

  /**
   * The title of the component.
   */
  title: string,


  /**
   * Represents the date when the variable was created.
   */
  createdDate: string,


  /**
   * Represents the name of the author.
   */
  author?: TypeAuthorStoryblok,


  /**
   * Represents a URL.
   */
  url: string
}

export interface BaseDivider {
  /**
   * Represents the type of line variant, which can be either solid or dashed.
   */
  variant: 'solid' | 'dashed'
}

export interface BaseHeadline {
  /**
   * The HTML tag of the headline element. Valid values are 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', and 'div'.
   */
  tag: HeadlineType,

  /**
   * The text content of the headline element.
   */
  title: string,

  /**
   * Indicates whether the headline should be styled as primary
   */
  primary?: true | false,

  /**
   * Indicates the presence of an anchor.
   */
  anchor?: true | false,

  /**
   * An optional string that specifies a custom CSS class to be applied.
   */
  customClass?: string
}

export interface BaseCrossSelling {
  /**
   * The cross-selling elements
   */
  element: Schemas['CrossSellingElement']
}

export interface BaseOverlay {
  /**
   * Indicates whether to show a loading indicator.
   */
  showLoadingIndicator?: true | false
}

/**
 * Interface representing a base structure for a newsletter form.
 */
export interface BaseNewsletterForm {
  /**
   * The title of the component.
   */
  title?: string,
  /**
   * The headline type of the newsletter form. Valid values are 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', or 'div'.
   */
  headline_type: HeadlineType,
  /**
   * An optional description providing additional information.
   * This can be any string that elaborates on the context or purpose.
   */
  description?: string,

  /**
   * Represents the unique identifier for a specific cleverreach mailing list.
   * This ID is used to track and manage subscriptions, send out mass communications,
   * and organize contacts within a mailing service provider's system.
   */
  mailingListId: string,

  /**
   * An optional array of user's newsletter interests.
   * This property is used to store user's preferences for different newsletters they may be interested in.
   */
  interests?: NewsletterInterests[]
}

/**
 * The BaseIcon interface represents an icon with a specified name.
 */
export interface BaseIcon {
  /**
   * Represents the icon associated with a particular element or feature. Typically, a string that can be used to identify or reference the icon, such as a file path, font icon class, or SVG data.
   */
  name: Icon,
}

/**
 * BaseSidebarNavigation represents the structure for the sidebar navigation configuration.
 * The configuration includes an array of navigation items that determine the elements
 * to be displayed within the sidebar.
 */
export interface BaseSidebarNavigation {
  items: BaseSidebarNavigationItem[]
}

/**
 * Represents a base sidebar navigation item.
 *
 * Contains the essential information for a navigation item in a sidebar, including:
 * - A universally unique identifier (UUID)
 * - The name of the navigation item
 * - A slug, typically used for URL path or routing purposes
 */
export interface BaseSidebarNavigationItem {
  /**
   * Represents a unique identifier for an entity.
   */
  id: number

  /**
   * The unique identifier for the parent entity.
   * This variable is used to establish a parent-child relationship between entities.
   */
  parent_id: number | null,

  /**
   * Represents the name of a entity.
   */
  name: string
  /**
   * The URL to be used for connecting to pages or websites
   */
  url: string

  /**
   * An array of child navigation items for the sidebar.
   */
  children: BaseSidebarNavigationItem[]
}

export interface BaseSidebarNavigationItemRaw {
  /**
   * Represents a unique identifier for an entity.
   */
  id: number

  /**
   * The unique identifier for the parent entity.
   * This variable is used to establish a parent-child relationship between entities.
   */
  parent_id: number | null,

  /**
   * Represents the name of a entity.
   */
  name: string
  /**
   * The URL to be used for connecting to pages or websites
   */
  url: string
}

/**
 * Interface representing a Table of Contents (TOC) component in Storyblok.
 */
export interface TocStoryblok {
  _uid: string,
  component: 'c-toc',
  data: {
    name: string,
    anchor: string
  }[]
}

export interface StaticPages {
  [key: string]: StaticPageItem[]
}

export interface StaticPageItem {
  name: string,
  description: string,
  url: string,
  actionLabel: string,
  showOverview: boolean,
  openWishlist?: boolean
}

export interface BaseCarousel {
  options?: EmblaOptionsType
  itemsClass?: string
  itemClass?: string
  navigationClass?: string
  paginationClass?: string
  pagination?: boolean
  navigation?: boolean
  thumbnails?: boolean
  initSlideIndex?: number
}

export interface BaseCarouselItem {
  itemClass?: string
}