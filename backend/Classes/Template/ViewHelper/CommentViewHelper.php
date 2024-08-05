<?php

declare(strict_types=1);

namespace Template\ViewHelper;

use Models\DOM\DOMNode;

/**
 * CommentViewHelper
 *
 * Does nothing... because it's just a wrapper to comment something in the template
 * which will be hidden in the rendered template.
 */
class CommentViewHelper extends BaseViewHelper implements ViewHelperInterface {
  /**
   * Does nothing...
   *
   * @param DOMNode $currentNode
   * @return string
   */
  public function render(): string {
    return '';
  }
}
