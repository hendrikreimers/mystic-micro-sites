import { Injectable } from '@angular/core';

/**
 * Token Service
 *
 * Handles the JWT and CSRF Tokens into a local storage
 *
 */
@Injectable({
  providedIn: 'root'
})
export class TokenService {
  // Token identifiers for local storage
  private tokenKey: string = 'auth-token';
  private refreshTokenKey: string = 'auth-refresh-token';
  private csrfToken: string = 'csrf-token';

  /**
   * set JWT
   *
   * @param token
   */
  setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }

  /**
   * get JWT
   */
  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  /**
   * set JWT Refresh Token
   *
   * @param refreshToken
   */
  setRefreshToken(refreshToken: string): void {
    localStorage.setItem(this.refreshTokenKey, refreshToken);
  }

  /**
   * Returns Refresh Token (JWT)
   *
   */
  getRefreshToken(): string | null {
    return localStorage.getItem(this.refreshTokenKey);
  }

  /**
   * Set CSRF Token
   *
   * @param csrfToken
   */
  setCsrfToken(csrfToken: string): void {
    localStorage.setItem(this.csrfToken, csrfToken);
  }

  /**
   * Returns CSRF Token
   *
   */
  getCsrfToken(): string | null {
    return localStorage.getItem(this.csrfToken);
  }

  /**
   * Removes all tokens from the browser (client)
   *
   */
  clearTokens(): void {
    localStorage.removeItem(this.csrfToken);
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.refreshTokenKey);
  }
}
