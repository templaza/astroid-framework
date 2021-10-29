<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

class Reporter
{
    public $title, $id, $reports = [];

    public function __construct($title)
    {
        $this->title = $title;
        $this->id = Helper::slugify($title) . '-reporter';
        Framework::addReporter($this);
    }

    public function add($report)
    {
        $this->reports[] = $report;
    }

    public function backtrace($array)
    {
        $info = $array[0];
        $this->add("<code>{$info['class']}</code> is deprecated and no longer suppoerted. use <code>Astroid\framework::getTemplate()</code> in <code>{$info['file']}</code> at line <code>{$info['line']}</code>.");
    }
}
