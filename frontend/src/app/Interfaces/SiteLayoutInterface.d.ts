import {SiteElementInterface} from "./SiteElementInterface";
import {FontFamilies} from "../Types/FontFamilies";

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
 * Extension with toJSON method
 */
export interface SiteLayoutToJsonInterface {
  toJSON(): SiteLayoutInterface;
}
