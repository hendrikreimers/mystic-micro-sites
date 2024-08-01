import {Component, Input} from "@angular/core";
import {SiteElement} from "../Models/SiteLayoutModel";
import {CommonModule} from "@angular/common";

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
