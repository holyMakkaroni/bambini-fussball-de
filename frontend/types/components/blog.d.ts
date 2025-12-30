import type { StoryblokAsset } from "~/types/storyblok.config";
import type { HeadlineType } from "~/types/components/base";

export interface BlogHero {
  /**
   * Represents an image file retrieved from Storyblok CMS.
   */
  image: StoryblokAsset | undefined,

  /**
   * A secondary title that can be optionally provided for additional context or detail.
   * This can be used in cases where an extra title or subtitle is needed.
   */
  additional_title?: string,

  /**
   * Represents the title of an item. This is an optional string
   * that can describe the name or heading associated with the item
   * in context.
   *
   * @type {string}
   */
  title?: string,

  /**
   * Defines the type of headline to be used.
   * Possible values are determined by the HeadlineType enumeration.
   * This variable helps to categorize and format headlines accordingly.
   */
  headline_type: HeadlineType
}