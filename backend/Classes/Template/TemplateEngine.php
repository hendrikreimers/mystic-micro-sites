<?php
declare(strict_types=1);

namespace Template;

use Enums\DOM\NodeTypesEnum;
use Models\DOM\NodeList;
use Parser\HTMLParser;
use Models\DOM\DOMNode;

class TemplateEngine {
  /**
   * @var HTMLParser The content of the template file.
   */
  private HTMLParser $domTemplate;

  /**
   * @var TemplateView An instance of the TemplateView class for managing template variables.
   */
  public TemplateView $view;

  /**
   * TemplateEngine constructor.
   *
   * Initializes the template engine with a given HTML DOM.
   *
   * @param string|null $templateFilePath The file path to the template.
   * @throws \Exception
   */
  public function __construct(?string $templateFilePath = null) {
    // Load the template file content
    $this->domTemplate = new HTMLParser();

    if ( $templateFilePath )
      $this->domTemplate->parseHTML(file_get_contents($templateFilePath));

    // Initialize a new TemplateView instance
    $this->view = new TemplateView();
  }

  /**
   * Render the template with variables and custom tags replaced.
   *
   * @return string The rendered template as a string.
   */
  public function render(): string {
    $this->domTemplate->dom->childNodes = $this->parseNodes($this->domTemplate->dom->childNodes);
    $this->domTemplate->dom->childNodes = $this->renderViewHelperNodes($this->domTemplate->dom->childNodes);
    return $this->domTemplate->saveHTML();
  }

  /**
   * Instead of a parsed HTML Template you can set a fragment of it.
   * Useful on parsing viewHelpers children.
   *
   * @param NodeList $nodeList
   * @return void
   */
  public function setFragment(NodeList $nodeList): void {
    $this->domTemplate->dom = new DOMNode(NodeTypesEnum::DOCUMENT, 'document', childNodes: $nodeList);
  }

  /**
   * Lookups for all MysticMicroSite (mms) ViewHelpers and renders them recursively
   *
   * @param NodeList $nodeList
   * @return NodeList
   * @throws \Exception
   */
  private function renderViewHelperNodes(NodeList $nodeList): NodeList {
    if ( $nodeList->count() > 0 ) {
      while ($nodeList->valid()) {
        // Get current node
        $currentNode = $nodeList->current();

        // Check if it is a ViewHelper
        if ( $currentNode->namespace === 'mms' ) {
          $nodeName = $currentNode->nodeName;
          $nodeList->replaceByIndex($nodeList->key(), $this->processViewHelper($nodeName, $currentNode));
        }

        // Change child nodes
        if ($currentNode->childNodes->count() > 0) {
          $currentNode->childNodes = $this->renderViewHelperNodes($currentNode->childNodes);
        }

        // Set pointer to next
        $nodeList->next();
      }

      // Don't forget to rewind the position marker
      $nodeList->rewind();
    }

    return $nodeList;
  }

  /**
   * Processes a ViewHelper
   *
   * This method creates an instance of the viewhelper and initializes it,
   * then content will be rendered as HTML String and pushed back to the DOM.
   *
   * @param string $viewHelperName
   * @param $currentNode
   * @return DOMNode
   * @throws \Exception
   */
  private function processViewHelper(string $viewHelperName, $currentNode): DOMNode {
    $className = '\\Template\\ViewHelper\\' . ucfirst($viewHelperName) . 'ViewHelper';

    // Check if the specified class exists
    if (!class_exists($className)) {
      throw new \Exception("ViewHelper Class \"$className\" not found");
    }

    // Check if the specified class implements interface
    if ( in_array('ViewHelperInterface', class_implements($className)) ) {
      throw new \Exception("ViewHelper Class \"$className\" not implements ViewHelperInterface");
    }

    // Reference attributes
    $attributes = &$currentNode->attributes;

    // Instantiate the view helper class
    $instance = new $className($currentNode, $this);

    // Initialize arguments
    if (method_exists($instance, 'registerArguments')) {
      $instance->registerArguments();
    }

    // Initialize the ViewHelper
    $instance->initialize($attributes, $className); // Checks if registered arguments are set and sets the value

    // Ensure the class has a render method
    if (!method_exists($instance, 'render')) {
      throw new \Exception("Class $className does not have a render method");
    }

    // Render the Node
    $renderedContent = $instance->render();

    return new DOMNode(NodeTypesEnum::TEXT_NODE, 'text', $renderedContent);
  }

  /**
   * Parses each Node for variables in attribute values and text node values
   *
   * @param NodeList $nodeList
   * @return void
   */
  private function parseNodes(NodeList $nodeList): NodeList {
    if ( $nodeList->count() > 0 ) {
      while ( $nodeList->valid() ) {
        // Initialize the next round
        $currentNode = $nodeList->current();
        $nodeHasChanged = false;

        // Change text content
        if (trim($currentNode->nodeValue) !== '') {
          $currentNode->nodeValue = $this->replaceVariables($currentNode->nodeValue);
          $nodeHasChanged = true;
        }

        // Push variables to attributes
        if ( $currentNode->attributes ) {
          $this->parseNodeAttributes($currentNode);
        }

        // Change child nodes
        if ($currentNode->childNodes->count() > 0) {
          $currentNode->childNodes = $this->parseNodes($currentNode->childNodes);
          $nodeHasChanged = true;
        }

        // Replace if something has changed
        if ( $nodeHasChanged ) {
          $nodeList->replaceByIndex($nodeList->key(), $currentNode);
        }

        // Set pointer to next
        $nodeList->next();
      }

      // Don't forget to rewind the position marker
      $nodeList->rewind();
    }

    return $nodeList;
  }

  /**
   * Replaces Variables in Attributes of the given DOMNode reference
   *
   * @param DOMNode $node
   * @return void
   */
  private function parseNodeAttributes(DOMNode &$node): void {
    if ( $node->attributes && count($node->attributes) > 0 ) {
      for ( $i = 0; $i < count($node->attributes); $i++ ) {
        $node->attributes[$i]->attributeValue = $this->replaceVariables($node->attributes[$i]->attributeValue);
      }
    }
  }

  /**
   * Replace template variables within {{ ... }} with their values.
   *
   * @param string $html The template content to process.
   * @return string The template with variables replaced.
   */
  private function replaceVariables(string $html): string {
    // Use regex to find all {{ variable }} patterns
    return preg_replace_callback('/{{\s*([\w.\[\d\]]+)\s*([|]{0,}\s*)([\w.]{0,})(\s*)?}}/', function ($matches) {
      // Extract the variable name and optional function pipe
      $variableName = $matches[1];
      $variablePipe = $matches[3];

      // Retrieve the variable value from the view
      $value = $this->view->getNested($variableName);

      // Use the variable's value or fallback to the matched pattern if not found
      $returnValue = $value !== null ? $value : $matches[0];

      // Apply a function to the value if a pipe is specified
      return ($variablePipe) ? $variablePipe($returnValue) : $returnValue;
    }, $html);
  }

  /**
   * Check if a given string is a valid JSON string.
   *
   * @param string $string The string to check.
   * @return bool True if the string is valid JSON, false otherwise.
   */
  private function isJson(string $string): bool {
    // Decode the string and check for JSON errors
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
  }
}
