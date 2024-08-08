import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import {from, Observable, of} from 'rxjs';
import { TokenService } from './token.service';
import {ApiConfig} from "../Configs/ApiConfig";
import {CryptoService} from "./crypto.service";
import {catchError, switchMap} from "rxjs/operators";

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
   * @param cryptoService
   */
  constructor(
    private http: HttpClient,
    private tokenService: TokenService,
    private cryptoService: CryptoService
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
   *
   * @param action
   * @param body
   */
  public post<T, R>(action: string, body: T): Observable<R> {
    if (!this.cryptoService.isInitialized) {
      return this.initializeCryptoService().pipe(
        switchMap(() => this.encryptAndPost(action, body))
      ) as Observable<R>;
    } else {
      return this.encryptAndPost(action, body);
    }
  }

  /**
   * Initializes the CryptoService by fetching the public key
   *
   * @returns {Observable<void>}
   */
  private initializeCryptoService(): Observable<void> {
    return this.get<{ publicKey: string }>('getpub').pipe(
      switchMap((response) => {
        if (response.publicKey) {
          return from(this.cryptoService.init(response.publicKey));
        } else {
          throw new Error('Public key not found in response');
        }
      }),
      catchError((error) => {
        console.error('Failed to initialize CryptoService', error);
        return of(undefined) as Observable<void>;
      })
    );
  }

  /**
   * Encrypts the body and sends a POST request
   *
   * @param action
   * @param body
   * @private
   */
  private encryptAndPost<T, R>(action: string, body: T): Observable<R> {
    return from(this.cryptoService.hybridEncrypt(JSON.stringify(body))).pipe(
      switchMap((encryptedBody: string) => {
        const postBody: { csrf_token: string | null; encryptedBody: string } = {
          csrf_token: this.tokenService.getCsrfToken(),
          encryptedBody: encryptedBody,
        };

        return this.http.post<R>(`${this.apiUrl}?action=${action}`, postBody, {
          withCredentials: true,
        });
      }),
      catchError((error) => {
        console.error('Failed to encrypt data', error);
        return of(undefined) as Observable<R>;
      })
    );
  }

}
