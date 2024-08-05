<?php
declare(strict_types=1);

namespace Models\DOM;

/**
 * Represents an HTML attribute for a DOM element.
 *
 * The NodeAttribute class encapsulates the name and value of an HTML attribute,
 * allowing it to be easily managed and manipulated within a DOM structure.
 */
class NodeAttribute {
  /**
   * Constructs a new NodeAttribute instance.
   *
   * @param string $attributeName The name of the HTML attribute.
   * @param string|null $attributeValue The value of the HTML attribute, or null if no value is set.
   */
  public function __construct(
    public string $attributeName,
    public ?string $attributeValue = null
  ) {}
}
