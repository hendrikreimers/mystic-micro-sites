import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {NgSwitch, NgSwitchCase} from "@angular/common";
import {SiteElementHeadline} from "../../../Models/SiteLayoutModel";

@Component({
  selector: 'headline-preview',
  standalone: true,
  imports: [
    NgSwitchCase,
    NgSwitch
  ],
  templateUrl: './headline-preview.component.html',
  styleUrl: './headline-preview.component.scss'
})
export class HeadlinePreviewComponent extends BasePreviewComponent<SiteElementHeadline> {

}
