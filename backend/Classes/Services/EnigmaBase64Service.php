<?php
declare(strict_types = 1);

namespace Services;

class EnigmaBase64Service {

  /**
   * @var int
   */
  private static int $defaultEnigmaShift = 5;

  /**
   * Enigma like char shifting
   *
   * @param string $input
   * @param int $shift
   * @return string
   */
  private static function charShift(string $input, int $shift): string {
    $output = '';
    $input_length = strlen($input);

    for ($i = 0; $i < $input_length; $i++) {
      $char = ord($input[$i]);
      $shifted_char = $char + $shift;

      // Handle wrapping of characters
      if ($shifted_char > 255) {
        $shifted_char -= 256;
      } elseif ($shifted_char < 0) {
        $shifted_char += 256;
      }

      $output .= chr($shifted_char);
    }

    return $output;
  }

  private static function getShift() {
    return ( defined("ENIGMA_SHIFT") ) ? intval(ENIGMA_SHIFT) : self::$defaultEnigmaShift;
  }

  /**
   * URL friendly base64 encoding with enigma like char shifting
   *
   * @param string $input
   * @return string
   */
  public static function enigmaBase64Encode(string $input): string {
    $encodedInput = base64_encode(CompressionService::compressString($input));
    $shifted = self::charShift($encodedInput, self::getShift());
    return urlencode(base64_encode($shifted));
  }

  /**
   * URL friendly base64 decoding with reversing enigma like char shifting
   *
   * @param string $input
   * @return string|false
   */
  public static function enigmaBase64Decode(string $input): string | false {
    try {
      $decoded = base64_decode(urldecode($input));
      $unshifted = self::charShift($decoded, -self::getShift());
      return CompressionService::decompressString(base64_decode($unshifted));
    } catch(\Exception $e) {
      return false;
    }
  }

}
