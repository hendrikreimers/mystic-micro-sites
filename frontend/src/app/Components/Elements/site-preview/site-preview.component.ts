import {Component, Input, ViewEncapsulation} from '@angular/core';
import {ElementDefaultValues} from "../../../Configs/ElementDefaults";
import {CommonModule} from "@angular/common";
import {transformSiteElementType} from "../../../Utility/TransformUtility";
import {TextPreviewComponent} from "../text-preview/text-preview.component";
import {ImagePreviewComponent} from "../image-preview/image-preview.component";
import {LinkPreviewComponent} from "../link-preview/link-preview.component";
import {HeadlinePreviewComponent} from "../headline-preview/headline-preview.component";
import {SiteLayout} from "../../../Models/SiteLayoutModel";
import {VcardPreviewComponent} from "../vcard-preview/vcard-preview.component";

/**
 * PAGE: Preview Component
 *
 */
@Component({
  selector: 'site-preview',
  standalone: true,
  imports: [
    CommonModule,
    TextPreviewComponent,
    ImagePreviewComponent,
    LinkPreviewComponent,
    HeadlinePreviewComponent,
    VcardPreviewComponent
  ],
  templateUrl: './site-preview.component.html',
  styleUrl: './site-preview.component.scss',
  encapsulation: ViewEncapsulation.ShadowDom
})
export class SitePreviewComponent {
  // Basic variables and component attributes
  @Input() siteLayout: SiteLayout = new SiteLayout(
    ElementDefaultValues.textColor,
    ElementDefaultValues.bgColor,
    ElementDefaultValues.fontFamily,
    []
  );

  protected readonly transformSiteElementType = transformSiteElementType;
}
