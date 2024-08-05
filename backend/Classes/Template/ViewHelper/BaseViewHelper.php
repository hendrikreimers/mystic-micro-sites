<?php
declare(strict_types=1);

namespace Template\ViewHelper;

use Models\DOM\DOMNode;
use Models\DOM\NodeList;
use Template\TemplateEngine;

abstract class BaseViewHelper {
  /**
   * @var array Registered Arguments list
   */
  protected array $arguments = [];

  /**
   * @var TemplateEngine Fresh template engine instance for iterations
   */
  protected TemplateEngine $engine;

  public function __construct(
    protected DOMNode $currentNode
  ) {
    $this->engine = new TemplateEngine();
  }

  /**
   * Initializes the ViewHelper by checking the register arguments and set the values given by the Node Attributes to them
   *
   * @param array $attributes
   * @param string $className
   * @return void
   * @throws \Exception
   */
  public function initialize(array &$attributes, string $className): void {
    if ( sizeof($attributes) === 0 ) return;

    /* @var \Models\DOM\NodeAttribute $attribute */
    foreach ( $attributes as $attribute ) {
      if ( array_key_exists($attribute->attributeName, $this->arguments ) ) {
        $argument = &$this->arguments[$attribute->attributeName];
        $argumentValue = $attribute->attributeValue;

        if ( $argument['htmlspecialcharsDecode'] ) {
          $argumentValue = htmlspecialchars_decode($argumentValue, ENT_QUOTES);
        }

        if ( $argument['jsonDecode'] ) {
          $argumentValue = json_decode($argumentValue, true);
        }

        $argument['value'] = $argumentValue;
      } else {
        $allowedArguments = array_map(fn($key, $argument) => $key, $this->arguments);
        throw new \Exception('Too much Arguments in "' . $className . '". Allowed Arguments: "' . join(',', $allowedArguments) . '"');
      }
    }

    // Check if all required arguments are set
    if ( sizeof($this->arguments) > 0 ) {
      $missingRequired = array_filter($this->arguments, fn($argument) => $argument['required'] === true && $argument['value'] === null);

      if ( sizeof($missingRequired) > 0 ) {
        foreach ( $missingRequired as $requiredArgument ) {
          throw new \Exception('Missing required argument: "' . $requiredArgument['name'] . '" in "' . $className . '"');
        }
      }
    }
  }

  /**
   * Appends the list of required and allowed arguments.
   * Called in viewHelper's registerArguments() method.
   *
   * @param string $name
   * @param bool $required
   * @param string $description
   * @param bool $htmlspecialcharsDecode
   * @param bool $jsonDecode
   * @return void
   */
  protected function registerArgument(string $name, bool $required = false, string $description = '', bool $htmlspecialcharsDecode = false, bool $jsonDecode = false) {
    $this->arguments[$name] = [
      'required' => $required,
      'description' => $description,
      'value' => null,
      'htmlspecialcharsDecode' => $htmlspecialcharsDecode,
      'jsonDecode' => $jsonDecode
    ];
  }

  /**
   * Returns an Argument value if possible
   *
   * @param string $argumentName
   * @return mixed
   */
  protected function getArgumentValue(string $argumentName): mixed {
    if ( !array_key_exists($argumentName, $this->arguments) )
      return false;

    return $this->arguments[$argumentName]['value'];
  }

  /**
   * Renders child nodes
   *
   * @return string
   */
  protected function renderChildren(): string {
    // Important we need to deep clone the child nodes
    $clonedChildNodes = unserialize(serialize($this->currentNode->childNodes));

    // Set a part of a template to it
    $this->engine->setFragment($clonedChildNodes);

    // Render HTML
    return $this->engine->render();
  }

}
