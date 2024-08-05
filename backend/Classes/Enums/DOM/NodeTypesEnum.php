<?php
declare(strict_types=1);

namespace Enums\DOM;

/**
 * HTMLParser DOMNode Types
 */
/**
 * Enum representing different types of DOM nodes.
 *
 * NodeTypesEnum is used to define constants for the various node types
 * within a DOM structure, such as documents, elements, text, and doctype nodes.
 */
enum NodeTypesEnum: string {
  /**
   * Represents the document node type.
   */
  case DOCUMENT = 'document';

  /**
   * Represents the element node type.
   */
  case ELEMENT_NODE = 'element';

  /**
   * Represents the text node type.
   */
  case TEXT_NODE = 'text';

  /**
   * Represents the doctype node type.
   */
  case DOCTYPE = 'doctype';
}
