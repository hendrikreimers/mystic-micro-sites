import {Component, OnInit} from '@angular/core';
import {NavigationEnd, Router, RouterLink, RouterOutlet} from '@angular/router';
import {ButtonDirective} from "primeng/button";
import {CommonModule} from "@angular/common";
import {filter} from "rxjs";
import {GeneralConfig} from "./Configs/GeneralConfig";

/**
 * App Component
 *
 * It's the beginning of everything ;-)
 */
@Component({
  selector: 'app-root',
  standalone: true,
    imports: [
      CommonModule,
      RouterOutlet,
      ButtonDirective,
      RouterLink
    ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent implements OnInit {
  // Template variables
  protected title: string = 'MysticMicroSites';
  public showLogin: boolean = false;

  /**
   * Constructor
   *
   * with dependency injections
   *
   * @param router
   */
  constructor(
    private router: Router
  ) {}

  /**
   * Angular's component initialization
   *
   * Watch on route changes to switch the visibility of the login button
   */
  ngOnInit(): void {
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe((event: NavigationEnd): void => {
      this.showLogin = !GeneralConfig.noLoginButtonOnRoutes.includes(event.url);
    });
  }
}
