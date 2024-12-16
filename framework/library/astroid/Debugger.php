<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid;

defined('_JEXEC') or die;

class Debugger
{
    protected $markers = [];
    protected $reports = [];
    public $debug = 0;
    protected $_last = null;

    public function __construct()
    {
        $params = Helper::getPluginParams();
        $this->debug = $params->get('astroid_debug', 0);
    }

    public function log($name)
    {
        if (!isset($this->markers[$name])) {
            $this->start($name);
        } else {
            $this->stop($name);
        }
    }

    public function start($name)
    {
        if (!$this->debug) return;
        $this->markers[$name] = getrusage();
        $this->reports[$name] = null;
    }

    public function stop($name)
    {
        if (!$this->debug) return;
        if (!isset($this->markers[$name])) {
            return;
        }
        $stop = getrusage();
        $utime = $this->getRunTime($stop, $this->markers[$name], "utime");
        $stime = $this->getRunTime($stop, $this->markers[$name], "stime");

        $report = new Helper\DebugReport($name);
        $report->save($utime, $stime, memory_get_usage(), memory_get_peak_usage());
        Framework::getReporter('Debug')->add($report->render());
    }

    protected function getRunTime($ru, $rus, $index)
    {
        return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
    }
}
