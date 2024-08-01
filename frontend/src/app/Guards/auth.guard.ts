import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import {TokenService} from "../Service/token.service";
import {AuthService} from "../Service/auth.service";
import {map, Observable, of} from "rxjs";
import {catchError} from "rxjs/operators";

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(
    private authService: AuthService,
    private tokenService: TokenService,
    private router: Router
  ) {}

  canActivate(): Observable<boolean> {
    return this.authService.validateToken().pipe(
      map(response => {
        if (response && response.data) {
          return true;
        } else {
          this.router.navigate(['/login']);
          return false;
        }
      }),
      catchError((err) => {
        this.tokenService.clearTokens();
        this.router.navigate(['/login']);
        return of(false);
      })
    );
  }
}
