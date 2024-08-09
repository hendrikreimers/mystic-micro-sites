import {SiteElementLinkInterface} from "../Interfaces/SiteElementLinkInterface";
import {htmlEncode} from "../Utility/TransformUtility";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";
import {LabelInterface} from "../Interfaces/LabelInterface";

/**
 * MODEL: SiteElementLink
 */
export class SiteElementLink implements SiteElementLinkInterface, ToJsonInterface {
  constructor(
    public title: string,
    public href: string
  ) {
  }

  /**
   * Get value HTML encoded
   */
  get titleEncoded(): string {
    return htmlEncode(this.title);
  }

  /**
   * Transform to JSON Object
   *
   */
  toJSON(): SiteElementLinkInterface {
    return <SiteElementLinkInterface>{
      title: this.titleEncoded,
      href: this.href
    };
  }

  /**
   * Dashboard Label
   *
   */
  getLabel(): string {
    return this.titleEncoded;
  }
}
