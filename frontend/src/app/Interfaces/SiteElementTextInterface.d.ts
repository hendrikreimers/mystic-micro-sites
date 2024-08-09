/**
 * Element Interface: Text
 */
export interface SiteElementTextInterface {
  value: string;

  toJSON(): SiteElementTextInterface;
  getLabel(): string;
}
