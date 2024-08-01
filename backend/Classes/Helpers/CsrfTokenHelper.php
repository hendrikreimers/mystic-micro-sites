<?php
declare(strict_types=1);

namespace Helpers;

use Symfony\Component\HttpFoundation\Request;

class CsrfTokenHelper {

  /**
   * @var ResponseHelper
   */
  private ResponseHelper $response;

  /**
   * @var Request
   */
  private Request $request;

  /**
   * Constructor
   *
   * @param ResponseHelper $responseHelper
   * @param Request $request
   * @throws \Random\RandomException
   */
  public function __construct(ResponseHelper &$responseHelper, Request &$request) {
    $this->response = &$responseHelper;
    $this->request = &$request;

    $this->initCsrfToken();
    $this->checkCsrfToken();
  }

  /**
   * Initializes a new CSRF token if not already exists
   *
   * @return void
   * @throws \Random\RandomException
   */
  public function initCsrfToken(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }

  /**
   * Checks a CSRF token and breaks if not valid
   *
   * @return void
   */
  public function checkCsrfToken(): true {
    $csrfToken = json_decode($this->request->getContent())->csrf_token ?? '';

    if ($this->request->isMethod('POST') && !$this->validateCsrfToken($csrfToken)) {
      $this->response->sendStatusError('INVALID_CSRF_TOKEN');
      $this->response->send();
      exit();
    } else return true;
  }

  /**
   * Returns the CSRF token
   *
   * @return string
   */
  public function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = $this->generateCsrfToken();
    }

    return $_SESSION['csrf_token'];
  }

  /**
   * Deletes the CSRF Token and removes the session
   *
   * @return void
   */
  public function deleteCsrfToken(): void {
    unset($_SESSION['csrf_token']);
    session_unset();
    session_destroy();
  }

  /**
   * Generates CSRF Token
   *
   * @return string
   * @throws \Random\RandomException
   */
  private function generateCsrfToken(): string {
    return bin2hex(random_bytes(32));
  }

  /**
   * Validates CSRF Token
   *
   * @param string $token
   * @return bool
   */
  private function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
  }

}
