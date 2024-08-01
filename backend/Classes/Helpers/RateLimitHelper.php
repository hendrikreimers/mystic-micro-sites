<?php
declare(strict_types=1);

namespace Helpers;

use Symfony\Component\HttpFoundation\Request;

/**
 * Request rate limit helper
 *
 * Just a basic version not perfect. But if something breaks in JS or something it's helpful.
 * In case of real attacks it's not really helpful, i know... because they just can delete the session cookie ;-)
 *
 */
class RateLimitHelper {

  /**
   * @var ResponseHelper
   */
  private ResponseHelper $response;

  /**
   * @var Request
   */
  private Request $request;

  /**
   * @var string
   */
  private $rateLimitKey = 'global_rate_limit';

  /**
   * @var int
   */
  private $currentTime = 0;

  /**
   * Constructor
   *
   * @param ResponseHelper $responseHelper
   * @param Request $request
   */
  public function __construct(ResponseHelper &$responseHelper, Request &$request) {
    $this->response = &$responseHelper;
    $this->request = &$request;

    // Get current time
    $this->currentTime = time();

    // Rate limit handling
    $this->initSession();
    $this->initRateLimit();
    $this->checkAndHandleRateLimit();
  }

  private function initSession(): void {
    // Initialize session if not already done
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Initialize rate limiting
   *
   * @return void
   */
  private function initRateLimit(): void {
    // Set initial rate limit for the user if not set
    if (!isset($_SESSION[$this->rateLimitKey])) {
      $_SESSION[$this->rateLimitKey] = ['count' => 0, 'timestamp' => $this->currentTime];
    }
  }

  private function checkAndHandleRateLimit(): void {
    // Get current config
    $rateLimit = $_SESSION[$this->rateLimitKey];

    // Reset rate limit if last request is outside the limit window
    if ($this->currentTime - $rateLimit['timestamp'] > (int)RATE_LIMIT_WINDOW) {
      $rateLimit['count'] = 0;
      $rateLimit['timestamp'] = $this->currentTime;
    }

    // If limit reached in a too short time, break it!
    if ($rateLimit['count'] >= (int)RATE_LIMIT) {
      $this->breakRequest();
    }

    // Count up and update session data
    $rateLimit['count'] += 1;
    $this->updateRateLimit($rateLimit);
  }

  private function updateRateLimit($rateLimit): void {
    $_SESSION[$this->rateLimitKey] = $rateLimit;
  }

  private function breakRequest(): void {
    $this->response->sendStatusError('TOO_MANY_REQUESTS');
    $this->response->send();
    exit();
  }

}
