<?php
declare(strict_types=1);

namespace Models\SiteElements;

/**
 * SiteElementText Model
 */
class SiteElementTextModel extends SiteElementBaseModel {
  /**
   * Constructor
   *
   * @param string $value
   */
  public function __construct(
    public string $value = ''
  ) {}
}
