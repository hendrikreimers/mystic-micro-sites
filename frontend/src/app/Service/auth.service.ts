import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { TokenService } from './token.service';
import {ApiService} from "./api.service";

/**
 * Authentication Service
 *
 * Handles the Auth Mechanisms
 */
@Injectable({
  providedIn: 'root'
})
export class AuthService {

  /**
   * Constructor to inject the additional services
   *
   * @param apiService
   * @param tokenService
   */
  constructor(
    private apiService: ApiService,
    private tokenService: TokenService
  ) {}

  /**
   * Login by username and password
   *
   * @param username
   * @param password
   */
  login(username: string, password: string): Observable<{ token: string, refreshToken: string }> {
    return this.apiService.post<{username: string, password: string},{ token: string, refreshToken: string }>(
      'login',
      { username, password }
    ).pipe(
      tap((response: {token: string, refreshToken: string}) => {
        this.tokenService.setToken(response.token);
        this.tokenService.setRefreshToken(response.refreshToken);
      })
    );
  }

  /**
   * Logout and clear everything
   *
   */
  logout(): Observable<void> {
    return this.apiService.post<null, void>(
      'logout',
      null
    ).pipe(
      tap(() => {
        this.tokenService.clearTokens();
      })
    );
  }

  /**
   * JWT Validation
   */
  validateToken(): Observable<any> {
    return this.apiService.get<any>('validate', true, true);
  }

  /**
   * Refreshes the JWT
   *
   */
  refreshToken(): Observable<{ token: string }> {
    return this.apiService.post<{ refreshToken: string | null },{ token: string }>(
      'refresh',
      { refreshToken: this.tokenService.getRefreshToken() }
    ).pipe(
      tap((response: {token: string}) => {
        this.tokenService.setToken(response.token);
      })
    );
  }

  /**
   * Retreives the CSRF token for POST Actions
   *
   */
  getCsrfToken(): Observable<{ csrfToken: string }> {
    return this.apiService.get<{ csrfToken: string }>('get_csrf_token').pipe(
      tap(response => {
        this.tokenService.setCsrfToken(response.csrfToken);
      })
    );
  }

}
