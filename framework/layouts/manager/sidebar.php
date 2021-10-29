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
$form = Astroid\Framework::getForm();
?>
<div id="astroid-sidebar-wrapper" class="col">
    <div class="astroid-logo text-center row">
        <div class="logo-image">
            <img style="vertical-align: baseline;" width="150" src="<?php echo ASTROID_MEDIA_URL . 'images/logo-dark-wide.png'; ?>" /> <span style="color: #8E2DE2;"><?php echo Astroid\Helper\Constants::$astroid_version; ?></span>
        </div>
    </div>
    <div class="astroid-logo text-center row d-none">
        <div class="logo-image">
            <img width="40" src="<?php echo ASTROID_MEDIA_URL . 'images/icon-logo-dark.png'; ?>" />
            <div class="d-inline ml-2">
                <img width="110" src="<?php echo ASTROID_MEDIA_URL . 'images/logo-dark-wide.png'; ?>" />
                <div class="clearfix"></div>
                <small style="position: relative;top: -12px;margin-left: 128px;" class="astroid-version">v <?php echo Astroid\Helper\Constants::$astroid_version; ?></small>
            </div>
        </div>
    </div>
    <div class="astroid-sidebar-toggle" onclick="Admin.toggleSidebar()">
        <span class="fas fa-chevron-right"></span>
    </div>
    <ul id="astroid-menu" class="nav flex-column sidebar-nav" role="tablist">
        <?php $active = false; ?>
        <?php foreach ($form->getFieldsets() as $key => $fieldset) { ?>
            <?php $fields = $form->getFields($key); ?>
            <?php
            $groups = [];
            foreach ($fields as $key => $field) {
                if ($field->type == 'astroidgroup') {
                    $groups[$field->fieldname] = ['title' => JText::_($field->getAttribute('title', '')), 'icon' => $field->getAttribute('icon', '')];
                }
            }
            ?>
            <li data-sidebar-tooltip="<?php echo JText::_($fieldset->label); ?>" class="nav-item row<?php echo !empty($groups) ? ' has-child' : ''; ?>">
                <a data-toggle="tab" id="<?php echo $fieldset->name; ?>-astroid-tab" class="nav-link<?php echo $active ? ' active' : ''; ?> col-12" data-target="#astroid-tab-<?php echo $fieldset->name; ?>" href="javascript:void(0);" role="tab" aria-controls="astroid-tab-<?php echo $fieldset->name; ?>" aria-selected="<?php echo $active ? 'true' : 'false'; ?>">
                    <?php if (!empty($fieldset->icon)) { ?>
                        <i class="<?php echo $fieldset->icon; ?>"></i>
                    <?php } ?>
                    <span><?php echo JText::_($fieldset->label); ?></span>
                </a>
                <?php if (!empty($groups)) { ?>
                    <ul id="fieldset-groupmenu-<?php echo $fieldset->name; ?>" class="nav flex-column sidebar-submenu">
                        <?php foreach ($groups as $groupname => $group) { ?>
                            <li class="nav-item"><a class="nav-link hash-link" href="#astroid-form-fieldset-section-<?php echo $groupname; ?>"><?php echo !empty($group['icon']) ? '<i class="' . $group['icon'] . '"></i>' : ''; ?><span><?php echo $group['title']; ?></span></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
            <?php $active = false; ?>
        <?php } ?>
        <li data-sidebar-tooltip="<?php echo JText::_('TPL_ASTROID_EXPORT_PRESET'); ?>" class="nav-item row">
            <a id="export-preset" ng-click="exportPreset()" class="nav-link col-12" href="javascript:void(0);">
                <i class="fas fa-palette"></i>
                <span><?php echo JText::_('TPL_ASTROID_EXPORT_PRESET'); ?></span>
            </a>
        </li>
        <li data-sidebar-tooltip="<?php echo JText::_('TPL_ASTROID_EXPORT'); ?>" class="nav-item row">
            <a id="export-options" class="nav-link col-12" href="javascript:void(0);">
                <i class="fas fa-download"></i>
                <span><?php echo JText::_('TPL_ASTROID_EXPORT'); ?></span>
            </a>
        </li>
        <li data-sidebar-tooltip="<?php echo JText::_('TPL_ASTROID_IMPORT'); ?>" class="nav-item row">
            <a id="import-options" class="nav-link col-12" href="javascript:void(0);">
                <i class="fas fa-upload"></i>
                <span><?php echo JText::_('TPL_ASTROID_IMPORT'); ?></span>
            </a>
        </li>
        <li data-sidebar-tooltip="<?php echo JText::_('TPL_ASTROID_CLOSE_LIVEPREVIEW'); ?>" class="nav-item row showin-live-preview">
            <a class="nav-link col-12" href="javascript:void(0);" onclick="Admin.closeLivePreview()">
                <i class="far fa-eye"></i>
                <span><?php echo JText::_('TPL_ASTROID_CLOSE_LIVEPREVIEW'); ?></span>
            </a>
        </li>
        <li data-sidebar-tooltip="<?php echo JText::_('TPL_ASTROID_BACK_TO_JOOMLA'); ?>" class="nav-item row showin-live-preview">
            <a class="nav-link col-12" href="<?php echo Astroid\Helper::getJoomlaUrl(); ?>">
                <i class="fab fa-joomla"></i>
                <span><?php echo JText::_('TPL_ASTROID_BACK_TO_JOOMLA'); ?></span>
            </a>
        </li>
    </ul>
</div>

<?php
$document = Astroid\Framework::getDocument();
$script = '
$(function () {
    $(\'[data-sidebar-tooltip="tooltip"]\').tooltip({ placement: \'right\', title: function(){
        return $(this).data(\'sidebar-tooltip\');
    } });
})
';
$document->addScriptDeclaration($script, 'body');
?>