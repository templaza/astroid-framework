<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$assets = JURI::root() . 'media' . '/' . 'astroid' . '/' . 'assets' . '/';
$app = JFactory::getApplication();
$atm = $app->input->get('atm', 0, 'INT');

if ($atm) {
   $joomla_url = \JRoute::_('index.php?option=com_advancedtemplates');
} else {
   $joomla_url = \JRoute::_('index.php?option=com_templates');
}
?>
<div class="astroid-manager-navbar w-100 fixed-top m-0 row">
    <div class="d-flex justify-content-center align-items-center">
        <div>
            <img style="vertical-align: baseline;" width="150" src="<?php echo $assets . 'images' . '/' . 'logo-dark-wide.png'; ?>" /> <span style="color: #009688;">auditor</span>
        </div>
    </div>
    <ul class="list-inline ml-auto mb-0 p-0 text-right">
        <li class="d-inline-block"><a id="close-editor" title="<?php echo JText::_('TPL_ASTROID_BACK_TO_JOOMLA'); ?>" href="<?php echo $joomla_url; ?>" class="astroid-sidebar-btn astroid-back-btn d-flex align-items-center">
                <div><i class="fas fa-times"></i><span><?php echo JText::_('ASTROID_TEMPLATE_CLOSE'); ?></span></div>
            </a></li>
    </ul>
</div>