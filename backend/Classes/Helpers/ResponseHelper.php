<?php
declare(strict_types=1);

namespace Helpers;

use Enums\MessageEnum;
use Enums\StatusCodeEnum;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelper extends Response {

  /**
   * Sends a typical response message in case of errors
   *
   * @param string $type
   * @return void
   */
  public function sendStatusError(string $type): void {
    if ( $type === 'INVALID_CREDENTIALS' ) {
      $this->setStatusCode(StatusCodeEnum::UNAUTHORIZED_ACCESS->value);
      $this->setContent(json_encode(['message' => MessageEnum::INVALID_CREDENTIALS->value]));
    } elseif ( $type === 'INVALID_TOKEN' ) {
      $this->setStatusCode(StatusCodeEnum::UNAUTHORIZED_ACCESS->value);
      $this->setContent(json_encode(['message' => MessageEnum::INVALID_TOKEN->value]));
    } elseif ( $type === 'INVALID_REFRESH_TOKEN' ) {
      $this->setStatusCode(StatusCodeEnum::FORBIDDEN->value); // Different status code for better frontend handling
      $this->setContent(json_encode(['message' => MessageEnum::INVALID_REFRESH_TOKEN->value]));
    } elseif ( $type === 'TOO_MANY_REQUESTS' ) {
      $this->setStatusCode(StatusCodeEnum::TOO_MANY_REQUESTS->value);
      $this->setContent(json_encode(['message' => MessageEnum::TOO_MANY_REQUESTS->value]));
    } elseif ( $type === 'NOT_FOUND' ) {
      $this->setStatusCode(StatusCodeEnum::NOT_FOUND->value);
      $this->setContent(json_encode(['message' => MessageEnum::NOT_FOUND->value]));
    } elseif ( $type === 'INVALID_CSRF_TOKEN' ) {
      $this->setStatusCode(StatusCodeEnum::FORBIDDEN->value);
      $this->setContent(json_encode(['message' => MessageEnum::INVALID_CSRF_TOKEN->value]));
    } elseif ( $type === 'INVALID_DATA' ) {
      $this->setStatusCode(StatusCodeEnum::NOT_ACCEPTABLE->value);
      $this->setContent(json_encode(['message' => MessageEnum::INVALID_DATA->value]));
    }
  }

}
