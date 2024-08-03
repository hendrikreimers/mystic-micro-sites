
/**
 * Type Definitions
 */
export type SiteElements = SiteElementHeadlineInterface | SiteElementTextInterface | SiteElementLinkInterface | SiteElementImageInterface;
export type SiteElementsTypes = 'headline' | 'text' | 'image' | 'link';
export type FontFamilies = 'Arial' | 'Helvetica' | 'sans-serif' | 'TimesNewRoman' | 'CourierNewRoman' | 'ComicSansMS';

/**
 * Basic Site Layout Interface
 */
export interface SiteLayoutInterface {
  textColor: string;
  bgColor: string;
  fontFamily: FontFamilies;

  elements: SiteElementInterface[];
}

/**
 * Basic Element Configuration
 * It's a generic Interface. It makes it easier to use it in typescript to force and identify that element type.
 */
export interface SiteElementInterface<T> {
  uid: string;
  type: SiteElementsTypes;
  element: T;
}

/**
 * Element Interface: Headline
 */
export interface SiteElementHeadlineInterface {
  layout: int;
  value: string;
}

/**
 * Element Interface: Text
 */
export interface SiteElementTextInterface {
  value: string;
}

/**
 * Element Interface: Image
 */
export interface SiteElementImageInterface {
  imageData: string;
}

/**
 * Element Interface: Link
 */
export interface SiteElementLinkInterface {
  title: string;
  href: string;
}
