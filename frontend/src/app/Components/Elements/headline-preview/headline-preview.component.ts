import { Component } from '@angular/core';
import {BasePreviewComponent} from "../../../Classes/BasePreviewComponent";
import {SiteElementHeadline} from "../../../Models/SiteLayoutModel";
import {NgSwitch, NgSwitchCase} from "@angular/common";

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
