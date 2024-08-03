<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementHeadline Model
 */
class SiteElementHeadlineModel extends SiteElementBaseModel {
  /**
   * Constructor
   *
   * @param int $layout
   * @param string $value
   */
  public function __construct(
    public int $layout = 1,
    public string $value = ''
  ) {}
}
