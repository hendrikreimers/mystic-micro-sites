<?php
declare(strict_types=1);

namespace Template;

/**
 * Template Engine View
 *
 * It handles the variables and values for the view atm.
 */
class TemplateView
{
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
  public function assign(string $name, string|array|int $value): void
  {
    $this->variables[$name] = $value;
  }

  /**
   * Assigns multiple variables to the variable container
   *
   * @param array $variables
   * @return void
   */
  public function assignMultiple(array $variables): void
  {
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
  public function get(string $name)
  {
    return $this->variables[$name] ?? null;
  }

  /**
   * Gets a nested variable value by its path, supporting both associative and indexed arrays
   *
   * @param string $path
   * @return mixed|null
   */
  public function getNested(string $path)
  {
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

    return is_array($value) ? json_encode($value) : $value;
  }

  /**
   * Returns all variables
   *
   * @return array
   */
  public function getAll(): array
  {
    return $this->variables;
  }

  public function renderContent(string $content): string
  {
    // Match all {{ variable }} patterns and replace them with actual values
    return preg_replace_callback('/{{\s*([\w.\[\]]+)\s*}}/', function ($matches) {
      $variableName = $matches[1];

      $value = $this->getNested($variableName);
      return $value !== null ? $value : $matches[0];
    }, $content);
  }
}
