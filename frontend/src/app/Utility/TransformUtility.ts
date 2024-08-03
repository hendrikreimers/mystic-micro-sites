/**
  * Forces the correct return type.
  * It's magic ;-)
  *
  * @param elField
  */
import {SiteElementInterface} from "../Interfaces/SiteLayoutModel";

/**
 * Forces the correct return type.
 * It's magic ;-)
 *
 * @param elField
 */
export function transformSiteElementType<T>(elField: SiteElementInterface<unknown>): SiteElementInterface<T> {
  return elField as SiteElementInterface<T>;
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

/**
 * HTML String encoding
 *
 * @param value
 */
export function htmlEncode(value: string): string {
  // Use mapping to convert predefined entities
  const predefinedEntities: { [key: string]: string } = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
    //' ': '&nbsp;', // optional: encodes space as non-breaking space
  };

  // All other characters that are not in the ASCII range and have no predefined entities
  // are converted into numeric HTML entities (e.g. © becomes &#169;)
  return value.replace(/[\u00A0-\u9999<>&"']/g, (char): string => {
    if (predefinedEntities[char]) {
      return predefinedEntities[char];
    }
    return `&#${char.charCodeAt(0)};`;
  });
}

/**
 * HTML String decoding
 *
 * @param input
 */
export function htmlDecode(input: string): string {
  // The fastest and best way is to use the browsers integrated functionality
  const parser: DOMParser = new DOMParser();
  const doc: Document = parser.parseFromString(input, 'text/html');
  return doc.documentElement.textContent || '';
}
