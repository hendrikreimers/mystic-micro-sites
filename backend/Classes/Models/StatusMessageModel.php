<?php
declare(strict_types=1);

namespace Models;

class StatusMessageModel {
  /**
   * Constructor
   *
   * @param int $statusCode
   * @param string $message
   */
  public function __construct(
    public int $statusCode = -1,
    public string $message = ''
  ) {}
}
