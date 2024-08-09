import {htmlEncode} from "../Utility/TransformUtility";
import {SiteElementVcardInterface} from "../Interfaces/SiteElementVcardInterface";
import {ToJsonInterface} from "../Interfaces/ToJsonInterface";
import {LabelInterface} from "../Interfaces/LabelInterface";

/**
 * MODEL: SiteElementText
 */
export class SiteElementVcard implements SiteElementVcardInterface, ToJsonInterface {
  private _firstName: string = '';
  private _lastName: string = '';
  private _address: string = '';
  private _email: string = '';
  private _website: string = '';
  private _phone: string = '';
  private _mobile: string = '';
  private _companyName: string = '';

  constructor(
    firstName: string,
    lastName: string,
    address: string,
    email: string,
    website: string,
    phone: string,
    mobile: string,
    companyName: string
  ) {
    this.firstName = firstName;
    this.lastName = lastName;
    this.address = address;
    this.email = email;
    this.website = website;
    this.phone = phone;
    this.mobile = mobile;
    this.companyName = companyName;
  }

  /**
   * Normal getter
   */
  get firstName(): string { return this._firstName; }
  get lastName(): string { return this._lastName; }
  get address(): string { return this._address; }
  get email(): string { return this._email; }
  get website(): string { return this._website; }
  get phone(): string { return this._phone; }
  get mobile(): string { return this._mobile; }
  get companyName(): string { return this._companyName; }

  /**
   * Save value save
   */
  set firstName(value: string) { this._firstName = this.validateText(value); }
  set lastName(value: string) { this._lastName = this.validateText(value); }
  set address(value: string) { this._address = this.validateText(value); }
  set email(value: string) { this._email = this.validateEmail(value); }
  set website(value: string) { this._website = this.validateURL(value); }
  set phone(value: string) { this._phone = this.validatePhoneNumber(value); }
  set mobile(value: string) { this._mobile = this.validatePhoneNumber(value); }
  set companyName(value: string) { this._companyName = this.validateText(value); }

  /**
   * Get's values HTML encoded
   */
  get firstNameEncoded(): string { return htmlEncode(this.firstName); }
  get lastNameEncoded(): string { return htmlEncode(this.lastName); }
  get addressEncoded(): string { return htmlEncode(this.address); }
  get emailEncoded(): string { return htmlEncode(this.email); }
  get websiteEncoded(): string { return htmlEncode(this.website); }
  get phoneEncoded(): string { return htmlEncode(this.phone); }
  get mobileEncoded(): string { return htmlEncode(this.mobile); }
  get companyNameEncoded(): string { return htmlEncode(this.companyName); }

  /**
   * Transform to JSON Object
   *
   */
  toJSON(): SiteElementVcardInterface {
    return <SiteElementVcardInterface>{
      firstName: this.firstNameEncoded,
      lastName: this.lastNameEncoded,
      address: this.addressEncoded,
      email: this.email,
      website: this.website,
      phone: this.phone,
      mobile: this.mobile,
      companyName: this.companyNameEncoded
    };
  }

  /**
   * Validates Text
   *
   * @param input
   * @private
   */
  private validateText(input: string): string {
    const sanitizedInput: string = input.replace(/<[^>]*>?/gm, ''); // Remove HTML Tags
    const textPattern: RegExp = /^[a-zA-Z0-9\s.,'-]*$/;
    return textPattern.test(sanitizedInput) ? sanitizedInput : '';
  }

  /**
   * Validates E-Mail
   *
   * @param email
   * @private
   */
  private validateEmail(email: string): string {
    const emailPattern: RegExp = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email) ? email : '';
  }

  /**
   * Validates URL
   *
   * @param url
   * @private
   */
  private validateURL(url: string): string {
    try {
      new URL(url);
      return url;
    } catch {
      return '';
    }
  }

  /**
   * Validates Phone number
   *
   * @param phone
   * @private
   */
  private validatePhoneNumber(phone: string): string {
    const phonePattern: RegExp = /^\+?[0-9\s\-]+$/;
    return phonePattern.test(phone) ? phone : '';
  }

  /**
   * Generates the VCard Content
   *
   */
  generateVCard(): string {
    return [
      `BEGIN:VCARD`,
      `VERSION:3.0`,
      `FN:${this.firstName} ${this.lastName}`,
      `ORG:${this.companyName}`,
      `TEL;TYPE=WORK,VOICE:${this.phone}`,
      `TEL;TYPE=CELL,VOICE:${this.mobile}`,
      `ADR;TYPE=WORK,PREF:;;${this.address}`,
      `EMAIL:${this.email}`,
      `URL:${this.website}`,
      `END:VCARD`
    ].join("\n").trim();
  }

  /**
   * Triggers a download of the VCard
   *
   */
  downloadVCard(): void {
    const vcardData: string = this.generateVCard();
    const blob: Blob = new Blob([vcardData], { type: 'text/vcard' });
    const url: string = window.URL.createObjectURL(blob);

    const a: HTMLAnchorElement = document.createElement('a');
    a.href = url;
    a.download = `${this.firstName}_${this.lastName}.vcf`;
    a.click();
    window.URL.revokeObjectURL(url);
  }

  /**
   * Dashboard Label
   *
   */
  getLabel(): string {
    return this.companyNameEncoded || this.firstNameEncoded + ' ' + this.lastNameEncoded;
  }
}
