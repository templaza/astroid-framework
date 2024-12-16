<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Helper;
use Astroid\Helper;
class DebugReport
{
    public $id = '', $title = '', $utime = 0, $stime = 0, $memory = 0, $memorypeak = 0, $processed = false;
    public function __construct($id)
    {
        $this->id = $id;
        $this->title = str_replace(['-', '_'], ' ', Helper::title($id));
    }

    public function save($utime, $stime, $memory, $memorypeak)
    {
        $this->utime = $utime;
        $this->stime = $stime;
        $this->memory = $memory;
        $this->memorypeak = $memorypeak;
        $this->processed = true;
    }

    public function render()
    {
        return '<p class="m-0"><strong class="mr-2"><em>' . $this->title . '</em></strong> <span class="badge badge-light mr-2">Time: ' . $this->utime . ' ms</span> <span class="badge badge-light">Memory: ' . round(($this->memorypeak - $this->memory) / 1048576, 3) . ' MB' . '</span></p>';
    }
}