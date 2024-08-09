/**
  * Forces the correct return type.
  * It's magic ;-)
  *
  * @param elField
  */
import {SiteLayout} from "../Models/SiteLayoutModel";
import {SiteElements} from "../Types/SiteElementsType";
import {SiteElement} from "../Models/SiteElementModel";

/**
 * Forces the correct return type.
 * It's magic ;-)
 *
 * @param elField
 */
export function transformSiteElementType<T extends SiteElements>(elField: SiteElement<SiteElements>): SiteElement<T> {
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
 * Checks if a string contains HTML entities.
 *
 * @param {string} input - The string to check.
 * @returns {boolean} - True if the string contains HTML entities, false otherwise.
 */
export function containsHtmlEntities(input: string): boolean {
  // Regular expression to detect HTML entities
  const entityPattern: RegExp = /&[a-zA-Z0-9#]+;/g;
  return entityPattern.test(input);
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

/**
 * Checks if the JSON string has a newline character every 120 characters except possibly the last segment.
 *
 * @param {string} jsonString - The JSON string with line breaks.
 * @returns {boolean} - True if the string has newlines every 120 characters, false otherwise.
 */
export function hasNewlinesEvery120Chars(jsonString: string): boolean {
  // Split the string by newlines
  const segments: string[] = jsonString.split('\n');

  // Check each segment except the last one
  for (let i = 0; i < segments.length - 1; i++) {
    if (segments[i].length !== 120) {
      return false;
    }
  }

  // The check for the last segment is not needed as it might be less than 120 characters
  return true;
}

/**
 * Removes newline characters from a JSON string that occur every 120 characters.
 *
 * @param {string} jsonString - The JSON string with line breaks.
 * @returns {string} - The JSON string without the unwanted line breaks.
 */
export function removeNewlinesEvery120Chars(jsonString: string): string {
  if (hasNewlinesEvery120Chars(jsonString)) {
    // Regular expression to match a newline character preceded by 120 characters
    const regex: RegExp = /(.{120})\n/g;

    // Replace the matched pattern with just the 120 characters (removing the newline)
    return jsonString.replace(regex, '$1');
  } else {
    //console.error("The JSON string does not have newlines every 120 characters.");
    return jsonString;
  }
}
