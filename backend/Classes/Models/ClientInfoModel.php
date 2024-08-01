<?php
declare(strict_types=1);

namespace Models;

class ClientInfoModel {
  public string $clientIp;
  public string $clientUserAgent;

  public function __construct(string $clientIp, string $clientUserAgent) {
    $this->clientIp = $clientIp;
    $this->clientUserAgent = $clientUserAgent;
  }
}
