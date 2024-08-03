<?php
declare(strict_types=1);

namespace Template;

use Utility\StringUtlity;

/**
 * Template Engine View
 *
 * It handles the variables and values for the view atm.
 */
class TemplateView {
  /**
   * Config value to escapeString on final value
   * @var bool
   */
  public $escapeString = true;

  /**
   * Config value to stripTags on final value
   * @var bool
   */
  public $stripTags = true;

  /**
   * Variable container
   * @var array
   */
  private array $variables = [];

  /**
   * Assign a variable value and variable name
   *
   * @param string $name
   * @param string|array|int $value
   * @return void
   */
  public function assign(string $name, string|array|int $value): void {
    $this->variables[$name] = $value;
  }

  /**
   * Assigns multiple variables to the variable container
   *
   * @param array $variables
   * @return void
   */
  public function assignMultiple(array $variables): void {
    foreach ($variables as $name => $value) {
      $this->variables[$name] = $value;
    }
  }

  /**
   * Gets a variable value by given variable name
   *
   * @param string $name
   * @return mixed|null
   */
  public function get(string $name) {
    return $this->handleValueEscape($this->variables[$name]) ?? null;
  }

  /**
   * Gets a nested variable value by its path, supporting both associative and indexed arrays
   *
   * @param string $path
   * @return mixed|null
   */
  public function getNested(string $path) {
    // Match each part of the path, handling both dots and array indices
    $pattern = '/(?<!\\\\)(?:\.|(?<=\])\.)/';  // Split by '.' unless inside brackets
    $keys = preg_split($pattern, $path);

    $value = $this->variables;

    foreach ($keys as $key) {
      // Check if the key includes an array index like [0]
      if (preg_match('/(.+?)\[(\d+)\]$/', $key, $matches)) {
        $key = $matches[1];
        $index = (int)$matches[2];

        if (is_array($value) && array_key_exists($key, $value) && isset($value[$key][$index])) {
          $value = $value[$key][$index];
        } else {
          return null;
        }
      } else {
        if (is_array($value) && array_key_exists($key, $value)) {
          $value = $value[$key];
        } else {
          return null;
        }
      }
    }

    return is_array($value) ? htmlspecialchars(json_encode($value), ENT_QUOTES | ENT_HTML5, 'UTF-8') : $this->handleValueEscape($value);
  }

  /**
   * Returns all variables
   *
   * @return array
   */
  public function getAll(): array {
    return $this->variables;
  }

  /**
   * Renders child content variables (used mostly in viewhelpers))
   *
   * @param string $content
   * @return string
   */
  public function renderContent(string $content): string {
    // Match all {{ variable }} patterns and replace them with actual values
    return preg_replace_callback('/{{\s*([\w.\[\]]+)\s*}}/', function ($matches) {
      $variableName = $matches[1];

      $value = $this->getNested($variableName);
      return $value !== null ? $value : $matches[0];
    }, $content);
  }

  /**
   * Escapes the string if set in view options
   *
   * @param mixed $value
   * @return mixed
   */
  private function handleValueEscape(mixed $value): mixed {
    if ( is_string($value) ) {
      $value = StringUtlity::escapeString($value, false, $this->escapeString, $this->stripTags);

      // Fix for double escaped
      $expr = '/&amp;(amp|lt|gt|quot|#\d+;)/m';
      $value = preg_replace($expr, '&$1', $value);
    }

    return $value;
  }
}
