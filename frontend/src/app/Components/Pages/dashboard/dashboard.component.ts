import { Component } from '@angular/core';
import {RouterLink} from "@angular/router";
import {ButtonDirective} from "primeng/button";

/**
 * PAGE: Dashboard
 *
 */
@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    RouterLink,
    ButtonDirective
  ],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent {

}
