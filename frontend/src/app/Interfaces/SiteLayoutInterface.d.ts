
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

  toJSON(): SiteLayoutInterface;
}

/**
 * Basic Element Configuration
 * It's a generic Interface. It makes it easier to use it in typescript to force and identify that element type.
 */
export interface SiteElementInterface<T> {
  uid: string;
  type: SiteElementsTypes;
  element: T;

  toJSON(): SiteElementInterface<T>;
}

/**
 * Element Interface: Headline
 */
export interface SiteElementHeadlineInterface {
  layout: int;
  value: string;

  toJSON(): SiteElementHeadlineInterface;
}

/**
 * Element Interface: Text
 */
export interface SiteElementTextInterface {
  value: string;

  toJSON(): SiteElementTextInterface;
}

/**
 * Element Interface: Image
 */
export interface SiteElementImageInterface {
  imageData: string;

  toJSON(): SiteElementImageInterface;
}

/**
 * Element Interface: Link
 */
export interface SiteElementLinkInterface {
  title: string;
  href: string;

  toJSON(): SiteElementLinkInterface;
}
