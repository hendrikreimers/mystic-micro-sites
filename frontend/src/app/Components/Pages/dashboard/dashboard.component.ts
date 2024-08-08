import { Component } from '@angular/core';
import {Router, RouterLink} from "@angular/router";
import {Button, ButtonDirective} from "primeng/button";
import {SaveDialogComponent} from "../../Molecules/save-dialog/save-dialog.component";
import {ImportDialogComponent} from "../../Molecules/import-dialog/import-dialog.component";
import {DialogEventDataInterface} from "../../../Interfaces/DialogDataInterface";
import {base64Encode} from "../../../Utility/TransformUtility";
import {SiteLayout} from "../../../Models/SiteLayoutModel";
import {GlobalContextStorageService} from "../../../Service/globalContextStorage.service";

/**
 * PAGE: Dashboard
 *
 */
@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    RouterLink,
    ButtonDirective,
    SaveDialogComponent,
    ImportDialogComponent,
    Button
  ],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent {
  protected showImportDialog: boolean = false;

  /**
   * Constructor for dependency Injections
   *
   * @param router
   * @param globalStorageService
   */
  constructor(
    private router: Router,
    private globalStorageService: GlobalContextStorageService
  ) {}

  protected onImportDialogButtonClick(dialogData: DialogEventDataInterface): void {
    this.showImportDialog = false;

    // Only do the magic if the save button is really pressed and there's a master pass entered
    if ( dialogData.buttonPressed === 'importBtn' && this.globalStorageService.getStorageValue('siteLayoutImported').length > 0 ) {
      // Redirect to save page
      this.router.navigate(['/new-site']);
    }
  }
}
