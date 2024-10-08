<?php
declare(strict_types=1);

namespace Utility;

/**
 * String Utility
 *
 * Helper functions for string handling and validation
 *
 */
class StringUtility {

  /**
   * Escapes a string
   *
   * @param string $string
   * @param bool $trim
   * @param bool $escapeString
   * @param bool $stripTags
   * @return string
   */
  public static function escapeString(string $string, bool $trim = false, bool $escapeString = true, bool $stripTags = true): string {
    if ( $stripTags ) {
      $string = strip_tags($string);
    }

    if ( $escapeString ) {
      $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    if ( $trim ) {
      $string = trim($string);
    }

    return $string;
  }

  /**
   * Regular Expression check for complex strings, not all chars are allowed
   *
   * @param string $string
   * @param $expr
   * @return bool
   */
  public static function validateString(string $string, $expr = '/^([0-9a-zA-Z#$%&^\.\[\]\{\}§\"!\*\`\´\'äöü\-:;µ\|\/\\=?+~,_]{4,})$/'): bool {
    return self::validateExpression($expr, $string);
  }

  /**
   * Validates API Action name
   * Only lowercase alphabet chars are allowed
   *
   * @param string $actionName
   * @param string $expr
   * @return bool
   */
  public static function validateActionName(string $actionName, string $expr = '/^([a-z_]{1,})$/'): bool {
    return self::validateExpression($expr, $actionName);
  }

  /**
   * Validates a string whether it's a correct URL
   *
   * @param string $url
   * @param string $expr
   * @return bool
   */
  public static function validateUrl(string $url, string $expr = '/^(https?:\/\/)?((([a-zA-Z0-9$-_@.&+!*"(),]|(%[0-9a-fA-F][0-9a-fA-F]))+(\:[a-zA-Z0-9$-_@.&+!*"(),]|(%[0-9a-fA-F][0-9a-fA-F]))*@)?(([0-9a-zA-Z]([0-9a-zA-Z-]{0,61}[0-9a-zA-Z])?\.)+[a-zA-Z]{2,6}\.?|[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}:[0-9a-fA-F]{1,4}|localhost|(([0-9]{1,3}\.){3}[0-9]{1,3}))(\:[0-9]{1,5})?((\/?)|([\/?]\S+))?)$/i;'): bool {
    return self::validateExpression($expr, $url);
  }

  /**
   * Helper function for the other validation checks
   * )
   * @param string $expr
   * @param string $string
   * @return bool
   */
  private static function validateExpression(string $expr, string $string): bool {
    return (bool)preg_match($expr, $string);
  }

  /**
   * Creates a secure hash from string
   *
   * @param string $string
   * @param string $secretKey
   * @param $hashAlgo
   * @param $binary
   * @return string
   */
  public static function hashString(string $string, string $secretKey, $hashAlgo = 'sha256', $binary = false): string {
    return hash_hmac($hashAlgo, $string, $secretKey, $binary);
  }

}
