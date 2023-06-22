<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
defined('_ASTROID') or die('Please install and activate <a href="https://astroidframe.work/" target="_blank">Astroid Framework</a> in order to use this module.');
use Joomla\CMS\Helper\ModuleHelper;
$class_sfx  = htmlspecialchars($params->get('class_sfx', ''), ENT_COMPAT, 'UTF-8');
require ModuleHelper::getLayoutPath('mod_astroid_menu', $params->get('layout', 'default'));
