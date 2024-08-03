import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {SiteElementLinkInterface} from "../../../Interfaces/SiteLayoutInterface";

@Component({
  selector: 'link-preview',
  standalone: true,
  imports: [],
  templateUrl: './link-preview.component.html',
  styleUrl: './link-preview.component.scss'
})
export class LinkPreviewComponent extends BasePreviewComponent<SiteElementLinkInterface> {

}
