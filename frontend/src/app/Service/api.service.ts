import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { TokenService } from './token.service';
import {ApiConfig} from "../Configs/ApiConfig";

/**
 * API Call Service
 *
 * Use this to send GET/POST requests with CSRF Token and other credentials like JWT etc.
 *
 */
@Injectable({
  providedIn: 'root'
})
export class ApiService {

  // API URL
  private apiUrl: string = ApiConfig.apiUrl;

  /**
   * Constructor
   * Service Injection
   *
   * @param http
   * @param tokenService
   */
  constructor(
    private http: HttpClient,
    private tokenService: TokenService
  ) {}

  /**
   * Generative HTTP GET Method
   *
   * @param action
   * @param withCredentials
   * @param withToken
   */
  public get<R>(action: string, withCredentials: boolean = true, withToken: boolean = true): Observable<R> {
    const getHeaders: {Authorization: string} | {} = ( withToken ) ? { Authorization: `Bearer ${this.tokenService.getToken()}` } : {};

    return this.http.get<R>(`${this.apiUrl}?action=${action}`, {
      headers: getHeaders,
      withCredentials: withCredentials
    });
  }

  /**
   * Generative HTTP POST Method
   * @param action
   * @param body
   */
  public post<T,R>(action: string, body: T): Observable<R> {
    const postBody: T & {csrf_token: string | null} = Object.assign({}, body, { csrf_token: this.tokenService.getCsrfToken() });

    return this.http.post<R>(
      `${this.apiUrl}?action=${action}`,
      postBody,
      { withCredentials: true }
    )
  }

}
