import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {SiteElementVcard} from "../../../Models/SiteElementVcardModel";
import {CommonModule} from "@angular/common";
import {Button} from "primeng/button";

@Component({
  selector: 'vcard-preview',
  standalone: true,
  imports: [
    CommonModule,
    Button
  ],
  templateUrl: './vcard-preview.component.html',
  styleUrl: './vcard-preview.component.scss'
})
export class VcardPreviewComponent extends BasePreviewComponent<SiteElementVcard> {

}
