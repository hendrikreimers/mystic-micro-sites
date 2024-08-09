import {SiteElementTextInterface} from "../Interfaces/SiteElementTextInterface";
import {htmlEncode} from "../Utility/TransformUtility";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";
import {LabelInterface} from "../Interfaces/LabelInterface";

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

  /**
   * Dashboard Label
   *
   */
  getLabel(): string {
    return htmlEncode(this.value.substring(0, 20) + '...');
  }
}
