<?php
declare(strict_types=1);

namespace Models;

use Enums\SiteElementsTypesEnum;

/**
 * SiteElement Model
 */
class SiteElementModel {
  /**
   * @var string
   */
  public string $uid;

  /**
   * @var SiteElementsTypesEnum
   */
  public SiteElementsTypesEnum $type;

  /**
   * @var SiteElementHeadlineModel | SiteElementTextModel | SiteElementImageModel | SiteElementLinkModel | SiteElementVCardModel | mixed
   */
  public SiteElementHeadlineModel | SiteElementTextModel | SiteElementImageModel | SiteElementLinkModel | SiteElementVCardModel $element;

  /**
   * Constructor
   *
   * @param string $uid
   * @param SiteElementsTypesEnum $type
   * @param mixed $element
   */
  public function __construct(
    string $uid,
    SiteElementsTypesEnum $type,
    mixed $element
  ) {
    $this->uid = $uid;
    $this->type = $type;
    $this->element = $element;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    $type = SiteElementsTypesEnum::from($data['type']);

    $element = match ($type) {
      SiteElementsTypesEnum::Headline => SiteElementHeadlineModel::fromArray($data['element']),
      SiteElementsTypesEnum::Text => SiteElementTextModel::fromArray($data['element']),
      SiteElementsTypesEnum::Image => SiteElementImageModel::fromArray($data['element']),
      SiteElementsTypesEnum::Link => SiteElementLinkModel::fromArray($data['element']),
      SiteElementsTypesEnum::VCard => SiteElementVCardModel::fromArray($data['element'])
    };

    return new self(
      $data['uid'],
      $type,
      $element
    );
  }
}
