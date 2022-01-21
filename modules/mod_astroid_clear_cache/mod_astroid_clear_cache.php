<?php
/**
 * @package Jollyany
 * @author TemPlaza http://www.templaza.com
 * @copyright Copyright (c) 2010 - 2022 Jollyany
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

$joomlaFilesExtensionId = ExtensionHelper::getExtensionRecord('joomla', 'file')->extension_id;
$document   =   JFactory::getDocument();
HTMLHelper::_('jquery.framework');
$document->addScript(JUri::base('true').'/modules/mod_astroid_clear_cache/js/notify.min.js');
$document->addScript(JUri::base('true').'/modules/mod_astroid_clear_cache/js/script.js');
require ModuleHelper::getLayoutPath('mod_astroid_clear_cache', $params->get('layout', 'default'));
