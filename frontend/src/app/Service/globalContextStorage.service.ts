import { Injectable } from '@angular/core';

/**
 * GlobalContextStorage Service
 *
 * A service that acts as a singleton global context storage.
 * It provides methods to set and get values using path-based keys.
 *
 */
@Injectable({
  providedIn: 'root'
})
export class GlobalContextStorageService {
  // The internal storage object
  private storage: Record<string, any> = {};

  /**
   * Constructor
   *
   */
  constructor() {}

  /**
   * Sets a value in the storage at the specified path.
   * If the path does not exist, it creates the necessary nested objects.
   *
   * @param {string} path - The dot-separated path where the value should be set.
   * @param {any} value - The value to set at the specified path.
   */
  setStorageValue(path: string, value: any): void {
    // Split the path into an array of keys
    const keys: string[] = path.split('.');
    let current: Record<string,any> = this.storage;

    // Iterate over each key in the path
    keys.forEach((key: string, index: number): void => {
      if (index === keys.length - 1) {
        // If it's the last key, set the value
        current[key] = value;
      } else {
        // If the current key does not exist or is not an object, initialize it as an empty object
        if (!current[key] || typeof current[key] !== 'object') {
          current[key] = {};
        }
        // Move to the next level in the nested object
        current = current[key];
      }
    });
  }

  /**
   * Retrieves a value from the storage at the specified path.
   * If the path does not exist, it returns undefined.
   *
   * @param {string} path - The dot-separated path from which the value should be retrieved.
   * @returns {any} - The value at the specified path or undefined if the path does not exist.
   */
  getStorageValue(path: string): any {
    // Split the path into an array of keys
    const keys: string[] = path.split('.');
    let current: Record<string,any> = this.storage;

    // Iterate over each key in the path
    for (const key of keys) {
      if (current[key] === undefined) {
        // Return undefined if the key is not found
        return undefined;
      }
      // Move to the next level in the nested object
      current = current[key];
    }

    // Return the found value
    return current;
  }
}
