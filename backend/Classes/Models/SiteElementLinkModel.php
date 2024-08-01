<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementLink Model
 */
class SiteElementLinkModel {
  public string $title;
  public string $href;

  /**
   * Constructor
   *
   * @param string $title
   * @param string $href
   */
  public function __construct(string $title, string $href) {
    $this->title = $title;
    $this->href = $href;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    return new self($data['title'], $data['href']);
  }
}
