<?php
declare(strict_types=1);

namespace Models;

use Enums\FontFamiliesEnums;
use Models\SiteElements\SiteElementModel;

/**
 * SiteLayout Model
 */
class SiteLayoutModel {
  /**
   * @var string
   */
  public string $textColor;

  /**
   * @var string
   */
  public string $bgColor;

  /**
   * @var FontFamiliesEnums
   */
  public FontFamiliesEnums $fontFamily;

  /**
   * @var array<SiteElementModel>
   */
  public array $elements;

  /**
   * Constructor
   *
   * @param string $textColor
   * @param string $bgColor
   * @param FontFamiliesEnums $fontFamily
   * @param array $elements
   */
  public function __construct(
    string $textColor,
    string $bgColor,
    FontFamiliesEnums $fontFamily,
    array $elements
  ) {
    $this->textColor = $textColor;
    $this->bgColor = $bgColor;
    $this->fontFamily = $fontFamily;
    $this->elements = $elements;
  }

  /**
   * Array/JSON to model transformation
   *
   * @param array $data
   * @return self
   */
  public static function fromArray(array $data): self {
    $fontFamily = FontFamiliesEnums::from($data['fontFamily']);

    $elements = array_map(
      fn($elementData) => SiteElementModel::fromArray($elementData),
      $data['elements']
    );

    return new self(
      $data['textColor'],
      $data['bgColor'],
      $fontFamily,
      $elements
    );
  }
}

