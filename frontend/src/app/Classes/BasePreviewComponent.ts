import {Component, Input} from "@angular/core";
import {CommonModule} from "@angular/common";
import {SiteElement} from "../Models/SiteElementModel";

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
export class BasePreviewComponent<T> {
  // Component Attributes
  @Input() uid!: string; // Unique Identifier (UID)
  @Input() elementConfig!: SiteElement<T>; // Element Configuration (Generic)
}
