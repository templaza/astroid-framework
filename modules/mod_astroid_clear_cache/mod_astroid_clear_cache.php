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

$joomlaFilesExtensionId = ExtensionHelper::getExtensionRecord('joomla', 'file')->extension_id;
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('mod_astroid_clear_cache_notify', 'astroid/mod_astroid_clear_cache/notify.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
$wa->registerAndUseScript('mod_astroid_clear_cache_script', 'astroid/mod_astroid_clear_cache/script.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
require ModuleHelper::getLayoutPath('mod_astroid_clear_cache', $params->get('layout', 'default'));
