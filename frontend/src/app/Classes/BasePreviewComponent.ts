import {Component, Input} from "@angular/core";
import {CommonModule} from "@angular/common";
import {SiteElement} from "../Models/SiteElementModel";
import {SiteElements} from "../Types/SiteElementsType";

/**
 * Base Component for Preview Elements
 *
 */
@Component({
  standalone: true,
  template: ``,
  imports: [
    CommonModule
  ]
})
export class BasePreviewComponent<T extends SiteElements> {
  // Component Attributes
  @Input() uid!: string; // Unique Identifier (UID)
  @Input() elementConfig!: SiteElement<T>; // Element Configuration (Generic)
}
