import {SiteLayoutInterface} from "../Interfaces/SiteLayoutInterface";
import {htmlEncode} from "../Utility/TransformUtility";
import {SiteElementInterface} from "../Interfaces/SiteElementInterface";
import {SiteElements} from "../Types/SiteElementsType";
import {FontFamilies} from "../Types/FontFamilies";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";
import {SiteElement} from "./SiteElementModel";

/**
 * MODEL: SiteLayout
 */
export class SiteLayout implements SiteLayoutInterface, ToJsonInterface {
  constructor(
    public textColor: string,
    public bgColor: string,
    public fontFamily: FontFamilies,
    public elements: SiteElement<SiteElements>[]
  ) {}

  /**
   * Get value HTML encoded
   */
  get textColorEncoded(): string {
    return htmlEncode(this.textColor);
  }

  /**
   * Get value HTML encoded
   */
  get bgColorEncoded(): string {
    return htmlEncode(this.bgColor);
  }

  /**
   * Transform to simple JSON and stringifies
   *
   */
  getJSONString(): string {
    return JSON.stringify(this.toJSON());
  }

  /**
   * Transform to JSON Object
   *
   */
  toJSON(): SiteLayoutInterface {
    return <SiteLayoutInterface>{
      textColor: this.textColorEncoded,
      bgColor: this.bgColorEncoded,
      fontFamily: this.fontFamily,
      elements: this.elements.map((element: SiteElementInterface<SiteElements>) => (element as SiteElement<SiteElements>).toJSON())
    };
  }

  /**
   * Create an instance of SiteLayout from a JSON object
   *
   * @param json
   */
  static fromJSON(json: SiteLayoutInterface): SiteLayout {
    const elements = json.elements.map((element: SiteElementInterface<SiteElements>) => SiteElement.fromJSON(element));
    return new SiteLayout(json.textColor, json.bgColor, json.fontFamily, elements);
  }
}

