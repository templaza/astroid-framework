<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
if (!defined('_ASTROID')) {
   try {
      Astroid\Framework::init();
   } catch (\Exception $e) {
      die('Please install and activate <a href="https://www.astroidframe.work/" target="_blank">Astroid Framework</a> in order to use this template.');
   }
}

require Astroid\Helper\Overrides::getHTMLSystem();