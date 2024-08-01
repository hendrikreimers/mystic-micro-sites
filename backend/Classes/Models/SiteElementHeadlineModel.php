<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementHeadline Model
 */
class SiteElementHeadlineModel {
  /**
   * @var int
   */
  public int $layout;

  /**
   * @var string
   */
  public string $value;

  /**
   * Constructor
   *
   * @param int $layout
   * @param string $value
   */
  public function __construct(int $layout, string $value) {
    $this->layout = $layout;
    $this->value = $value;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    return new self($data['layout'], $data['value']);
  }
}
