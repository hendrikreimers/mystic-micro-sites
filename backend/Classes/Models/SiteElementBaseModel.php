<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteModelBaseModel
 *
 * The other SiteModels will extend from it to get these methods
 */
abstract class SiteElementBaseModel {
  /**
   * Dynamically sets properties from an array.
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    $instance = new static(); // Create a new instance of the called class

    foreach ($data as $key => $value) {
      if (property_exists($instance, $key)) {
        $instance->$key = $value;
      }
    }

    return $instance;
  }
}
