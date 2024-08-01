import {Component, OnInit} from '@angular/core';
import {AuthService} from "../../../Service/auth.service";
import {RouterLink} from "@angular/router";

/**
 * PAGE: Logout
 *
 */
@Component({
  selector: 'app-logout',
  standalone: true,
  imports: [
    RouterLink
  ],
  templateUrl: './logout.component.html',
  styleUrl: './logout.component.scss'
})
export class LogoutComponent implements OnInit {

  /**
   * Constructor
   * Inject required services
   *
   * @param authService
   */
  constructor(
    private authService: AuthService,
  ) {}

  /**
   * Angular's component initialization method
   *
   */
  ngOnInit() {
    // Immediately logout
    this.logout();
  }

  /**
   * Logout the user
   *
   * @private
   */
  private logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        // ...
      },
      error: (err) => {
        console.error('Logout failed', err);
      }
    });
  }

}
