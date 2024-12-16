<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;

class AstroidParams
{
    public $params = [];
    function __construct($params)
    {
        $this->params = $params;
    }

    public function get($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            return $default;
        }
    }
}