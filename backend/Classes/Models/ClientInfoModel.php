<?php
declare(strict_types=1);

namespace Models;

class ClientInfoModel {
  /**
   * Constructor
   *
   * @param string $clientIp
   * @param string $clientUserAgent
   */
  public function __construct(
    public string $clientIp = '',
    public string $clientUserAgent = ''
  ) {}
}
