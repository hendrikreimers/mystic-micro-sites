import {Component, OnInit} from '@angular/core';
import {SiteElementHeadlineInterface} from "../../../Interfaces/SiteLayoutModel";
import {CommonModule} from "@angular/common";
import {DropdownOptions, DropdownOptionsInterface} from "../../../Interfaces/DropdownOptionsInterface";
import {FormsModule} from "@angular/forms";
import {DropdownModule} from "primeng/dropdown";
import {InputTextModule} from "primeng/inputtext";
import {Button} from "primeng/button";
import {BaseFieldComponent} from "../../../Classes/BaseFieldComponent";
import {headerLayoutOptions} from "../../../Configs/DropdownOptions";

/**
 * Headline Field Element
 *
 * SITE ELEMENT: Headline
 * SITE ELEMENT TYPE: Field
 *
 */
@Component({
  selector: 'headline-field',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    DropdownModule,
    InputTextModule,
    Button
  ],
  templateUrl: './headline-field.component.html',
  styleUrl: './headline-field.component.scss'
})
export class HeadlineFieldComponent extends BaseFieldComponent<SiteElementHeadlineInterface> implements OnInit {
  // Temporary value (before it's saved)
  public headlineValue: string = '';

  // Make this available in the HTML Template
  protected readonly headerLayoutOptions: DropdownOptions = headerLayoutOptions;

  // DROPDOWN - headerLayout / Getter and Setter
  public get headerLayout(): DropdownOptionsInterface {
    const m: DropdownOptionsInterface | undefined = headerLayoutOptions.find( (lo: DropdownOptionsInterface): boolean =>
      lo.value === this.elementConfig.element.layout
    );

    return m || headerLayoutOptions[0];
  }
  public set headerLayout(option: DropdownOptionsInterface) {
    this.elementConfig.element.layout = option.value;
  }

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
    this.headlineValue = this.elementConfig.element.value
  }

  /**
   * Changes the config value
   *
   * @param e
   */
  protected onSaveValue(e: MouseEvent): void {
    this.elementConfig.element.value = this.headlineValue;
    this.onElementChange();
  }

  /**
   * Resets the input value
   *
   * @param e
   */
  protected onCancelValue(e: MouseEvent): void {
    this.headlineValue = this.elementConfig.element.value;
  }

  /**
   * Checks if the header value changed to enable/disable the save/cancel buttons
   *
   */
  protected areButtonsEnabled(): boolean {
    return this.headlineValue === this.elementConfig.element.value;
  }

}
