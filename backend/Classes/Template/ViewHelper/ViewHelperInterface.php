<?php

namespace Template\ViewHelper;

use Models\DOM\DOMNode;

interface ViewHelperInterface {
    public function render(): string;
}
