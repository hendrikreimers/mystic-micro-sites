/**
 * Element Interface: Image
 */
export interface SiteElementImageInterface {
  imageData: string;

  toJSON(): SiteElementImageInterface;
  getLabel(): string;
}
