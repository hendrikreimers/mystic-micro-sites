/**
 * Element Interface: Link
 */
export interface SiteElementLinkInterface {
  title: string;
  href: string;

  toJSON(): SiteElementLinkInterface;
}
