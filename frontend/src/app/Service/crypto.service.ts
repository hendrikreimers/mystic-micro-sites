import { Injectable } from '@angular/core';

/**
 * Service for encrypting data using the Web Crypto API
 *
 * It encrypts a random key with RSA PublicKey and symetric encryption for big data,
 * because RSA Encryption SHA-256 is limited.
 */
@Injectable({
  providedIn: 'root'
})
export class CryptoService {
  private publicKey: CryptoKey | null = null;

  /**
   * Returns true if the public key is loaded
   * @returns {boolean}
   */
  public get isInitialized(): boolean {
    return this.publicKey !== null;
  }

  /**
   * Initializes the service with the given public key
   *
   * @param {string} publicKeyPem - The public key in PEM format
   * @returns {Promise<void>}
   */
  async init(publicKeyPem: string): Promise<void> {
    const keyData: ArrayBuffer = this.pemToArrayBuffer(publicKeyPem);

    this.publicKey = await crypto.subtle.importKey(
      'spki',
      keyData,
      {
        name: 'RSA-OAEP',
        hash: { name: 'SHA-256' },
      },
      true,
      ['encrypt']
    );
  }

  /**
   * Encrypts the given data using hybrid encryption (AES for data, RSA for AES key)
   *
   * @param {string} value - The data to encrypt
   * @returns {Promise<string>} A JSON string containing the encrypted AES key, encrypted data, iv, and tag, all as base64 strings
   */
  async hybridEncrypt(value: string): Promise<string> {
    if (!this.publicKey) {
      throw new Error('Public key not initialized');
    }

    // Generate a random AES key
    const aesKey: Uint8Array = crypto.getRandomValues(new Uint8Array(32)); // 256-bit key
    const iv: Uint8Array = crypto.getRandomValues(new Uint8Array(12)); // IV for AES-GCM

    // Encrypt the data using AES-GCM
    const encoded: Uint8Array = new TextEncoder().encode(value);
    const aesKeyCryptoKey: CryptoKey = await crypto.subtle.importKey(
      'raw',
      aesKey,
      'AES-GCM',
      true,
      ['encrypt']
    );
    const encryptedData: ArrayBuffer = await crypto.subtle.encrypt(
      {
        name: 'AES-GCM',
        iv: iv,
        tagLength: 128 // Set the tag length for GCM
      },
      aesKeyCryptoKey,
      encoded
    );

    // Extract the authentication tag
    const encryptedDataArray: Uint8Array = new Uint8Array(encryptedData);
    const tag: Uint8Array = encryptedDataArray.slice(-16); // Last 16 bytes are the tag
    const data: Uint8Array = encryptedDataArray.slice(0, -16); // The rest is the actual data

    // Encrypt the AES key using RSA-OAEP
    const encryptedKey: ArrayBuffer = await crypto.subtle.encrypt(
      {
        name: 'RSA-OAEP',
      },
      this.publicKey,
      aesKey
    );

    // Convert the encrypted data, encrypted key, iv, and tag to base64
    const encryptedDataBase64: string = this.arrayBufferToBase64(data.buffer);
    const encryptedKeyBase64: string = this.arrayBufferToBase64(encryptedKey);
    const ivBase64: string = this.arrayBufferToBase64(iv.buffer);
    const tagBase64: string = this.arrayBufferToBase64(tag.buffer);

    // Create a JSON object containing the encrypted key, data, iv, and tag
    const result = {
      encryptedKey: encryptedKeyBase64,
      encryptedData: encryptedDataBase64,
      iv: ivBase64,
      tag: tagBase64
    };

    // Return the JSON object as a base64-encoded string
    return btoa(JSON.stringify(result));
  }

  /**
   * Converts a PEM formatted string to an ArrayBuffer
   *
   * @param {string} pem - The PEM formatted string
   * @returns {ArrayBuffer} The corresponding ArrayBuffer
   */
  private pemToArrayBuffer(pem: string): ArrayBuffer {
    const b64: string = pem
      .replace(/-----(BEGIN|END) PUBLIC KEY-----/g, '')
      .replace(/\s/g, '');

    const binary: string = window.atob(b64);
    const buffer: ArrayBuffer = new ArrayBuffer(binary.length);
    const view: Uint8Array = new Uint8Array(buffer);

    for (let i: number = 0; i < binary.length; i++) {
      view[i] = binary.charCodeAt(i);
    }

    return buffer;
  }

  /**
   * Converts an ArrayBuffer to a base64 string
   *
   * @param {ArrayBuffer} buffer - The ArrayBuffer to convert
   * @returns {string} The corresponding base64 string
   */
  private arrayBufferToBase64(buffer: ArrayBuffer): string {
    const binary: string = String.fromCharCode(...new Uint8Array(buffer));
    return window.btoa(binary);
  }
}
