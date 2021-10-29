<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
?>
<div class="ezlb-pop" id="element-settings">
    <div class="ezlb-pop-overlay"></div>
    <div class="ezlb-pop-body">
        <div class="astroid-ring-loading"></div>
        <div id="element-settings-form" ng-bind-html="elementFormContent"></div>
        <div class="ezlb-pop-footer text-right">
            <button type="button" id="element-settings-save" class="btn btn-lg btn-wide btn-round btn-astroid"><?php echo JText::_('ASTROID_SAVE'); ?></button>
        </div>
    </div>
</div>

<div class="ezlb-pop" id="astroid-import-confirm">
    <div class="ezlb-pop-overlay"></div>
    <div class="ezlb-pop-body text-center">
        <h1><?php echo JText::_('ASTROID_IMPORT_POPUP_TITLE'); ?></h1>
        <p><?php echo JText::_('ASTROID_IMPORT_POPUP_DESC'); ?></p>
        <strong><label><input type="checkbox" id="astroid-import-option" /> <?php echo JText::_('ASTROID_IMPORT_POPUP_OPTION'); ?></label></strong>
        <div class="row mt-3">
            <div class="col text-right">
                <button id="astroid-import-cancel" type="button" class="btn btn-lg btn-wide btn-round astroid-back-btn">Cancel</button>
            </div>
            <div class="col text-left">
                <button id="astroid-import-continue" type="button" class="btn btn-lg btn-wide btn-round btn-astroid">Continue</button>
            </div>
        </div>
    </div>
</div>