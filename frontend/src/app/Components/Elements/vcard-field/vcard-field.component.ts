import {Component, OnInit} from '@angular/core';
import {BaseFieldComponent} from "../../../Classes/BaseFieldComponent";
import {SiteElementVcard} from "../../../Models/SiteElementVcardModel";
import {Button} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {CommonModule} from "@angular/common";
import {FormsModule} from "@angular/forms";

@Component({
  selector: 'vcard-field',
  standalone: true,
  imports: [
    CommonModule,
    Button,
    InputTextModule,
    FormsModule
  ],
  templateUrl: './vcard-field.component.html',
  styleUrl: './vcard-field.component.scss'
})
export class VcardFieldComponent extends BaseFieldComponent<SiteElementVcard> implements OnInit {
  // Temporary values (before it's saved)
  public firstName: string = '';
  public lastName: string = '';
  public address: string = '';
  public email: string = '';
  public website: string = '';
  public phone: string = '';
  public mobile: string = '';
  public companyName: string = '';

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
    this.firstName = this.elementConfig.element.firstName;
    this.lastName = this.elementConfig.element.lastName;
    this.address = this.elementConfig.element.address;
    this.email = this.elementConfig.element.email;
    this.website = this.elementConfig.element.website;
    this.phone = this.elementConfig.element.phone;
    this.mobile = this.elementConfig.element.mobile;
    this.companyName = this.elementConfig.element.companyName;
  }

  /**
   * Changes the config value
   *
   * @param e
   */
  protected onSaveValue(e: MouseEvent): void {
    this.elementConfig.element.firstName = this.firstName;
    this.elementConfig.element.lastName = this.lastName;
    this.elementConfig.element.address = this.address;
    this.elementConfig.element.email = this.email;
    this.elementConfig.element.website = this.website;
    this.elementConfig.element.phone = this.phone;
    this.elementConfig.element.mobile = this.mobile;
    this.elementConfig.element.companyName = this.companyName;

    this.onElementChange();
  }

  /**
   * Resets the input value
   *
   * @param e
   */
  protected onCancelValue(e: MouseEvent): void {
    this.firstName = this.elementConfig.element.firstName;
    this.lastName = this.elementConfig.element.lastName;
    this.address = this.elementConfig.element.address;
    this.email = this.elementConfig.element.email;
    this.website = this.elementConfig.element.website;
    this.phone = this.elementConfig.element.phone;
    this.mobile = this.elementConfig.element.mobile;
    this.companyName = this.elementConfig.element.companyName;
  }

  /**
   * Checks if the values changed to enable/disable the save/cancel buttons
   *
   */
  protected areButtonsEnabled(): boolean {
    return  this.firstName === this.elementConfig.element.firstName &&
            this.lastName === this.elementConfig.element.lastName &&
            this.address === this.elementConfig.element.address &&
            this.email === this.elementConfig.element.email &&
            this.website === this.elementConfig.element.website &&
            this.phone === this.elementConfig.element.phone &&
            this.mobile === this.elementConfig.element.mobile &&
            this.companyName === this.elementConfig.element.companyName;
  }
}
