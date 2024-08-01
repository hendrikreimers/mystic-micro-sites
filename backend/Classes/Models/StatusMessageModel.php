<?php
declare(strict_types=1);

namespace Models;

class StatusMessageModel {
  /**
   * @var int
   */
  public int $statusCode;

  /**
   * @var string
   */
  public string $message;

  public function __construct(int $statusCode, string $message) {
    $this->statusCode = $statusCode;
    $this->message = $message;
  }
}
