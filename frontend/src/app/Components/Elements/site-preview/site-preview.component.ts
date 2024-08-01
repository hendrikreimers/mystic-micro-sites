import {Component, Input, OnInit, ViewEncapsulation} from '@angular/core';
import {SiteLayoutModel} from "../../../Models/SiteLayoutModel";
import {ElementDefaultValues} from "../../../Configs/ElementDefaults";
import {CommonModule} from "@angular/common";
import {transformSiteElementType} from "../../../Utility/TransformUtility";
import {TextPreviewComponent} from "../text-preview/text-preview.component";
import {ImagePreviewComponent} from "../image-preview/image-preview.component";
import {LinkPreviewComponent} from "../link-preview/link-preview.component";
import {HeadlinePreviewComponent} from "../headline-preview/headline-preview.component";

@Component({
  selector: 'site-preview',
  standalone: true,
  imports: [
    CommonModule,
    TextPreviewComponent,
    ImagePreviewComponent,
    LinkPreviewComponent,
    HeadlinePreviewComponent
  ],
  templateUrl: './site-preview.component.html',
  styleUrl: './site-preview.component.scss',
  encapsulation: ViewEncapsulation.ShadowDom
})
export class SitePreviewComponent implements OnInit {
  // Basic variables and component attributes
  @Input() siteLayout: SiteLayoutModel = {
    textColor: ElementDefaultValues.textColor,
    bgColor: ElementDefaultValues.bgColor,
    fontFamily: ElementDefaultValues.fontFamily,
    elements: []
  };

  ngOnInit(): void {

  }

  protected readonly transformSiteElementType = transformSiteElementType;
}
