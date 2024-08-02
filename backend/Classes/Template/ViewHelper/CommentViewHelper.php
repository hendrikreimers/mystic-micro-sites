<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Template\TemplateView;

/**
 * CommentViewHelper
 *
 * Does nothing... because it's just a wrapper to comment something in the template
 * which will be hidden in the rendered template.
 */
class CommentViewHelper
{
  /**
   * @var TemplateView
   *
   * The TemplateView instance that allows access to assigned variables
   * within the template.
   */
  public TemplateView $view;

  /**
   * Does nothing...
   *
   * @param array $attributes
   * @param string $content
   * @return string
   */
  public function render(array $attributes, string $content): string {
    return '';
  }
}
