import {Component, EventEmitter, Input, Output} from '@angular/core';
import {DialogModule} from "primeng/dialog";
import {Button} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {ButtonPressedTypes, DialogEventDataInterface} from "../../../Interfaces/DialogDataInterface";
import {FormsModule} from "@angular/forms";
import {InputTextareaModule} from "primeng/inputtextarea";
import {GlobalContextStorageService} from "../../../Service/globalContextStorage.service";

/**
 * Save Dialog component
 *
 * Shows a Dialog before save
 */
@Component({
  selector: 'import-dialog',
  standalone: true,
  imports: [
    DialogModule,
    Button,
    FormsModule,
    InputTextareaModule
  ],
  templateUrl: './import-dialog.component.html',
  styleUrl: './import-dialog.component.scss'
})
export class ImportDialogComponent {
  // Component Arguments and Events
  @Input() visible: boolean = false;
  @Output() buttonClicked: EventEmitter<DialogEventDataInterface> = new EventEmitter<DialogEventDataInterface>();

  // siteLayoutImported getter/setter
  protected get siteLayoutImported(): string {
    return this.globalStorageService.getStorageValue('siteLayoutImported') || '';
  }
  protected set siteLayoutImported(value: string) {
    this.globalStorageService.setStorageValue('siteLayoutImported', value);
    this.importBtnDisabled = (this.siteLayoutImported.length <= 0); // Enable/Disable Save Button
  }

  // Switch for Save Button (Enable/Disable)
  protected importBtnDisabled: boolean = true;

  /**
   * Constructor for dependency Injections
   *
   * @param globalStorageService
   */
  constructor(
    private globalStorageService: GlobalContextStorageService
  ) {}

  /**
   * Triggers the cross button click event and submits the button identifier
   *
   * @param e
   * @protected
   */
  protected onClose(e: Event): void {
    this.visible = false; // Hide dialog
    this.siteLayoutImported = ''; // Reset textfield
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
        buttonPressed: buttonName
      });
    }
  }
}
