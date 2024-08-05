import {Component, OnInit, ViewEncapsulation} from '@angular/core';
import {GlobalContextStorageService} from "../../../Service/globalContextStorage.service";
import {CommonModule} from "@angular/common";
import {RouterLink} from "@angular/router";
import {ProgressSpinnerModule} from "primeng/progressspinner";
import {ApiService} from "../../../Service/api.service";
import {Button} from "primeng/button";
import {InputTextModule} from "primeng/inputtext";
import {QRCodeModule} from "angularx-qrcode";
import {base64Encode} from "../../../Utility/TransformUtility";
import {SiteLayout} from "../../../Models/SiteLayoutModel";

@Component({
  selector: 'app-new-site-save',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    ProgressSpinnerModule,
    Button,
    InputTextModule,
    QRCodeModule
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
    const siteLayout: SiteLayout | undefined = this.globalStorageService.getStorageValue('saveSite.siteLayout');
    let siteLayoutEncoded: string | undefined = undefined;

    // Transform siteLayout for transmission
    if ( siteLayout ) {
      siteLayoutEncoded = base64Encode(siteLayout.getJSONString());
    }

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
      next: ( response: {url: string} ): void => {
        this.globalStorageService.setStorageValue('saveSite', null);
        result = true;
        this.resultUrl = response.url.replace('/view/', '/view/#'); // REPLACE Workaround due issues on sending hashtag via backend
      },
      error: (err): void => {
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

  /**
   * Downloads the QR Code as image
   *
   * @protected
   */
  protected saveQrCodeImage(qrcode: HTMLElement): void {
    // Get by the parent element the canvas element by using native JS selection
    const qrcodeImageStr: string | undefined | null = qrcode.querySelector("canvas")?.toDataURL("image/png")

    if ( qrcodeImageStr !== undefined && qrcodeImageStr !== null ) {
      // converts base 64 encoded image to blobData
      let blobData: Blob = this.convertBase64ToBlob(qrcodeImageStr);

      // saves as image
      const blob: Blob = new Blob([blobData], { type: "image/png" });
      const url: string = window.URL.createObjectURL(blob);
      const link: HTMLAnchorElement = document.createElement("a");
      link.href = url;

      // name of the file
      link.download = "mysticMicroSite-QRCode.png";
      link.click();
    }
  }

  /**
   * Converts base64 string to blob for downloadable data
   *
   * @see https://github.com/Cordobo/angularx-qrcode/blob/main/projects/demo-app/src/app/app.component.ts
   *
   * @param Base64Image
   * @private
   */
  private convertBase64ToBlob(Base64Image: string): Blob {
    // split into two parts
    const parts: string[] = Base64Image.split(";base64,");

    // hold the content type
    const imageType = parts[0].split(":")[1];

    // decode base64 string
    const decodedData: string = window.atob(parts[1]);

    // create unit8Array of size same as row data length
    const uInt8Array: Uint8Array = new Uint8Array(decodedData.length);

    // insert all character code into uint8Array
    for (let i: number = 0; i < decodedData.length; ++i) {
      uInt8Array[i] = decodedData.charCodeAt(i);
    }

    // return blob image after conversion
    return new Blob([uInt8Array], { type: imageType });
  }
}
