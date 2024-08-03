import {Component, EventEmitter, Input, Output} from '@angular/core';
import {DialogModule} from "primeng/dialog";
import {Button} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {ButtonPressedTypes, DialogEventDataInterface} from "../../../Interfaces/DialogDataInterface";
import {FormsModule} from "@angular/forms";

/**
 * Save Dialog component
 *
 * Shows a Dialog before save
 */
@Component({
  selector: 'save-dialog',
  standalone: true,
  imports: [
    DialogModule,
    Button,
    InputTextModule,
    FormsModule
  ],
  templateUrl: './save-dialog.component.html',
  styleUrl: './save-dialog.component.scss'
})
export class SaveDialogComponent {
  // Component Arguments and Events
  @Input() visible: boolean = false;
  @Output() buttonClicked: EventEmitter<DialogEventDataInterface> = new EventEmitter<DialogEventDataInterface>();

  // Password getter/setter
  private _password: string = '';
  protected get password(): string {
    return this._password;
  }
  protected set password(value: string) {
    this._password = value;
    this.saveBtnDisabled = (this.password.length <= 0); // Enable/Disable Save Button
  }

  // Switch for Save Button (Enable/Disable)
  protected saveBtnDisabled: boolean = true;

  /**
   * Triggers the cross button click event and submits the button identifier
   *
   * @param e
   * @protected
   */
  protected onClose(e: Event): void {
    this.visible = false; // Hide dialog
    this.password = ''; // Reset password
    this.buttonClicked.emit({ buttonPressed: 'closeBtn' }); // Send event
  }

  /**
   * Triggers a button click event and submits the input data as well as the identifier which button is pressed
   *
   * @param e
   * @param buttonName
   * @protected
   */
  protected onButtonClick(e: Event, buttonName: ButtonPressedTypes): void {
    this.visible = false;

    if ( buttonName === 'cancelBtn' ) {
      this.buttonClicked.emit({buttonPressed: buttonName});
    } else {
      this.buttonClicked.emit({
        buttonPressed: buttonName,
        args: {
          masterPassword: this.password
        }
      });
    }

    // Reset input field
    this.password = '';
  }
}
