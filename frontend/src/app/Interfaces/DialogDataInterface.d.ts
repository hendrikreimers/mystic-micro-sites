/**
 * Possible button types
 */
export type ButtonPressedTypes = 'saveBtn' | 'cancelBtn' | 'closeBtn' | 'importBtn';

/**
 * Event Emitter Data Object
 */
export interface DialogEventDataInterface {
  buttonPressed: ButtonPressedTypes;
  args?: {[key: string]: string}
}
