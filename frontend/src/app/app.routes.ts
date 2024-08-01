import { Routes } from '@angular/router';
import {AuthGuard} from "./Guards/auth.guard";
import {DashboardComponent} from "./Components/Pages/dashboard/dashboard.component";
import {LoginComponent} from "./Components/Pages/login/login.component";
import {NewSiteComponent} from "./Components/Pages/new-site/new-site.component";
import {LogoutComponent} from "./Components/Pages/logout/logout.component";
import {NewSiteSaveComponent} from "./Components/Pages/new-site-save/new-site-save.component";

export const routes: Routes = [
  {
    path: 'login',
    component: LoginComponent
  },
  {
    path: 'logout',
    component: LogoutComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'dashboard',
    component: DashboardComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'new-site',
    component: NewSiteComponent,
    canActivate: [AuthGuard]
  },
  {
    path: 'new-site-save',
    component: NewSiteSaveComponent,
    canActivate: [AuthGuard]
  },
  {
    path: '',
    redirectTo: '/login',
    pathMatch: 'full'
  },
  {
    path: '**',
    redirectTo: '/login'
  }
];
