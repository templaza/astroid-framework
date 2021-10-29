<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
$plugin_params = Astroid\Helper::getPluginParams();
if (!$plugin_params->get('astroid_shortcut_enable', 1)) {
    return;
}
?>
<div id="astroidUnderlay" class="astroid-underlay astroid-isVisible">
    <div id="helpModal" class="astroid-modal">
        <div class="modal-heading">
            <h3 class="m-0"><?php echo JText::_('ASTROID_KEYBOARD_SHORTCUTS'); ?></h3>
            <div id="helpClose" class="astroid-close">&times;</div>
        </div>
        <div id="helpModalContent" class="astroid-modal-content p-0">
            <div id="helpListWrap" class="astroid-list-wrap">
                <div class="table-responsive p-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo JText::_('ASTROID_SHORTCUT_ACTION_LABEL'); ?></th>
                                <th><?php echo JText::_('ASTROID_SHORTCUT_LABEL'); ?></th>
                                <th><?php echo JText::_('ASTROID_SHORTCUT_DESCRIPTION_LABEL'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_SAVE'); ?></td>
                                <td>
                                    <div class="pb-2"><span class="badge badge-light p-2">Ctrl</span><span class="px-1">+</span><span class="badge badge-light p-2">S</span></div>
                                    <div><span class="badge badge-light p-2">⌘</span><span class="px-1">+</span><span class="badge badge-light p-2">S</span></div>
                                </td>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_SAVE_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_PREVIEW'); ?></td>
                                <td>
                                    <div class="pb-2"><span class="badge badge-light p-2">Ctrl</span><span class="px-1">+</span><span class="badge badge-light p-2">P</span></div>
                                    <div><span class="badge badge-light p-2">⌘</span><span class="px-1">+</span><span class="badge badge-light p-2">P</span></div>
                                </td>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_PREVIEW_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_CACHE'); ?></td>
                                <td>
                                    <div class="pb-2"><span class="badge badge-light p-2"><?php echo JText::_('ASTROID_SHORTCUT_DELETE'); ?></span></div>
                                    <div><span class="badge badge-light p-2"><?php echo JText::_('ASTROID_SHORTCUT_DEL'); ?></span></div>
                                </td>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_CACHE_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_CLOSE_POPUP'); ?></td>
                                <td>
                                    <div class="pb-2"><span class="badge badge-light p-2"><?php echo JText::_('ASTROID_SHORTCUT_ESC'); ?></span></div>
                                </td>
                                <td><?php echo JText::_('ASTROID_SHORTCUT_CLOSE_POPUP_DESC'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>