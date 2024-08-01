<?php
declare(strict_types=1);

namespace Models;

class JwtTokensModel {
  /**
   * @var string
   */
  public string $token;

  /**
   * @var string|null
   */
  public ?string $refreshToken;

  public function __construct(string $token, ?string $refreshToken) {
    $this->token = $token;
    $this->refreshToken = $refreshToken;
  }
}
