import { HttpHandlerFn, HttpInterceptorFn, HttpRequest } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, switchMap } from 'rxjs/operators';
import { throwError } from 'rxjs';
import { AuthService } from '../Service/auth.service';
import { TokenService } from '../Service/token.service';

/**
 * Authentication Interceptor for HTTP Requests.
 * It's a kind of middleware ;-)
 *
 * In this case it will send the token as an authorization header and tries to refresh that token if it's not valid.
 *
 * @todo Optimize error handling, especially max retries!
 *
 * @param req
 * @param next
 */
export const authInterceptor: HttpInterceptorFn = (req: HttpRequest<any>, next: HttpHandlerFn) => {
  const authService: AuthService = inject(AuthService);
  const tokenService: TokenService = inject(TokenService);

  const token: string | null = tokenService.getToken();
  let authReq: HttpRequest<any> = req;

  if (token) {
    authReq = req.clone({
      setHeaders: { Authorization: `Bearer ${token}` }
    });
  }

  return next(authReq).pipe(
    catchError(error => {
      if (error.status === 403) { // Stop retry if refresh token wasn't accepted
        tokenService.clearTokens();
        return throwError(() => undefined);
      } else if (error.status === 401 && token) { // Try to refresh the token
        return authService.refreshToken().pipe(
          switchMap((response: {token:string}) => {
            tokenService.setToken(response.token);
            const newAuthReq = req.clone({
              setHeaders: { Authorization: `Bearer ${response.token}` }
            });
            return next(newAuthReq);
          }),
          catchError(err => {
            tokenService.clearTokens();
            return throwError(() => err);
          })
        );
      }
      return throwError(() => error);
    })
  );
};
