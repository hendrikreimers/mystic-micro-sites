import {SiteElementHeadlineInterface} from "../Interfaces/SiteElementHeadlineInterface";
import {htmlEncode} from "../Utility/TransformUtility";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";

/**
 * MODEL: SiteElementHeadline
 */
export class SiteElementHeadline implements SiteElementHeadlineInterface, ToJsonInterface {
  constructor(
    public layout: number,
    public value: string
  ) {}

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
  toJSON(): SiteElementHeadlineInterface {
    return <SiteElementHeadlineInterface>{
      layout: this.layout,
      value: this.valueEncoded
    };
  }
}
