import {SiteElementInterface} from "../Interfaces/SiteElementInterface";
import {SiteElementsTypes} from "../Types/SiteElementsTypes";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";

/**
 * MODEL: SiteElement
 */
export class SiteElement<T> implements SiteElementInterface<T>, ToJsonInterface {
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
}
