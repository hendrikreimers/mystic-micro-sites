import {SiteElementImageInterface} from "../Interfaces/SiteElementImageInterface";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";

/**
 * MODEL: SiteElementImage
 *
 */
export class SiteElementImage implements SiteElementImageInterface, ToJsonInterface {
  constructor(public imageData: string) {}

  /**
   * Transform to JSON Object
   *
   */
  // No encoding needed for imageData
  toJSON(): SiteElementImageInterface {
    return <SiteElementImageInterface>{
      imageData: this.imageData
    };
  }
}
