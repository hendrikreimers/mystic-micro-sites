import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {SiteElementImage} from "../../../Models/SiteLayoutModel";

@Component({
  selector: 'image-preview',
  standalone: true,
  imports: [],
  templateUrl: './image-preview.component.html',
  styleUrl: './image-preview.component.scss'
})
export class ImagePreviewComponent extends BasePreviewComponent<SiteElementImage> {

}
