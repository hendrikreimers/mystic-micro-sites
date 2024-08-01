<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Template\TemplateView;

/**
 * Class ForEachViewHelper
 *
 * This class provides functionality to iterate over a collection of items and render
 * a template block for each item, substituting placeholders with item-specific values.
 */
class ForEachViewHelper
{
  /**
   * @var TemplateView
   *
   * The TemplateView instance that allows access to assigned variables
   * within the template.
   */
  public TemplateView $view;

  /**
   * Renders the content block for each item in a collection.
   *
   * The method expects attributes specifying the collection to iterate over,
   * as well as the variable names for the current item and its index. It replaces
   * placeholders in the content block with these values for each item.
   *
   * @param array $attributes An associative array containing 'each', 'let', and 'index' keys.
   * @param string $content The content template to be rendered for each item.
   * @return string The concatenated output of the rendered content for each item.
   * @throws \Exception If the 'each' attribute is not a valid array or iterable.
   */
  public function render(array $attributes, string $content): string
  {
    $output = '';
    $items = $attributes['each'] ?? [];
    $variableName = $attributes['let'] ?? 'item';
    $variablePipe = $attributes['eachPipe'] ?? '';
    $indexName = $attributes['index'] ?? 'index';

    if ( $variablePipe ) {
      $items = $variablePipe($items);
    }

    // Decode JSON string if $items is a JSON-encoded string
    if (is_string($items)) {
      $decodedItems = json_decode($items, true);

      if (json_last_error() === JSON_ERROR_NONE) {
        $items = $decodedItems;
      } else {
        $items = $this->parseItems($items);
      }
    }

    // Check if items are iterable
    if (!is_iterable($items)) {
      throw new \Exception("Invalid array or iterable format in 'each' attribute");
    }

    foreach ($items as $index => $item) {
      // Temporarily assign variables to the view
      $this->view->assign($variableName, $item);
      $this->view->assign($indexName, $index);

      // Render content using the view to replace placeholders
      $renderedContent = $this->view->renderContent($content);

      // Append rendered content to the output
      $output .= $renderedContent;
    }

    return $output;
  }

  /**
   * Parses a string representation of an array and returns an array of items.
   *
   * This method takes a string formatted as a list of values enclosed in square brackets,
   * e.g., "['a','b','c']", and converts it into a PHP array. The method handles both single
   * and double quotes around the items and trims any extra spaces.
   *
   * @param string $itemsString The string representation of an array.
   * @return array The parsed array of items.
   */
  private function parseItems(string $itemsString): array
  {
    // Remove square brackets from the beginning and end of the string
    $trimmed = trim($itemsString, "[]");

    // Split the string into individual items using commas as delimiters, accounting for possible spaces
    $parts = preg_split("/\s*,\s*/", $trimmed);

    // Trim surrounding quotes (single or double) from each item and return the resulting array
    return array_map(fn($item) => trim($item, "'\""), $parts);
  }
}
