<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

extract($displayData);

$astDocument = Astroid\Framework::getDocument();
$astDocument->loadFontAwesome();


$document = \JFactory::getDocument();
$document->addStyleSheet('../media/astroid/assets/vendor/astroidmenuoptions/dist/index.css');
$document->addScript('../media/astroid/assets/vendor/astroidmenuoptions/dist/index.js', '', ['type' => 'module']);
$document->addScriptDeclaration("(function ($) {
   $(document).ready(function () {
      $('#fieldset-astroidmenu').find( '.control-label' ).remove();
   });
})(jQuery)");
$document->addCustomTag($astDocument->getStylesheets());
$astroidMenu = [
    'id' => $id,
    'name' => $name,
    'value' => $value,
    'language' => [
        'TPL_ASTROID_MENU_OPTIONS' => JText::_('TPL_ASTROID_MENU_OPTIONS'),
        'TPL_ASTROID_MENU_TEXT' => JText::_('TPL_ASTROID_MENU_TEXT'),
        'TPL_ASTROID_MEGA_MENU' => JText::_('TPL_ASTROID_MEGA_MENU'),
        'TPL_ASTROID_SHOW_TITLE' => JText::_('TPL_ASTROID_SHOW_TITLE'),
        'TPL_ASTROID_SUBTITLE' => JText::_('TPL_ASTROID_SUBTITLE'),
        'TPL_ASTROID_ICON' => JText::_('TPL_ASTROID_ICON'),
        'TPL_ASTROID_SELECT_ICON' => JText::_('TPL_ASTROID_SELECT_ICON'),
        'ASTROID_CUSTOM_CLASS' => JText::_('ASTROID_CUSTOM_CLASS'),
        'TPL_ASTROID_MENU_OPTIONS_WIDTH' => JText::_('TPL_ASTROID_MENU_OPTIONS_WIDTH'),
        'TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH' => JText::_('TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH'),
        'TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT' => JText::_('TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT'),
        'TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT' => JText::_('TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT'),
        'JGLOBAL_LEFT' => JText::_('JGLOBAL_LEFT'),
        'JGLOBAL_RIGHT' => JText::_('JGLOBAL_RIGHT'),
        'JGLOBAL_CENTER' => JText::_('JGLOBAL_CENTER'),
        'TPL_ASTROID_CONTAINER' => JText::_('TPL_ASTROID_CONTAINER'),
        'TPL_ASTROID_FULL' => JText::_('TPL_ASTROID_FULL'),
        'TPL_ASTROID_MEGA_MENU_OPTIONS' => JText::_('TPL_ASTROID_MEGA_MENU_OPTIONS'),
        'TPL_ASTROID_MEGA_MENU_TEXT' => JText::_('TPL_ASTROID_MEGA_MENU_TEXT'),
        'TPL_ASTROID_EDIT_GRID_ROW' => JText::_('TPL_ASTROID_EDIT_GRID_ROW'),
        'TPL_ASTROID_REMOVE_ROW' => JText::_('TPL_ASTROID_REMOVE_ROW'),
        'TPL_ASTROID_REMOVE_ELEMENT' => JText::_('TPL_ASTROID_REMOVE_ELEMENT'),
        'TPL_ASTROID_ADD_ELEMENT_COLUMN' => JText::_('TPL_ASTROID_ADD_ELEMENT_COLUMN'),
        'TPL_ASTROID_MENU_OPTIONS_ADD_ROW' => JText::_('TPL_ASTROID_MENU_OPTIONS_ADD_ROW'),
        'TPL_ASTROID_MEGA_BADGE_OPTIONS' => JText::_('TPL_ASTROID_MEGA_BADGE_OPTIONS'),
        'TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT' => JText::_('TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT'),
        'TPL_ASTROID_MENU_BADGE' => JText::_('TPL_ASTROID_MENU_BADGE'),
        'TPL_ASTROID_MENU_BADGE_TEXT' => JText::_('TPL_ASTROID_MENU_BADGE_TEXT'),
        'TPL_ASTROID_MENU_BADGE_COLOR' => JText::_('TPL_ASTROID_MENU_BADGE_COLOR'),
        'TPL_ASTROID_MENU_BADGE_BGCOLOR' => JText::_('TPL_ASTROID_MENU_BADGE_BGCOLOR'),
    ]
]
?>
<script type="application/json" id="astroid-menu-params"><?php echo json_encode($astroidMenu); ?></script>
<div id="astroid-menu-options"></div>
