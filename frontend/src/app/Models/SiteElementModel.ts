import {SiteElementInterface} from "../Interfaces/SiteElementInterface";
import {SiteElementsTypes} from "../Types/SiteElementsTypes";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";
import {LabelInterface} from "../Interfaces/LabelInterface";
import {SiteElements} from "../Types/SiteElementsType";

/**
 * MODEL: SiteElement
 */
export class SiteElement<T extends SiteElements> implements SiteElementInterface<T>, ToJsonInterface {
  constructor(
    public uid: string,
    public type: SiteElementsTypes,
    public element: T
  ) {}

  /**
   * Transform to JSON Object
   *
   */
  toJSON(): SiteElementInterface<T> {
    return <SiteElementInterface<T>>{
      uid: this.uid,
      type: this.type,
      element: this.element
    }
  }

  /**
   * Create an instance of SiteElement from a JSON object
   *
   * @param json
   */
  static fromJSON<T extends SiteElements>(json: SiteElementInterface<T>): SiteElement<T> {
    return new SiteElement(json.uid, json.type, json.element);
  }
}
