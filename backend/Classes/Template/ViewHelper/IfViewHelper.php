<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Template\TemplateView;

/**
 * Class IfViewHelper
 *
 * This class provides functionality to evaluate conditions using the Symfony Expression Language
 * and render a template block based on the evaluation result.
 */
class IfViewHelper
{
  /**
   * @var TemplateView
   *
   * The TemplateView instance that allows access to assigned variables
   * within the template.
   */
  public TemplateView $view;

  /**
   * @var ExpressionLanguage
   *
   * The ExpressionLanguage instance to evaluate expressions.
   */
  private ExpressionLanguage $expressionLanguage;

  public function __construct()
  {
    $this->expressionLanguage = new ExpressionLanguage();
  }

  /**
   * Renders the content block if the condition evaluates to true.
   *
   * The method evaluates the given condition and renders the content block only if
   * the condition is true.
   *
   * @param array $attributes An associative array containing the 'condition' key.
   * @param string $content The content template to be rendered if the condition is true.
   * @return string The rendered content if the condition is true, otherwise an empty string.
   * @throws \Exception If the condition expression is invalid.
   */
  public function render(array $attributes, string $content): string
  {
    $condition = $attributes['condition'] ?? null;

    if ($condition === null) {
      throw new \Exception("The 'condition' attribute is required.");
    }

    // Get all variables from the view
    $variables = $this->view->getAll();

    // Evaluate the condition using Symfony Expression Language
    $result = $this->expressionLanguage->evaluate($condition, $variables);

    // Render content only if the condition evaluates to true
    if ($result) {
      return $this->view->renderContent($content);
    }

    return '';
  }
}
