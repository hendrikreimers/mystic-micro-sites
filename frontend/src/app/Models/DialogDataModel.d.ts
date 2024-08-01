/**
 * Possible button types
 */
export type ButtonPressedTypes = 'saveBtn' | 'cancelBtn' | 'closeBtn';

/**
 * Event Emitter Data Object
 */
export interface DialogEventData {
  buttonPressed: ButtonPressedTypes;
  args?: {[key: string]: string}
}
