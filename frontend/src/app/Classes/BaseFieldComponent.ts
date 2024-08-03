import {Component, EventEmitter, Input, Output} from "@angular/core";
import {SiteElementInterface} from "../Interfaces/SiteLayoutModel";
import {CommonModule} from "@angular/common";

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
export class BaseFieldComponent<T> {
  // Event Emitters
  @Output() elementChange: EventEmitter<SiteElementInterface<T>> = new EventEmitter<SiteElementInterface<T>>();
  @Output() elementRemove: EventEmitter<string> = new EventEmitter<string>();

  // Component Attributes
  @Input() uid!: string; // Unique Identifier (UID)
  @Input() elementConfig!: SiteElementInterface<T>; // Element Configuration (Generic)

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
