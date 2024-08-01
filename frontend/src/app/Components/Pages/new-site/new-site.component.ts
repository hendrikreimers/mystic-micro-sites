import {Component, OnInit, ViewEncapsulation} from '@angular/core';
import {Button, ButtonDirective} from "primeng/button";
import {Router, RouterLink} from "@angular/router";
import {ButtonGroupModule} from "primeng/buttongroup";
import {
  FontFamilies,
  SiteElement,
  SiteElementHeadline,
  SiteElementImage,
  SiteElementLink,
  SiteElements, SiteElementsTypes,
  SiteElementText,
  SiteLayoutModel
} from "../../../Models/SiteLayoutModel";
import {CommonModule} from "@angular/common";
import {HeadlineFieldComponent} from "../../Elements/headline-field/headline-field.component";
import {v6 as uuidv6} from 'uuid';
import {LinkFieldComponent} from "../../Elements/link-field/link-field.component";
import {ImageFieldComponent} from "../../Elements/image-field/image-field.component";
import {TextFieldComponent} from "../../Elements/text-field/text-field.component";
import {ElementDefaultValues} from "../../../Configs/ElementDefaults";
import {ColorPickerModule} from "primeng/colorpicker";
import {FormsModule} from "@angular/forms";
import {DropdownOptions, DropdownOptionsModel} from "../../../Models/DropdownOptionsModel";
import {DropdownModule} from "primeng/dropdown";
import {fontFamilyOptions} from "../../../Configs/DropdownOptions";
import {SitePreviewComponent} from "../../Elements/site-preview/site-preview.component";
import {base64Decode, base64Encode, transformSiteElementType} from "../../../Utility/TransformUtility";
import {SaveDialogComponent} from "../../Molecules/save-dialog/save-dialog.component";
import {DialogEventData} from "../../../Models/DialogDataModel";
import {GlobalContextStorageService} from "../../../Service/globalContextStorage.service";

/**
 * PAGE: New Site
 *
 */
@Component({
  selector: 'app-new-site',
  standalone: true,
  imports: [
    CommonModule,
    ButtonDirective,
    RouterLink,
    ButtonGroupModule,
    Button,
    HeadlineFieldComponent,
    LinkFieldComponent,
    ImageFieldComponent,
    TextFieldComponent,
    ColorPickerModule,
    FormsModule,
    DropdownModule,
    SitePreviewComponent,
    SaveDialogComponent
  ],
  templateUrl: './new-site.component.html',
  styleUrl: './new-site.component.scss',
  encapsulation: ViewEncapsulation.None
})
export class NewSiteComponent implements OnInit {
  protected showSaveDialog: boolean = false;

  // Make these available to the HTML Template
  protected readonly fontFamilyOptions: DropdownOptions = fontFamilyOptions;
  protected readonly transformSiteElementType = transformSiteElementType;

  /**
   * Initialize SiteLayout
   */
  protected siteLayout: SiteLayoutModel = {
    textColor: ElementDefaultValues.textColor,
    bgColor: ElementDefaultValues.bgColor,
    fontFamily: ElementDefaultValues.fontFamily,
    elements: []
  };

  // DROPDOWN - fontFamily / Getter and Setter
  protected get fontFamily(): DropdownOptionsModel {
    const m: DropdownOptionsModel | undefined = fontFamilyOptions.find( (lo: DropdownOptionsModel): boolean =>
      lo.value === this.siteLayout.fontFamily
    );

    return m || fontFamilyOptions[0];
  }
  protected set fontFamily(option: DropdownOptionsModel) {
    this.siteLayout.fontFamily = option.value as FontFamilies;
  }

  /**
   * Constructor
   *
   */
  constructor(
    private router: Router,
    private globalStorageService: GlobalContextStorageService
  ) {}

  ngOnInit(): void {
    const savedSiteLayout = this.globalStorageService.getStorageValue('saveSite.siteLayoutEncoded');

    if ( typeof savedSiteLayout === 'string' ) {
      this.siteLayout = base64Decode(savedSiteLayout) as SiteLayoutModel;
    }
  }

  /**
   * Add Element to the editor on button press
   *
   * @param elName
   */
  protected addElement(elName: SiteElementsTypes): void {
    // ELEMENT: Headline
    if ( elName === 'headline' ) {
      const newType: SiteElement<SiteElementHeadline> = this.getNewElementBasicConfig('headline', {
        layout: 1,
        value: ElementDefaultValues.headline
      });

      this.siteLayout.elements.push(newType);
    }

    // ELEMENT: Text
    if ( elName === 'text' ) {
      const newType: SiteElement<SiteElementText> = this.getNewElementBasicConfig<SiteElementText>('text', {
        value: ElementDefaultValues.text
      });

      this.siteLayout.elements.push(newType);
    }

    // ELEMENT: Image
    if ( elName === 'image' ) {
      const newType: SiteElement<SiteElementImage> = this.getNewElementBasicConfig<SiteElementImage>('image', {
        imageData: ElementDefaultValues.imageData
      });

      this.siteLayout.elements.push(newType);
    }

    // ELEMENT: Link
    if ( elName === 'link' ) {
      const newType: SiteElement<SiteElementLink> = this.getNewElementBasicConfig<SiteElementLink>('link', {
        title: ElementDefaultValues.linkTitle,
        href: ElementDefaultValues.linkHref
      })

      this.siteLayout.elements.push(newType);
    }
  }

  /**
   * Returns a new SiteElement on given subtype
   *
   * @param type
   * @param elementConfig
   */
  protected getNewElementBasicConfig<R>(type: SiteElementsTypes, elementConfig: R): SiteElement<R> {
    return <SiteElement<R>>{
      uid: uuidv6(),
      type: type,
      element: elementConfig
    };
  }

  /**
   * Saves the MysticMicroSite in the backend
   *
   * @param dialogData
   * @protected
   */
  protected onSaveDialogButtonClick(dialogData: DialogEventData): void {
    this.showSaveDialog = false;

    // Only do the magic if the save button is really pressed and there's a master pass entered
    if ( dialogData.buttonPressed === 'saveBtn' && dialogData.args && dialogData.args['masterPassword'].length > 0 ) {
      const masterPasswordEncoded: string = base64Encode(dialogData.args['masterPassword']);
      const siteLayoutEncoded: string = base64Encode(this.siteLayout);

      // Save to global storage
      this.globalStorageService.setStorageValue('saveSite.siteLayoutEncoded', siteLayoutEncoded);
      this.globalStorageService.setStorageValue('saveSite.passwordEncoded', masterPasswordEncoded);

      // Redirect to save page
      this.router.navigate(['/new-site-save']);
    }
  }

  /**
   * Removes the requested element, identified by their unique ID
   *
   * @param uid
   */
  protected onElementRemove(uid: string): void {
    this.siteLayout.elements = this.siteLayout.elements.filter((element: SiteElement<SiteElements>): boolean =>
      element.uid !== uid
    );
  }

  /**
   * Updates in the siteLayout storage any changes
   *
   * @param elField
   */
  protected onElementChange(elField: SiteElement<SiteElements>): void {
    this.siteLayout.elements.map((element: SiteElement<SiteElements>): SiteElement<SiteElements> =>
      ( element.uid === elField.uid ) ? elField : element
    );
  }

  /**
   * Moves element up in order
   *
   * @param uid
   */
  protected moveElementUp(uid: string): void {
    const index: number = this.siteLayout.elements.findIndex((el: SiteElement<SiteElements>): boolean =>
      el.uid === uid
    );

    if (index > 0) {
      [this.siteLayout.elements[index - 1], this.siteLayout.elements[index]] = [this.siteLayout.elements[index], this.siteLayout.elements[index - 1]];
    }
  }

  /**
   * Moves element down in order
   *
   * @param uid
   */
  protected moveElementDown(uid: string): void {
    const index: number = this.siteLayout.elements.findIndex((el:SiteElement<any>): boolean =>
      el.uid === uid
    );

    if (index < this.siteLayout.elements.length - 1) {
      [this.siteLayout.elements[index + 1], this.siteLayout.elements[index]] = [this.siteLayout.elements[index], this.siteLayout.elements[index + 1]];
    }
  }

}
