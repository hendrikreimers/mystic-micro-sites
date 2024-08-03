<?php
declare(strict_types=1);

namespace Models;

class JwtTokensModel {
  /**
   * Constructor
   *
   * @param string $token
   * @param string | null $refreshToken
   */
  public function __construct(
    public string $token = '',
    public ?string $refreshToken = null
  ) {}
}
