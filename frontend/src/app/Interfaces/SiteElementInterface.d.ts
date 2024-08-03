import {SiteElementsTypes} from "../Types/SiteElementsTypes";

/**
 * Basic Element Configuration
 * It's a generic Interface. It makes it easier to use it in typescript to force and identify that element type.
 */
export interface SiteElementInterface<T> {
  uid: string;
  type: SiteElementsTypes;
  element: T;
}
