<?php
declare(strict_types=1);

namespace Utility;

/**
 * Object Utility methods
 *
 */
class ObjectUtility
{
  /**
   * Convert a stdClass object to an associative array recursively.
   *
   * @param mixed $data The data to be converted.
   * @return mixed The converted array or the original value.
   */
  public static function objectToArray(mixed $data): mixed
  {
    if (is_object($data)) {
      $data = get_object_vars($data);
    }

    if (is_array($data)) {
      return array_map([self::class, __FUNCTION__], $data);
    }

    return $data;
  }
}
