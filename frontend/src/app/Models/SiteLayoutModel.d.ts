
/**
 * Type Definitions
 */
export type SiteElements = SiteElementHeadline | SiteElementText | SiteElementLink | SiteElementImage;
export type SiteElementsTypes = 'headline' | 'text' | 'image' | 'link';
export type FontFamilies = 'Arial' | 'Helvetica' | 'sans-serif' | 'TimesNewRoman' | 'CourierNewRoman' | 'ComicSansMS';

/**
 * Basic Site Layout Interface
 */
export interface SiteLayoutModel {
  textColor: string;
  bgColor: string;
  fontFamily: FontFamilies;

  elements: SiteElement[];
}

/**
 * Basic Element Configuration
 * It's a generic Interface. It makes it easier to use it in typescript to force and identify that element type.
 */
export interface SiteElement<T> {
  uid: string;
  type: SiteElementsTypes;
  element: T;
}

/**
 * Element Interface: Headline
 */
export interface SiteElementHeadline {
  layout: int;
  value: string;
}

/**
 * Element Interface: Text
 */
export interface SiteElementText {
  value: string;
}

/**
 * Element Interface: Image
 */
export interface SiteElementImage {
  imageData: string;
}

/**
 * Element Interface: Link
 */
export interface SiteElementLink {
  title: string;
  href: string;
}
