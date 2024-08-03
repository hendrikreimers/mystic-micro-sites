/**
  * Forces the correct return type.
  * It's magic ;-)
  *
  * @param elField
  */
import {SiteElements} from "../Interfaces/SiteLayoutInterface";
import {SiteElement, SiteLayout} from "../Models/SiteLayoutModel";

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
export function base64Encode(value: string | object | SiteLayout | SiteElements | SiteElement<any>): string {
  // Type guard to check if the value is an object and not null
  if (typeof value === 'object' && value !== null) {
    // Check if the value has a 'toJSON' method
    if (typeof (value as any).getJSONString === 'function') {
      value = (value as any).getJSONString();
    }
  }

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
