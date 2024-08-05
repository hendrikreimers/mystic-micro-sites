<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Models\DOM\DOMNode;

/**
 * Class ForEachViewHelper
 *
 * This class provides functionality to iterate over a collection of items and render
 * a template block for each item, substituting placeholders with item-specific values.
 */
class ForEachViewHelper extends BaseViewHelper implements ViewHelperInterface {
  /**
   * Registers allowed and required Arguments for this ViewHelper
   *
   * @return void
   */
  public function registerArguments(): void {
    $this->registerArgument('each', true, 'JSON String of traversable array', true, true);
    $this->registerArgument('as', true, 'Current item of loop');
    $this->registerArgument('index', false, 'Current index of iteration');
  }

  /**
   * Renders the content block for each item in a collection.
   *
   * The method expects attributes specifying the collection to iterate over,
   * as well as the variable names for the current item and its index. It replaces
   * placeholders in the content block with these values for each item.
   *
   * @return string The concatenated output of the rendered content for each item.
   * @throws \Exception If the 'each' attribute is not a valid array or iterable.
   */
  public function render(): string {
    $output = '';
    $items = $this->getArgumentValue('each');
    $variableName = $this->getArgumentValue('as');
    $indexName = $this->getArgumentValue('index') ?? 'index';

    // Check if items are iterable
    if (!is_iterable($items)) {
      throw new \Exception("Invalid array or iterable format in 'each' attribute");
    }

    foreach ($items as $index => $item) {
      // Temporarily assign variables to the view
      $this->engine->view->assign($variableName, $item);
      $this->engine->view->assign($indexName, $index);

      $output .= $this->renderChildren();

      $this->engine->view->assign($variableName, '');
      $this->engine->view->assign($indexName, '');
    }

    return $output;
  }
}
