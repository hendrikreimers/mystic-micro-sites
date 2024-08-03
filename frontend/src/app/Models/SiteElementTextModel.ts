import {SiteElementTextInterface} from "../Interfaces/SiteElementTextInterface";
import {htmlEncode} from "../Utility/TransformUtility";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";

/**
 * MODEL: SiteElementText
 */
export class SiteElementText implements SiteElementTextInterface, ToJsonInterface {
  constructor(public value: string) {}

  /**
   * Get value HTML encoded
   */
  get valueEncoded(): string {
    return htmlEncode(this.value);
  }

  /**
   * Transform to JSON Object
   *
   */
  toJSON(): SiteElementTextInterface {
    return <SiteElementTextInterface>{
      value: this.valueEncoded
    };
  }
}
