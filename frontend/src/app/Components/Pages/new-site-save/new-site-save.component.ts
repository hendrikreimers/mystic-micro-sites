import {Component, OnInit, ViewEncapsulation} from '@angular/core';
import {GlobalContextStorageService} from "../../../Service/globalContextStorage.service";
import {CommonModule} from "@angular/common";
import {RouterLink} from "@angular/router";
import {ProgressSpinnerModule} from "primeng/progressspinner";
import {ApiService} from "../../../Service/api.service";
import {Observable} from "rxjs";
import {Button} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";

@Component({
  selector: 'app-new-site-save',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    ProgressSpinnerModule,
    Button,
    InputTextModule
  ],
  templateUrl: './new-site-save.component.html',
  styleUrl: './new-site-save.component.scss',
  encapsulation: ViewEncapsulation.None
})
export class NewSiteSaveComponent implements OnInit {
  protected isFalseCall: boolean = false;
  protected isSaving: boolean = true;
  protected errorMsg: string = '';
  protected resultUrl: string = '';

  /**
   * Constructor
   *
   * @param apiService
   * @param globalStorageService
   */
  constructor(
    private apiService: ApiService,
    private globalStorageService: GlobalContextStorageService
  ) {}

  /**
   * Angular component initialization method
   *
   * Start the saving process if all data is available
   */
  ngOnInit(): void {
    const masterPasswordEncoded: string | undefined = this.globalStorageService.getStorageValue('saveSite.passwordEncoded');
    const siteLayoutEncoded: string | undefined = this.globalStorageService.getStorageValue('saveSite.siteLayoutEncoded');

    // Show error message if something is missing
    if ( !masterPasswordEncoded || !siteLayoutEncoded ) {
      // Change template layout switches
      this.isFalseCall = true;
      this.isSaving = false;

      // Reset global storage
      this.globalStorageService.setStorageValue('saveSite', null);
    } else {
      this.isFalseCall = false;
      this.isSaving = true;

      this.saveMicroSite(masterPasswordEncoded, siteLayoutEncoded);
    }
  }

  private saveMicroSite(passwordEncoded: string, siteLayoutEncoded: string): boolean {
    let result: boolean = false;

    this.apiService.post<{passwordEncoded: string, siteLayoutEncoded: string}, { url: string }>(
      'save',
      {
        passwordEncoded,
        siteLayoutEncoded
      }
    ).subscribe({
      next: ( response: {url: string} ) => {
        this.globalStorageService.setStorageValue('saveSite', null);
        result = true;
        this.resultUrl = response.url;
      },
      error: (err) => {
        this.errorMsg = err.error.message;
        result = false;
        this.resultUrl = '';

        this.globalStorageService.setStorageValue('saveSite.passwordEncoded', null);
      }
    });

    this.isSaving = false;

    return result;
  }

  /**
   * Copies text to clipboard
   *
   * @param inputElement
   * @private
   */
  protected copyToClipboard(inputElement: HTMLInputElement): void {
    const clipBoard: Clipboard = navigator.clipboard;
    clipBoard.writeText(inputElement.value).then(() => {
      alert("Copied text to clipboard");
    });
  }
}
