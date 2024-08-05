<?php
declare(strict_types=1);

namespace Models\SiteElements;

/**
 * SiteElementLink Model
 */
class SiteElementLinkModel extends SiteElementBaseModel {
  /**
   * Constructor
   *
   * @param string $title
   * @param string $href
   */
  public function __construct(
    public string $title = '',
    public string $href = ''
  ) {}
}
