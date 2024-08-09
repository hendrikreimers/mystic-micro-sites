import {Component, EventEmitter, Input, Output} from "@angular/core";
import {CommonModule} from "@angular/common";
import {SiteElement} from "../Models/SiteElementModel";
import {SiteElements} from "../Types/SiteElementsType";

/**
 * Base Component for Field Elements
 *
 */
@Component({
  standalone: true,
  template: ``,
  imports: [
    CommonModule
  ]
})
export class BaseFieldComponent<T extends SiteElements> {
  // Event Emitters
  @Output() elementChange: EventEmitter<SiteElement<T>> = new EventEmitter<SiteElement<T>>();
  @Output() elementRemove: EventEmitter<string> = new EventEmitter<string>();

  // Component Attributes
  @Input() uid!: string; // Unique Identifier (UID)
  @Input() elementConfig!: SiteElement<T>; // Element Configuration (Generic)

  /**
   * Triggers the event (especially on parent) to submit changes
   *
   */
  protected onElementChange(): void {
    this.elementChange.emit(this.elementConfig);
  }

  /**
   * Triggers the event (especially on parent) to remove this element
   *
   */
  protected onElementRemove(): void {
    this.elementRemove.emit(this.elementConfig.uid);
  }
}
