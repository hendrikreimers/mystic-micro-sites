<?php
declare(strict_types=1);

namespace Models\DOM;

use Enums\DOM\NodeTypesEnum;

/**
 * Represents a node in the Document Object Model (DOM).
 *
 * The DOMNode class provides a structure for representing nodes within a DOM tree,
 * including elements, text, and document nodes. It supports hierarchy and relationships
 * between nodes.
 */
class DOMNode {

  /**
   * Constructs a new DOMNode instance.
   *
   * @param NodeTypesEnum $nodeType The type of the DOM node.
   * @param string $nodeName The name of the node, typically the tag name for element nodes.
   * @param string $nodeValue Optional. The text content of the node (default is an empty string).
   * @param string|null $namespace Optional. The namespace of the node, if applicable.
   * @param array|null $attributes Optional. An array of NodeAttribute objects representing the node's attributes.
   * @param NodeList $childNodes Optional. An array of child DOMNode objects.
   * @param DOMNode|null $parent Optional. The parent DOMNode of this node.
   */
  public function __construct(
    public NodeTypesEnum $nodeType,
    public string $nodeName,
    public string $nodeValue = '',
    public ?string $namespace = null,
    public ?array $attributes = null,
    public NodeList $childNodes = new NodeList(),
    public ?DOMNode $parent = null
  ) {}

  /**
   * Adds a child node to this DOM node.
   *
   * This method appends a given DOMNode as a child of the current node, and
   * sets the parent property of the child node to the current node.
   *
   * @param DOMNode $childNode The child node to be added.
   */
  public function addChild(DOMNode $childNode): void {
    // Set the parent of the child node to this node.
    #$childNode->parent = &$this;

    // Append the child node to the childNodes array.
    $this->childNodes->addNode($childNode);
  }
}
