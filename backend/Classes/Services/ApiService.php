<?php
declare(strict_types=1);

namespace Services;

use Helpers\CsrfTokenHelper;
use Helpers\JwtHelper;
use Helpers\ResponseHelper;
use Utility\StringUtlity;
use Symfony\Component\HttpFoundation\Request;

/**
 * Magic API Service
 *
 * Automatically matches the method to the calling method or fallbacks to a 404
 * and handles the request, response, etc.
 *
 * The Actions (Endpoints) are private methods called "endpoint_ACTION_NAME" and will be magically called
 * by the __call() method. If and endpoint method doesn't exist it will fall back to the not found (404).
 *
 * Usage:
 *
 * ```
 * // Initialize Class Instances
 * $request = Request::createFromGlobals(); // Symfony Request Handler
 * $response = new ResponseHelper(); // ResponseHelper
 * $rateLimit = new RateLimitHelper($response, $request); // Initialize rate limiter
 * $csrfTokenHelper = new CsrfTokenHelper($response, $request); // Initialize CSRF Token Handler
 * $auth = new JwtHelper(); // Initialize JWT Helper
 * $encryptionService = new Services\EncryptionService(); // Initialize encryption service
 *
 * // Initialize the API Endpoint Service
 * $apiService = new ApiService($request, $response, $encryptionService, $auth, $csrfTokenHelper);
 *
 * // Get the requested action
 * $action = $request->query->get('action', '');
 *
 * // Call requested API Endpoint (Magic call)
 * $apiService->{$action}();
 * ```
 */
class ApiService {

  /**
   * @var ResponseHelper
   */
  private ResponseHelper $response;

  /**
   * @var Request
   */
  private Request $request;

  /**
   * @var EncryptionService
   */
  private EncryptionService $encryptionService;

  /**
   * @var JwtHelper
   */
  private JwtHelper $jwtHelper;

  /**
   * @var CsrfTokenHelper
   */
  private CsrfTokenHelper $csrfTokenHelper;

  /**
   * Constructor
   *
   * Get used classes by dependency injections
   */
  public function __construct(
    Request &$request,
    ResponseHelper &$response,
    EncryptionService &$encryptionService,
    JwtHelper &$jwtHelper,
    CsrfTokenHelper &$csrfTokenHelper
  ) {
    // Inject the needed classes as reference (&$...), no instance copy.
    $this->request = &$request;
    $this->response = &$response;
    $this->encryptionService = &$encryptionService;
    $this->jwtHelper = &$jwtHelper;
    $this->csrfTokenHelper = &$csrfTokenHelper;
  }

  /**
   * Magic method caller
   *
   * @param $name
   * @param $arguments
   * @return void
   */
  public function __call($name, $arguments) {
    // Try to call the method based on action name or fall back to not found endpoint method.
    // Disallow direct caller names (like endpoint_...)
    if (
      !StringUtlity::validateActionName($name) || // Wrong action name
      strstr($name, 'endpoint_') || // Try to access them directly
      $name === '404' || // Direct call Not found
      $name === 'not_found' || // Direct call not found
      !method_exists($this, 'endpoint_' . strtoupper($name)) // Endpoint method not available
    ) {
      // Send 404 (Not found) error
      $this->endpoint_NOT_FOUND();
    } else {
      // Call the requested and existing endpoint
      $this->{'endpoint_' . $name}(...$arguments);
    }

    // Finally send the response
    $this->response->send();
  }

  /**
   * Sends the general API Header
   *
   * @return void
   */
  public function sendApiHeader(): void {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: ' . CORS_ALLOWED_ORIGINS);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
  }

  /**
   * Endpoint Handler: 404
   * Fallback if nothing else is available
   *
   * @return void
   */
  private function endpoint_NOT_FOUND(): void {
    $this->response->sendStatusError('NOT_FOUND');
  }

  /**
   * Endpoint Handler: login
   *
   * Validates USER and PASS and logins the user (creates a JWT etc)
   *
   * @return void
   */
  private function endpoint_LOGIN(): void {
    // Decode the POST Request
    $postData = json_decode($this->request->getContent(), true);

    // Initialize username and password
    $username = StringUtlity::escapeString(($postData['username'] ?? ''), true);
    $password = trim(strip_tags($postData['password'] ?? ''));

    // Security regular expression (simple regexp to take care that there are only allowed characters in user and pass)
    if ( !StringUtlity::validateString($username) && !StringUtlity::validateString($password) ) {
      $username = '';
      $password = '';
    }

    // Validate CSRF Token (checkCsrfToken will break if its not valid)
    // @todo Maybe useless, because the CSRF check will be checked already on class construct
    if ( $this->csrfTokenHelper->checkCsrfToken() ) {
      if ( // Credentials (Username / Password) validation
        $username === USERNAME &&
        $this->encryptionService->verifyPassword($password, MASTER_PASSWORD_HASH, SECRET_KEY)
      ) {
        // Generate JWTs (Token and RefreshToken)
        $tokenArray = $this->jwtHelper->generateTokens($username);

        // Set to return body
        $this->response->setContent(json_encode(array(
          'token' => $tokenArray->token,
          'refreshToken' => $tokenArray->refreshToken
        )));
      } else {
        // Validation failed response
        $this->response->sendStatusError('INVALID_CREDENTIALS');
      }
    }
  }

  /**
   * Endpoint Handler: validate
   *
   * Validates the JWT
   *
   * @return void
   */
  private function endpoint_VALIDATE(): void {
    $authHeader = $this->request->headers->get('Authorization');

    $data = $this->jwtHelper->validateTokenHeader($authHeader);

    if ($data && !$data['isRefreshToken']) {
      $this->response->setContent(json_encode(['data' => $data]));
    } else {
      $this->response->sendStatusError('INVALID_TOKEN');
    }
  }

  /**
   * Endpoint Handler: refresh
   *
   * Refreshes the JWT
   *
   * @return void
   */
  private function endpoint_REFRESH(): void {
    $postData = json_decode($this->request->getContent(), true);
    $refreshToken = $postData['refreshToken'] ?? '';

    $data = $this->jwtHelper->validateTokenHeader($refreshToken, isRefreshToken: true);

    if ($data && $data['isRefreshToken']) {
      $tokens = $this->jwtHelper->generateTokens($data['username']);
      $token = $tokens->token;

      $this->response->setContent(json_encode(['token' => $token]));
    } else {
      $this->response->sendStatusError('INVALID_REFRESH_TOKEN');
    }
  }

  /**
   * Endpoint Handler: get_csrf_token
   *
   * Delivers a CSRF Token for POST requests
   *
   * @return void
   */
  private function endpoint_GET_CSRF_TOKEN(): void {
    $csrfToken = $this->csrfTokenHelper->getCsrfToken();
    $this->response->setContent(json_encode(['csrfToken' => $csrfToken]));
  }

  /**
   * Endpoint Handler: logout
   *
   * @return void
   */
  private function endpoint_LOGOUT(): void {
    $this->csrfTokenHelper->deleteCsrfToken();
    $this->response->setContent(json_encode(['message' => 'Logged out']));
  }

  /**
   * Endpoint Handler: Save
   *
   * @return void
   */
  private function endpoint_SAVE(): void {
    $authHeader = $this->request->headers->get('Authorization');
    $postData = json_decode($this->request->getContent(), true);

    // Data and Security validation
    // @todo CSRF Token validation Maybe useless, because the CSRF check will be checked already on class construct
    if (
      isset($authHeader) &&
      $this->csrfTokenHelper->checkCsrfToken() &&
      $this->jwtHelper->validateTokenHeader($authHeader) &&
      is_array($postData) &&
      isset($postData['passwordEncoded'], $postData['siteLayoutEncoded'])
    ) {
      $masterPassword = base64_decode($postData['passwordEncoded']) ?? '';
      $siteLayoutEncoded = $postData['siteLayoutEncoded'];

      if ( !$this->encryptionService->verifyPassword($masterPassword, MASTER_PASSWORD_HASH, SECRET_KEY) ) {
        $this->response->sendStatusError('INVALID_DATA');
      } else {
        // Create a random password
        $randomPass = $this->encryptionService->generateSecretKey();

        // Shorten the Password for the URL
        $keyA = substr($randomPass, 0, 10);
        $keyB = substr($randomPass, 10, -10);
        $keyC = substr($randomPass, -10);

        // Save part of the password for the header file (this will be the rest of the shortened password)
        $headerData = EnigmaBase64Service::enigmaBase64Encode(json_encode([
          'keyB' => $keyB,
          'endTime' => ''
        ]));

        // Generate the password Key
        $urlKey = urlencode(EnigmaBase64Service::enigmaBase64Encode($keyA . $keyC));

        // Generate a filename
        $filename = FileService::generateUniqueFilename();

        // Create the final URL
        $url = BASE_URL . '/view/' . $urlKey . '/' . str_replace('.enc', '', $filename);

        // Encrypt the data
        $encryptedData = $this->encryptionService->encryptData(
          base64_decode($siteLayoutEncoded), // Will be re-encoded in the encryption function
          [$randomPass, $masterPassword],
          SECRET_KEY
        );

        // Encrypt for the header file
        $encryptedHeader = $this->encryptionService->encryptDataWithKeyPair($headerData);

        // Save everything
        FileService::saveFileContent($filename, $encryptedData);
        FileService::saveFileContent($filename . '.h', $encryptedHeader);

        // Return URL as result
        $this->response->setContent(json_encode(['url' => $url]));
      }
    } else {
      // Send error
      $this->response->sendStatusError('INVALID_DATA');
    }
  }

}
