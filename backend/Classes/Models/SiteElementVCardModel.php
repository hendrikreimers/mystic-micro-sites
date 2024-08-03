<?php
declare(strict_types=1);

namespace Models;

/**
 * SiteElementLink Model
 */
class SiteElementVCardModel extends SiteElementBaseModel {
  /**
   * Constructor
   *
   * @param string $firstName
   * @param string $lastName
   * @param string $address
   * @param string $email
   * @param string $website
   * @param string $phone
   * @param string $mobile
   * @param string $companyName
   */
  public function __construct(
    public string $firstName = '',
    public string $lastName = '',
    public string $address = '',
    public string $email = '',
    public string $website = '',
    public string $phone = '',
    public string $mobile = '',
    public string $companyName = ''
  ) {}
}
