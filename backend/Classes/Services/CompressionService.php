<?php
declare(strict_types=1);

namespace Services;

/**
 * String compression and decompressing service
 *
 */
class CompressionService {

  /**
   * Compress string
   *
   * @param string $input string to compress
   * @return string | false
   */
  public static function compressString(string $input): string | false {
    try {
      if (function_exists('bzcompress')) {
        $result = bzcompress($input, 9);
      } elseif (function_exists('gzcompress')) {
        $result = gzcompress($input, 9);
      } else {
        $result = http_inflate($input);
      }
    } catch (\Exception $e) {
      return false;
    }

    return (is_string($result) && !is_int($result)) ? $result : false;
  }

  /**
   * Decompress string
   *
   * @param string $input string to decompress
   * @return string | false
   */
  public static function decompressString(string $input): string | false {
    try {
      if (function_exists('bzdecompress')) {
        $result = bzdecompress($input);
      } elseif (function_exists('gzuncompress')) {
        $result = @gzuncompress($input);
      } else {
        $result = http_deflate($input);
      }

      return (is_string($result) && !is_int($result)) ? $result : false;
    } catch(\Exception $e) {
      return false;
    }
  }

}
