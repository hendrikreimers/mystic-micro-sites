<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementImage Model
 */
class SiteElementImageModel {
  /**
   * @var string
   */
  public string $imageData;

  /**
   * Constructor
   *
   * @param string $imageData
   */
  public function __construct(string $imageData) {
    $this->imageData = $imageData;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    return new self($data['imageData']);
  }
}
