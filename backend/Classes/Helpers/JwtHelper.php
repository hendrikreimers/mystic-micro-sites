<?php
declare(strict_types=1);

namespace Helpers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Models\ClientInfoModel;
use Models\JwtTokensModel;
use Models\StatusMessageModel;
use Enums\MessageEnum;

/**
 * JSON Web Token Helper Class
 *
 * Helps you to generate and validate JWTs
 */
class JwtHelper {

  /**
   * @var Request
   */
  private Request $request;

  /**
   * @var string|false
   */
  private string $privateKey;

  /**
   * @var string|false
   */
  private string $publicKey;

  /**
   * @var int
   */
  private int $expirationTime;

  /**
   * @var int
   */
  private int $refreshExpirationTime;

  /**
   * Constructor
   */
  public function __construct() {
    $this->request = Request::createFromGlobals();

    $this->privateKey = file_get_contents(JWT_PRIVATE_KEY_PATH);
    $this->publicKey = file_get_contents(JWT_PUBLIC_KEY_PATH);
    $this->expirationTime = (int)JWT_EXPIRATION_TIME;
    $this->refreshExpirationTime = (int)JWT_REFRESH_EXPIRATION_TIME;
  }

  /**
   * Generate the JWT Tokens (Token and RefreshToken)
   *
   * @param string $username
   * @return JwtTokensModel
   */
  public function generateTokens(string $username, bool $withRefreshToken = true): JwtTokensModel {
    $clientInfo = $this->getUserInfo();
    $token = $this->generateToken($username, $clientInfo->clientIp, $clientInfo->clientUserAgent);

    if ( $withRefreshToken ) {
      $refreshToken = $this->generateToken($username, $clientInfo->clientIp, $clientInfo->clientUserAgent, true);
    } else $refreshToken = null;

    return new JwtTokensModel($token, $refreshToken);
  }

  /**
   * Validates a token by given Authorization header
   *
   * @param string $authorizationHeader
   * @return false|array
   */
  public function validateTokenHeader(string $authorizationHeader, bool $isRefreshToken = false): array|false {
    $clientInfo = $this->getUserInfo();

    if ( !$isRefreshToken ) {
      $matches = [];
      preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches);
      $token = $matches[1] ?? '';
    } else $token = &$authorizationHeader;

    // Validate token
    $data = $this->validateToken($token, $clientInfo->clientIp, $clientInfo->clientUserAgent);

    return ( $data !== null ) ? $data : false;
  }

  /**
   * Refreshes a JWT Token
   *
   * @param string $refreshToken
   * @return JwtTokensModel|StatusMessageModel
   */
  public function refreshToken(string $refreshToken): JwtTokensModel|false {
    $clientInfo = $this->getUserInfo();
    $data = $this->validateToken($refreshToken, $clientInfo->clientIp, $clientInfo->clientUserAgent);

    if ($data && $data['isRefreshToken']) {
      $token = $this->generateTokens($data['username'], false)->token;
      return new JwtTokensModel($token, null);
    } else {
      return false;
    }
  }

  /**
   * Generates a JWT Token
   *
   * @param string $username
   * @param string $ipAddress
   * @param string $userAgent
   * @param bool $isRefreshToken
   * @return string
   */
  private function generateToken(string $username, string $ipAddress, string $userAgent, bool $isRefreshToken = false): string {
    $payload = [
      'iss' => 'yourdomain.com',
      'aud' => 'yourdomain.com',
      'iat' => time(),
      'nbf' => time(),
      'exp' => time() + ($isRefreshToken ? $this->refreshExpirationTime : $this->expirationTime),
      'data' => [
        'username' => $username,
        'ip' => $ipAddress,
        'ua' => $userAgent,
        'isRefreshToken' => $isRefreshToken
      ]
    ];

    return JWT::encode($payload, $this->privateKey, 'RS256');
  }

  /**
   * Validates a JWT Token
   *
   * @param string $token
   * @param string $currentIp
   * @param string $currentUserAgent
   * @return array|null
   */
  private function validateToken(string $token, string $currentIp, string $currentUserAgent): ?array {
    try {
      $decoded = JWT::decode($token, new Key($this->publicKey, 'RS256'));
      $data = (array) $decoded->data;

      if ($data['ip'] !== $currentIp || $data['ua'] !== $currentUserAgent) {
        throw new Exception('Token does not match current IP address or User Agent');
      }

      return $data;
    } catch (Exception $e) {
      return null;
    }
  }

  /**
   * Gets user details for token hardening
   *
   * @return object
   */
  private function getUserInfo(): ClientInfoModel {
    $clientIp = $this->request->getClientIp();
    $clientUserAgent = $this->request->headers->get('User-Agent');

    return new ClientInfoModel(
      $clientIp,
      $clientUserAgent
    );
  }

}
