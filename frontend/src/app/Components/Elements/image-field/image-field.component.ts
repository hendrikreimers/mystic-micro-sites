import {Component, OnInit} from '@angular/core';
import {SiteElementImage} from "../../../Models/SiteLayoutModel";
import {BaseFieldComponent} from "../../../Classes/BaseFieldComponent";
import {ElementDefaultValues} from "../../../Configs/ElementDefaults";
import {ImageProcessor} from "../../../Processors/ImageProcessor";
import {CommonModule} from "@angular/common";
import {Button} from "primeng/button";

@Component({
  selector: 'image-field',
  standalone: true,
  imports: [
    CommonModule,
    Button
  ],
  templateUrl: './image-field.component.html',
  styleUrl: './image-field.component.scss'
})
export class ImageFieldComponent extends BaseFieldComponent<SiteElementImage> implements OnInit {
  // Define basics
  public processedImage: string | undefined; // Temporary image storage before save button pressed
  public errorMessage: string | undefined; // Error message variable

  /**
   * Constructor
   *
   * Dependency injections of needed classes
   */
  constructor(
    private imageProcessor: ImageProcessor = new ImageProcessor()
  ) {
    super();
  }

  /**
   * Initializes the headlineValue
   * It's important to react on the button events and not instant model changes.
   *
   */
  ngOnInit(): void {
    this.processedImage = ElementDefaultValues.imageData;
  }

  /**
   * Event handler if a file is selected to rescale the image and load it into a preview
   *
   * @param event
   */
  async onFileSelected(event: any): Promise<void> {
    const file: File = event.target.files[0];

    if (file) {
      try {
        this.processedImage = await this.imageProcessor.processImage(file);
        this.errorMessage = undefined;
      } catch (error) {
        this.errorMessage = (error as Error).message;
        this.processedImage = undefined;
      }
    }
  }

  /**
   * Changes the config value
   *
   * @param e
   */
  protected onSaveValue(e: MouseEvent): void {
    if ( this.processedImage ) {
      this.elementConfig.element.imageData = this.processedImage;
      this.onElementChange();
    }
  }

  /**
   * Resets the input value
   *
   * @param e
   */
  protected onCancelValue(e: MouseEvent): void {
    this.processedImage = this.elementConfig.element.imageData;
  }

  /**
   * Checks if the header value changed to enable/disable the save/cancel buttons
   *
   */
  protected areButtonsEnabled(): boolean {
    return this.processedImage === this.elementConfig.element.imageData;
  }
}
