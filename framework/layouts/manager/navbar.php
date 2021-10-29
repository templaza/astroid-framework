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
$template = Astroid\Framework::getTemplate();
?>
<div class="astroid-manager-navbar fixed-top m-0 row">
    <ul class="list-unstyled m-md-0 m-auto col-auto p-0">
        <li class="float-left">
            <div class="astroid-sidebar-header-toggle" onclick="Admin.toggleSidebar()">
                <span class="fas fa-chevron-left"></span>
            </div>
        </li>
        <li class="float-left">
            <button id="save-options" class="astroid-sidebar-btn align-items-center text-white" type="button">
                <div>
                    <i class="far fa-save"></i>
                    <span><?php echo JText::_('ASTROID_SAVE'); ?></span>
                </div>
            </button>
            <a href="javascript:void(0);" id="saving-options" class="astroid-sidebar-btn align-items-center d-none">
                <div>
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span><?php echo JText::_('ASTROID_TEMPLATE_SAVING'); ?></span>
                </div>
            </a>
        </li>
        <li class="float-left">
            <a id="clear-cache" href="javascript:void(0);" class="astroid-sidebar-btn align-items-center bg-light text-dark">
                <div>
                    <i class="fas fa-eraser"></i>
                    <span><?php echo JText::_('ASTROID_TEMPLATE_CLEAR_CACHE'); ?></span>
                </div>
            </a>
            <a id="clearing-cache" href="javascript:void(0);" class="astroid-sidebar-btn align-items-center bg-light text-dark d-none">
                <div>
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span><?php echo JText::_('ASTROID_TEMPLATE_CLEARING_CACHE'); ?></span>
                </div>
            </a>
        </li>
        <li class="float-left">
            <a id="show-previews" href="<?php echo JURI::root(); ?>" target="_blank" class="astroid-sidebar-btn d-flex align-items-center bg-light  text-dark">
                <div>
                    <i class="fas fa-external-link-alt"></i>
                    <span><?php echo JText::_('ASTROID_TEMPLATE_PREVIEW'); ?></span>
                </div>
            </a>
        </li>
    </ul>
    <div class="col-md col-8 p-0 template-title text-ellipsis">
        <?php echo $template->title; ?>
    </div>
    <ul class="list-inline m-0 col-sm-auto col-4 p-0">
        <li class="float-sm-left float-right">
            <a id="close-editor" title="<?php echo JText::_('TPL_ASTROID_BACK_TO_JOOMLA'); ?>" href="<?php echo Astroid\Helper::getJoomlaUrl(); ?>" class="astroid-sidebar-btn astroid-back-btn d-flex align-items-center">
                <div>
                    <i class="fas fa-times"></i>
                    <span><?php echo JText::_('ASTROID_TEMPLATE_CLOSE'); ?></span>
                </div>
            </a>
        </li>
    </ul>
</div>