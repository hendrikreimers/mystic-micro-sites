/**
  * Forces the correct return type.
  * It's magic ;-)
  *
  * @param elField
  */
import {SiteElement} from "../Models/SiteLayoutModel";

/**
 * Forces the correct return type.
 * It's magic ;-)
 *
 * @param elField
 */
export function transformSiteElementType<T>(elField: SiteElement<unknown>): SiteElement<T> {
  return elField as SiteElement<T>;
}

/**
 * Base64 encode string or object (by stringify it)
 *
 * @param value
 */
export function base64Encode(value: string | object): string {
  return ( typeof value === "string" ) ? btoa(value) : btoa(JSON.stringify(value));
}

/**
 * Base64 Decode string and parse JSON if possible
 *
 * @param value
 */
export function base64Decode(value: string): string | object {
  try {
    // Decode string
    const decodedString: string = atob(value);

    try {
      // Try to parse it as an object
      return JSON.parse(decodedString);
    } catch {
      // Parse failed, so we return it as string
      return decodedString;
    }
  } catch {
    // Decoding failed
    return value;
  }
}
