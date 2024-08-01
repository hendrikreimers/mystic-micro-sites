import {Injectable} from "@angular/core";
import {max} from "rxjs";
import {GeneralConfig} from "../Configs/GeneralConfig";

/**
 * Image Processor
 *
 * Processes Images (Scale and Quality) using Browser capabilities.
 *
 */
@Injectable({
  providedIn: 'root'
})
export class ImageProcessor {
  /**
   * Dynamically creates an Image Element
   *
   * @param file
   * @private
   */
  private createImage(file: File): Promise<HTMLImageElement> {
    return new Promise((resolve, reject): void => {
      // Check mime-type
      if (!GeneralConfig.allowedMimeTypes.includes(file.type)) {
        reject(new Error(`Unsupported file type. Only ${GeneralConfig.allowedMimeTypes.join(', ')} are allowed.`));
        return;
      }

      // Initialize FileReader to access the local file directly
      const reader: FileReader = new FileReader();

      // Read file content as Image Element
      reader.onload = (event: any): void => {
        const img: HTMLImageElement = new Image();

        img.onload = () => resolve(img);
        img.onerror = (err: string | Event) => reject(err);
        img.src = event.target.result;
      };

      // Error handling
      reader.onerror = (err: ProgressEvent<FileReader>) => reject(err);

      // Result
      reader.readAsDataURL(file);
    });
  }

  /**
   * Image Scaling for HTMLImageElement
   *
   * @param img
   * @param maxWidth
   * @private
   */
  private scaleImage(img: HTMLImageElement, maxWidth: number = 428): HTMLCanvasElement {
    // Initialize basics
    const canvas: HTMLCanvasElement = document.createElement('canvas');
    let width: number = img.width;
    let height: number = img.height;

    // Calculate target size base on maxWidth
    if (width > maxWidth) {
      height = Math.round((height * maxWidth) / width);
      width = maxWidth;
    }

    // Set target size to temporary canvas
    canvas.width = width;
    canvas.height = height;

    // Draw image on canvas with new size
    const ctx: CanvasRenderingContext2D | null = canvas.getContext('2d');
    if (ctx) {
      ctx.drawImage(img, 0, 0, width, height);
    }

    // Return result
    return canvas;
  }

  /**
   * Image processing (Main function
   *
   * @param file
   * @param maxWidth
   * @param quality
   */
  async processImage(file: File, maxWidth: number = 428, quality: number = 0.7): Promise<string> {
    const img: HTMLImageElement = await this.createImage(file);
    const canvas: HTMLCanvasElement = this.scaleImage(img, maxWidth);

    // Transform image to BASE64 encoded data url
    return canvas.toDataURL(file.type, file.type === 'image/jpeg' ? quality : undefined);
  }
}
