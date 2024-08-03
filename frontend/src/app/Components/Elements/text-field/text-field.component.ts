import {Component, OnInit} from '@angular/core';
import {BaseFieldComponent} from "../../../Classes/BaseFieldComponent";
import {InputTextareaModule} from "primeng/inputtextarea";
import {Button} from "primeng/button";
import {FormsModule} from "@angular/forms";
import {InputTextModule} from "primeng/inputtext";
import {SiteElementText} from "../../../Models/SiteLayoutModel";

@Component({
  selector: 'text-field',
  standalone: true,
  imports: [
    InputTextareaModule,
    Button,
    FormsModule,
    InputTextModule
  ],
  templateUrl: './text-field.component.html',
  styleUrl: './text-field.component.scss'
})
export class TextFieldComponent extends BaseFieldComponent<SiteElementText> implements OnInit {
  // Temporary value (before it's saved)
  public textValue: string = '';

  /**
   * Constructor
   *
   */
  constructor() {
    super();
  }

  /**
   * Initializes the headlineValue
   * It's important to react on the button events and not instant model changes.
   *
   */
  ngOnInit(): void {
    this.textValue = this.elementConfig.element.value
  }

  /**
   * Changes the config value
   *
   * @param e
   */
  protected onSaveValue(e: MouseEvent): void {
    this.elementConfig.element.value = this.textValue;
    this.onElementChange();
  }

  /**
   * Resets the input value
   *
   * @param e
   */
  protected onCancelValue(e: MouseEvent): void {
    this.textValue = this.elementConfig.element.value;
  }

  /**
   * Checks if the header value changed to enable/disable the save/cancel buttons
   *
   */
  protected areButtonsEnabled(): boolean {
    return this.textValue === this.elementConfig.element.value;
  }
}
