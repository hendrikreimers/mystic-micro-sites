import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {SiteElementTextInterface} from "../../../Interfaces/SiteLayoutModel";

@Component({
  selector: 'text-preview',
  standalone: true,
  imports: [],
  templateUrl: './text-preview.component.html',
  styleUrl: './text-preview.component.scss'
})
export class TextPreviewComponent extends BasePreviewComponent<SiteElementTextInterface> {

}
