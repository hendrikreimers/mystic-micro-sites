import {SiteElementHeadlineInterface} from "../Interfaces/SiteElementHeadlineInterface";
import {SiteElementTextInterface} from "../Interfaces/SiteElementTextInterface";
import {SiteElementLinkInterface} from "../Interfaces/SiteElementLinkInterface";
import {SiteElementImageInterface} from "../Interfaces/SiteElementImageInterface";

import {SiteElementVcardInterface} from "../Interfaces/SiteElementVcardInterface";

/**
 * Type Definitions
 */
export type SiteElements =
  SiteElementHeadlineInterface
  | SiteElementTextInterface
  | SiteElementLinkInterface
  | SiteElementImageInterface
  | SiteElementVcardInterface;
