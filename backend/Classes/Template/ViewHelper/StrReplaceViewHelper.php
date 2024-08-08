<?php

declare(strict_types=1);

namespace Template\ViewHelper;

/**
 * Class StrReplaceViewHelper
 *
 * This class provides functionality to search and replace inner content
 */
class StrReplaceViewHelper extends BaseViewHelper implements ViewHelperInterface {
  /**
   * Registers allowed and required Arguments for this ViewHelper
   *
   * @return void
   */
  public function registerArguments(): void {
    $this->registerArgument('search', true, 'needle', false);
    $this->registerArgument('replace', true, 'replace', false);
  }

  /**
   * Renders the content and then search and replaces strings
   *
   * @return string The rendered content
   */
  public function render(): string {
    $search = $this->getArgumentValue('search');
    $replace = $this->getArgumentValue('replace');
    $haystack = $this->renderChildren() ?: '';

    return str_replace($search, $replace, $haystack);
  }
}
