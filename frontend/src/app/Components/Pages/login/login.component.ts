import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { PasswordModule } from 'primeng/password';
import { CommonModule } from '@angular/common';
import {AuthService} from "../../../Service/auth.service";
import {TokenService} from "../../../Service/token.service";

/**
 * PAGE: Login
 *
 */
@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, ButtonModule, InputTextModule, PasswordModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  // Template variables
  protected username = '';
  protected password = '';

  /**
   * Constructor
   * Inject required services
   *
   * @param authService
   * @param tokenService
   * @param router
   */
  constructor(
    private authService: AuthService,
    private tokenService: TokenService,
    private router: Router
  ) {}

  /**
   * Angular's Component initialization
   *
   */
  ngOnInit(): void {
    // Retrieve CSRF-Token from backend
    this.authService.getCsrfToken().subscribe({
      next: (response) => {
        this.tokenService.setCsrfToken(response.csrfToken);
      },
      error: (err) => {
        console.error('Failed to get CSRF token', err);
      }
    });
  }

  /**
   * Login call
   *
   */
  login(): void {
    this.authService.login(this.username, this.password).subscribe({
      next: (response) => {
        this.tokenService.setToken(response.token);
        this.tokenService.setRefreshToken(response.refreshToken);
        this.router.navigate(['/dashboard']);
      },
      error: (err) => {
        console.error('Login failed', err);
      }
    });
  }
}
