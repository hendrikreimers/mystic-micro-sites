import {Component, OnInit} from '@angular/core';
import {BaseFieldComponent} from "../../../Classes/BaseFieldComponent";
import {Button} from "primeng/button";
import {DropdownModule} from "primeng/dropdown";
import {FormsModule} from "@angular/forms";
import {InputTextModule} from "primeng/inputtext";
import {GeneralConfig} from "../../../Configs/GeneralConfig";

import {SiteElementLink} from "../../../Models/SiteElementLinkModel";

@Component({
  selector: 'link-field',
  standalone: true,
  imports: [
    Button,
    DropdownModule,
    FormsModule,
    InputTextModule
  ],
  templateUrl: './link-field.component.html',
  styleUrl: './link-field.component.scss'
})
export class LinkFieldComponent extends BaseFieldComponent<SiteElementLink> implements OnInit {
  // Temporary value (before it's saved)
  public titleValue: string = '';
  public hrefValue: string = '';

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
    this.titleValue = this.elementConfig.element.title;
    this.hrefValue = this.elementConfig.element.href;
  }

  /**
   * Changes the config value
   *
   * @param e
   */
  protected onSaveValue(e: MouseEvent): void {
    // Take care of URIs
    const urlRegexp: RegExp = new RegExp(GeneralConfig.urlExpr, 'i');
    if ( !urlRegexp.test(this.hrefValue) ) {
      this.hrefValue = '';
    }

    this.elementConfig.element.title = this.titleValue;
    this.elementConfig.element.href = this.hrefValue;

    this.onElementChange();
  }

  /**
   * Resets the input value
   *
   * @param e
   */
  protected onCancelValue(e: MouseEvent): void {
    this.titleValue = this.elementConfig.element.title;
    this.hrefValue = this.elementConfig.element.href;
  }

  /**
   * Checks if the header value changed to enable/disable the save/cancel buttons
   *
   */
  protected areButtonsEnabled(): boolean {
    return this.titleValue === this.elementConfig.element.title && this.hrefValue === this.elementConfig.element.href;
  }
}
