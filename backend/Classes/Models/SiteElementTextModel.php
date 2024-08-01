<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementText Model
 */
class SiteElementTextModel {
  public string $value;

  /**
   * Constructor
   *
   * @param string $value
   */
  public function __construct(string $value) {
    $this->value = $value;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    return new self($data['value']);
  }
}
