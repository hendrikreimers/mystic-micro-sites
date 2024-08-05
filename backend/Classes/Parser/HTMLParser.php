<?php
declare(strict_types=1);

namespace Parser;

use Enums\DOM\NodeTypesEnum;
use Models\DOM\DOMNode;
use Models\DOM\NodeAttribute;

/**
 * Simple self-made HTML DOM
 *
 * It's not as efficient as Symfony's Crawler,
 * but it's for educational reasons to understand how parsers work.
 */
class HTMLParser
{
  /**
   * @var DOMNode The root DOMNode representing the parsed document
   */
  public DOMNode $dom;

  /**
   * @var string[] Tags which are self-closing tags
   */
  private array $selfClosingTags = ['img', 'br', 'hr', 'input', 'meta', 'link'];

  /**
   * @var array[] Tags which can have optional closing Tags
   */
  private array $optionalClosingTags = [
    'li' => ['li'],
    'dt' => ['dt', 'dd'],
    'dd' => ['dt', 'dd'],
    'p' => ['p'],
  ];

  /**
   * @var string[] Tags whose content is always parsed as text (like script tags containing dynamic HTML rendering)
   */
  private array $contentAsTextTags = ['script', 'style'];

  /**
   * Parses HTML to a DOM Node Object
   *
   * @param string $html
   * @throws \Exception
   */
  public function parseHTML(string $html): void {
    $tokens = $this->tokenize($html);
    $this->parseTokens($tokens);
  }

  /**
   * Calls the internal buildHTML method and returns the indented HTML string
   *
   * @return string
   */
  public function saveHTML(): string {
    return $this->buildHTML($this->dom);
  }

  /**
   * Converts a DOMNode and its children into an indented HTML string representation.
   *
   * This function recursively processes a DOMNode object to build a well-formatted
   * HTML string. It handles different node types, including document, element, text,
   * and doctype nodes, applying indentation for readability.
   *
   * @param DOMNode $node The DOMNode to be converted into HTML.
   * @param int $indentLevel The current level of indentation (default is 0).
   * @return string The HTML string representation of the node and its children.
   */
  private function buildHTML(DOMNode $node, int $indentLevel = 0): string {
    // Initialize the HTML output string.
    $html = '';

    // Determine the indentation based on the current indentation level.
    // Each level of indentation is represented by two spaces.
    $indent = ($indentLevel > 1) ? str_repeat('  ', $indentLevel - 1) : '';

    // Switch based on the node type to handle different node kinds appropriately.
    switch ($node->nodeType) {
      case NodeTypesEnum::DOCUMENT:
        // For a document node, recursively call buildHTML on all child nodes.
        foreach ($node->childNodes ?? [] as $childNode) {
          $html .= $this->buildHTML($childNode, $indentLevel);
        }
        break;

      case NodeTypesEnum::ELEMENT_NODE:
        // Build the opening tag of the element, including the namespace prefix if present.
        $namespacePrefix = $node->namespace ? $node->namespace . ':' : '';
        $html .= $indent . '<' . $namespacePrefix . $node->nodeName;

        // Add attributes to the element.
        if ($node->attributes) {
          foreach ($node->attributes as $attribute) {
            $html .= ' ' . $attribute->attributeName;
            if ($attribute->attributeValue !== null) {
              $html .= '="' . $attribute->attributeValue . '"';
            }
          }
        }

        // Check if the tag is self-closing.
        $selfClosing = $this->isSelfClosingTag($node->nodeName);
        if ($selfClosing) {
          // For self-closing tags, add a slash before closing.
          $html .= ' />' . PHP_EOL;
        } else {
          // Otherwise, close the opening tag.
          $html .= '>' . PHP_EOL;

          // Recursively process all child nodes, increasing the indentation level.
          foreach ($node->childNodes ?? [] as $childNode) {
            $html .= $this->buildHTML($childNode, $indentLevel + 1);
          }

          // Add the closing tag for the element.
          $html .= $indent . '</' . $namespacePrefix . $node->nodeName . '>' . PHP_EOL;
        }
        break;

      case NodeTypesEnum::TEXT_NODE:
        // For text nodes, add the text content.
        // Only add non-empty trimmed text.
        $html .= (strlen(trim($node->nodeValue)) > 0) ? $indent . $node->nodeValue . PHP_EOL : '';
        break;

      case NodeTypesEnum::DOCTYPE:
        // Add the doctype declaration.
        $html .= $indent . '<!DOCTYPE html>' . PHP_EOL;
        break;
    }

    // Return the constructed HTML string.
    return $html;
  }

  /**
   * Parses an array of tokens and constructs a DOM tree representation.
   *
   * This function processes an array of HTML tokens to build a DOM tree. It handles
   * different types of nodes including document, element, and text nodes. The function
   * also manages special handling for tags that contain content treated as text, such
   * as `<script>` tags.
   *
   * @param array $tokens The array of HTML tokens to be parsed.
   * @throws \Exception If an unexpected end tag is encountered.
   */
  private function parseTokens(array $tokens): void {
    // Initialize the root node as a document node.
    $this->dom = new DOMNode(NodeTypesEnum::DOCUMENT, 'document');

    // Set the current node to the root node.
    $currentNode = $this->dom;

    // Stack to manage open elements and ensure correct closing.
    $stack = [];

    // Flag to determine if we are inside a tag whose content should be treated as text.
    $insideContentAsTextTag = false;

    // If there are no tokens, return the root node immediately.
    if (count($tokens) <= 0) {
      return;
    }

    // Iterate over each token in the input array.
    foreach ($tokens as $token) {
      // Skip empty tokens.
      if (strlen($token) <= 0) continue;

      // Check for a DOCTYPE declaration.
      if (stripos($token, '<!DOCTYPE') === 0) {
        // Create and add a DOCTYPE node to the root.
        $doctypeNode = new DOMNode(NodeTypesEnum::DOCTYPE, 'doctype');
        $this->dom->addChild($doctypeNode);
        continue;
      }

      if ($insideContentAsTextTag) {
        // Check if the token is a closing tag for content-as-text tags.
        if ($this->isContentAsTextClosingTag($token)) {
          // End handling of content-as-text.
          $insideContentAsTextTag = false;

          // Pop the stack to manage node closure.
          array_pop($stack);

          // Reset the current node pointer to the last stack element or root.
          $currentNode = !empty($stack) ? end($stack) : $this->dom;
        } else {
          // Append token as text content inside a content-as-text tag.
          $currentNode->addChild(
            new DOMNode(NodeTypesEnum::TEXT_NODE, 'text', $token)
          );
        }
      } else {
        // Check if the token is a content-as-text opening tag (e.g., <script>).
        if ($this->isContentAsTextTag($token)) {
          $insideContentAsTextTag = true;

          // Get tag data
          $tagData = $this->getTagName($token);

          // Create a new element node for the content-as-text tag
          $element = $this->addNodeChild($currentNode, $tagData);

          // Push the element onto the stack.
          $stack[] = $element;

          // Set the current node to the newly created element.
          $currentNode = $element;
        } elseif ($tagData = $this->getTagName($token)) {
          // Determine if the tag is self-closing.
          $isSelfClosing = $this->isSelfClosingTag($tagData['nodeName']);

          // Create a new element node for the tag.
          $element = $this->addNodeChild($currentNode, $tagData);

          // If the tag is not self-closing, handle the stack logic.
          if (!$isSelfClosing) {
            // Handle optional closing tags by popping stack elements.
            if (isset($this->optionalClosingTags[$tagData['nodeName']])) {
              while (!empty($stack) && in_array(end($stack)->nodeName, $this->optionalClosingTags[$tagData['nodeName']])) {
                array_pop($stack);
                $currentNode = end($stack);
              }
            }

            // Push the new element onto the stack.
            $stack[] = $element;
            $currentNode = $element;
          }
        } elseif ($tagData = $this->getClosingTagName($token)) {
          // If token is a closing tag, process the stack.
          $tagName = $tagData['nodeName'];

          // Pop elements from the stack until the corresponding opening tag is found.
          while (!empty($stack) && end($stack)->nodeName !== $tagName) {
            array_pop($stack);
            $currentNode = end($stack);
          }

          // If a matching opening tag is found, pop it from the stack.
          if (!empty($stack) && end($stack)->nodeName === $tagName) {
            array_pop($stack);
            $currentNode = !empty($stack) ? end($stack) : $this->dom;
          } else {
            // If no matching opening tag is found, throw an exception.
            throw new \Exception('Unexpected end tag: </' . $tagName . '> found');
          }
        } else {
          // If the token is text, create a text node and add it to the current node.
          $text = new DOMNode(NodeTypesEnum::TEXT_NODE, '', trim($token));
          $currentNode->addChild($text);
        }
      }
    }
  }

  /**
   * Splits an HTML string into an array of tokens, separating tags and text blocks.
   *
   * This function uses regular expressions to tokenize an HTML string into distinct
   * elements. Each tag and text block is treated as a separate token. Special handling
   * is applied to content-as-text tags such as `<script>` or `<style>`.
   *
   * @param string $html The HTML string to be tokenized.
   * @return array An array of tokens representing the HTML structure.
   */
  private function tokenize(string $html): array {
    // Initialize an empty array to hold the tokens.
    $tokens = [];

    // Create a regular expression pattern for content-as-text tags.
    $contentAsTextTagsExpr = implode('|', $this->contentAsTextTags);

    // Define the pattern to match:
    // - <!DOCTYPE html>
    // - Content-as-text tags and their content (e.g., <script>...</script>)
    // - Any other HTML tags
    // - Text between tags
    /*$pattern = '/<!DOCTYPE html>|<(?:' . $contentAsTextTagsExpr . ')[^>]*?>.*?<\/(?:' . $contentAsTextTagsExpr . ')>|<[^>]+>|[^<]+/is';*/
    $pattern = '/<!DOCTYPE html>|<(?:script|style)[^>]*?>|<[^>]+>|[^<]+/is';

    // Use preg_match_all to find all matches based on the pattern.
    preg_match_all($pattern, $html, $matches);

    // Iterate over the matches and add them to the tokens array.
    foreach ($matches[0] as $match) {
      if ( strlen(trim($match)) > 0 )
        $tokens[] = $match;
    }

    // Return the array of tokens.
    return $tokens;
  }

  /**
   * Adds a child node to current node. Used in parseTokens.
   *
   * @param $currentNode
   * @param $tagData
   * @return void
   */
  private function addNodeChild(&$currentNode, $tagData, $nodeType = NodeTypesEnum::ELEMENT_NODE): DOMNode {// If token is a standard tag, extract its data.
    // Prepare arguments
    $tagName = $tagData['nodeName'];
    $namespace = $tagData['namespace'] ?? null;
    $attributes = $tagData['nodeAttributes'] ?? null;

    // Create a new element node for the tag.
    $element = new DOMNode($nodeType, $tagName, '', $namespace, $attributes);
    $currentNode->addChild($element);

    return $element;
  }

  /**
   * Determines if a tag is self-closing.
   *
   * This function checks if a given tag name is a self-closing tag by comparing
   * it against a predefined list of self-closing tags or checking if the tag
   * ends with a forward slash.
   *
   * @param string $tagName The name of the tag to check.
   * @return bool True if the tag is self-closing, false otherwise.
   */
  private function isSelfClosingTag(string $tagName): bool {
    // Check if the tag is in the list of self-closing tags or ends with a slash.
    return in_array($tagName, $this->selfClosingTags) || substr($tagName, -1) === '/';
  }

  /**
   * Checks if a token is a content-as-text tag (like <script> or <style>).
   *
   * This function uses regular expressions to determine if a given token represents
   * an opening or closing content-as-text tag. These tags contain content that should
   * be treated as text rather than parsed as HTML.
   *
   * @param string $token The token to check.
   * @param bool $isClosingTag Optional. If true, checks for a closing tag. Default is false.
   * @return bool True if the token is a content-as-text tag, false otherwise.
   */
  private function isContentAsTextTag(string $token, bool $isClosingTag = false): bool {
    // Create a regex pattern for content-as-text tags.
    $contentAsTextTagsExpr = implode('|', $this->contentAsTextTags);

    // Regular expressions for matching opening and closing tags.
    $startExpr = '/^<(' . $contentAsTextTagsExpr . ')[^>]*>$/i';
    $endExpr = '/^<\/(' . $contentAsTextTagsExpr . ')>$/i';

    // Return true if the token matches the appropriate regex pattern.
    return (bool)(($isClosingTag) ? preg_match($endExpr, $token) : preg_match($startExpr, $token));
  }

  /**
   * Checks if a token is a closing tag for content-as-text tags.
   *
   * This function is a shortcut to check if a token is a closing tag for tags that
   * treat their content as text.
   *
   * @param string $token The token to check.
   * @return bool True if the token is a closing content-as-text tag, false otherwise.
   */
  private function isContentAsTextClosingTag(string $token): bool {
    // Utilize the existing isContentAsTextTag function for closing tags.
    return $this->isContentAsTextTag($token, true);
  }

  /**
   * Extracts the tag name and attributes from a token.
   *
   * This function uses regular expressions to extract the tag name, namespace,
   * and attributes from a token. It handles both opening and closing tags.
   *
   * @param string $token The token to extract the tag name from.
   * @param bool $isClosingTag Optional. If true, processes a closing tag. Default is false.
   * @return array|false An associative array with keys 'namespace', 'nodeName', and 'nodeAttributes',
   *                     or false if the token does not represent a valid tag.
   */
  private function getTagName(string $token, bool $isClosingTag = false): array|false {
    if (!$isClosingTag) {
      // Regex to capture the tag name and attributes, including optional namespace.
      $expr = '/^<((?:(\w+):)?([a-zA-Z0-9\-]+))(.*?)>$/';

      if (preg_match($expr, $token, $matches)) {
        return [
          'namespace' => $matches[2] ?? null,
          'nodeName' => ( $matches[2] ) ? $matches[3] : strtolower($matches[3]),
          'nodeAttributes' => $this->parseAttributes($matches[4])
        ];
      }
    } else {
      // Regex for capturing closing tags with optional namespace.
      $expr = "/^<\/(?:(\w+):)?([a-zA-Z0-9\-]+)>/";

      if (preg_match($expr, $token, $matches)) {
        return [
          'namespace' => $matches[1] ?? null,
          'nodeName' => ( $matches[1] ) ? $matches[2] : strtolower($matches[2]),
          'nodeAttributes' => null
        ];
      }
    }

    // Return false if the token does not match the expected patterns.
    return false;
  }

  /**
   * Extracts the tag name from a closing tag token.
   *
   * This function uses the getTagName function to process closing tags specifically.
   *
   * @param string $token The closing tag token to process.
   * @return array|false An associative array with 'namespace' and 'nodeName', or false if invalid.
   */
  private function getClosingTagName(string $token): array|false {
    // Use the existing getTagName function with the closing tag flag set to true.
    return $this->getTagName($token, true);
  }

  /**
   * Parses a string of attributes into an array of NodeAttribute objects.
   *
   * This function uses regular expressions to extract attributes from a string,
   * creating NodeAttribute objects for each attribute found.
   *
   * @param string $attributesString The string containing HTML attributes.
   * @return array An array of NodeAttribute objects representing the attributes.
   */
  private function parseAttributes(string $attributesString): array {
    // Regex to capture attribute name and value pairs.
    $expr = '/([a-zA-Z0-9\-:]+)(=("([^"]*)")|(\'([^\']*)\'))?/';

    // Initialize an empty array to hold the attributes.
    $attributes = [];

    // Use preg_match_all to extract attribute matches.
    if (preg_match_all($expr, $attributesString, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $attributeName = strtolower($match[1]);
        $attributeValue = isset($match[3]) ? $match[3] : (isset($match[5]) ? $match[5] : null);

        // Remove surrounding quotes from attribute values.
        if ($attributeValue && preg_match('/^(\'|")(.*)(\'|")$/', $attributeValue, $matches)) {
          $attributeValue = $matches[2];
        }

        // Create a new NodeAttribute object and add it to the array.
        $attributes[] = new NodeAttribute($attributeName, $attributeValue);
      }
    }

    // Return the array of NodeAttribute objects.
    return $attributes;
  }
}
