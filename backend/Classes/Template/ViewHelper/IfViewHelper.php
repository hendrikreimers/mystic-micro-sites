<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class IfViewHelper
 *
 * This class provides functionality to evaluate conditions using the Symfony Expression Language
 * and render a template block based on the evaluation result.
 */
class IfViewHelper extends BaseViewHelper implements ViewHelperInterface {
  /**
   * Registers allowed and required Arguments for this ViewHelper
   *
   * @return void
   */
  public function registerArguments(): void {
    $this->registerArgument('condition', true, 'Symfony Expression Language condition', true);
  }

  /**
   * Renders the content block if the condition evaluates to true.
   *
   * The method evaluates the given condition and renders the content block only if
   * the condition is true.
   *
   * @return string The rendered content if the condition is true, otherwise an empty string.
   */
  public function render(): string {
    $expressionLanguage = new ExpressionLanguage();

    $condition = $this->getArgumentValue('condition');

    // Get all variables from the view
    $variables = $this->engine->view->getAll();

    // Evaluate the condition using Symfony Expression Language
    $result = $expressionLanguage->evaluate($condition, $variables);

    // Render content only if the condition evaluates to true
    return ( $result ) ? $this->renderChildren() : '';
  }
}
