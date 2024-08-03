<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementImage Model
 */
class SiteElementImageModel extends SiteElementBaseModel {
  /**
   * Constructor
   *
   * @param string $imageData
   */
  public function __construct(
    public string $imageData = ''
  ) {}
}
