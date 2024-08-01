<?php
declare(strict_types = 1);

namespace Enums;

enum MessageEnum: string {
  case INVALID_TOKEN = 'Invalid token';
  case INVALID_REFRESH_TOKEN = 'Invalid refresh token';
  case NOT_FOUND = 'Not found';
  case INVALID_CREDENTIALS = 'Invalid credentials';
  case TOO_MANY_REQUESTS = 'Too many requests';
  case INVALID_CSRF_TOKEN = 'Invalid CSRF token';
  case INVALID_DATA = 'Invalid data';
}
