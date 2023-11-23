<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
defined('JPATH_BASE') or die;

extract($displayData);

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('astroid.menuoptions.css', 'media/astroid/assets/vendor/astroidmenuoptions/dist/index.css');
$wa->registerAndUseScript('astroid.menuoptions.js', 'media/astroid/assets/vendor/astroidmenuoptions/dist/index.js', ['relative' => true, 'version' => 'auto'], ['type' => 'module']);
$wa->addInlineScript("(function ($) {
   $(document).ready(function () {
      $('#fieldset-astroidmenu').find( '.control-label' ).remove();
   });
})(jQuery)");

// Get Menu
$app = Factory::getApplication('site');
$menu = $app->getMenu('site');

$itemId = $app->input->get('id');
$menuItem = $menu->getItem($itemId);
if ($menuItem === null || (isset($menuItem->language) && $menuItem->language == '*')) {
    $items = $menu->getItems(['menutype'], $menu_type);
} else {
    $items = $menu->getItems(['menutype', 'language'], [$menu_type, $menuItem->language]);
}

$children = [];

foreach ($items as $i => $item) {
    if ($item->parent_id != $menu_item_id) {
        continue;
    }
    $children[] = $item;
}

$astroidMenu = [
    'id' => $id,
    'name' => $name,
    'value' => $value,
    'options' => [
        'submenu' => $children,
        'module' => Astroid\Helper::getModules()
    ],
    'language' => [
        'TPL_ASTROID_MENU_OPTIONS' => Text::_('TPL_ASTROID_MENU_OPTIONS'),
        'TPL_ASTROID_MENU_TEXT' => Text::_('TPL_ASTROID_MENU_TEXT'),
        'TPL_ASTROID_MEGA_MENU' => Text::_('TPL_ASTROID_MEGA_MENU'),
        'TPL_ASTROID_SHOW_TITLE' => Text::_('TPL_ASTROID_SHOW_TITLE'),
        'TPL_ASTROID_SUBTITLE' => Text::_('TPL_ASTROID_SUBTITLE'),
        'TPL_ASTROID_ICON' => Text::_('TPL_ASTROID_ICON'),
        'TPL_ASTROID_SELECT_ICON' => Text::_('TPL_ASTROID_SELECT_ICON'),
        'ASTROID_CUSTOM_CLASS' => Text::_('ASTROID_CUSTOM_CLASS'),
        'TPL_ASTROID_MENU_OPTIONS_WIDTH' => Text::_('TPL_ASTROID_MENU_OPTIONS_WIDTH'),
        'TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH' => Text::_('TPL_ASTROID_MENU_OPTIONS_MEGAMENU_WIDTH'),
        'TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT' => Text::_('TPL_ASTROID_MENU_OPTIONS_DROPDOWN_ALIGNMENT'),
        'TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT' => Text::_('TPL_ASTROID_MENU_OPTIONS_SELECT_ALIGNMENT'),
        'JGLOBAL_LEFT' => Text::_('JGLOBAL_LEFT'),
        'JGLOBAL_RIGHT' => Text::_('JGLOBAL_RIGHT'),
        'JGLOBAL_CENTER' => Text::_('JGLOBAL_CENTER'),
        'TPL_ASTROID_CONTAINER' => Text::_('TPL_ASTROID_CONTAINER'),
        'TPL_ASTROID_FULL' => Text::_('TPL_ASTROID_FULL'),
        'TPL_ASTROID_MEGA_MENU_OPTIONS' => Text::_('TPL_ASTROID_MEGA_MENU_OPTIONS'),
        'TPL_ASTROID_MEGA_MENU_TEXT' => Text::_('TPL_ASTROID_MEGA_MENU_TEXT'),
        'TPL_ASTROID_EDIT_GRID_ROW' => Text::_('TPL_ASTROID_EDIT_GRID_ROW'),
        'TPL_ASTROID_REMOVE_ROW' => Text::_('TPL_ASTROID_REMOVE_ROW'),
        'TPL_ASTROID_REMOVE_ELEMENT' => Text::_('TPL_ASTROID_REMOVE_ELEMENT'),
        'TPL_ASTROID_ADD_ELEMENT_COLUMN' => Text::_('TPL_ASTROID_ADD_ELEMENT_COLUMN'),
        'TPL_ASTROID_MENU_OPTIONS_ADD_ROW' => Text::_('TPL_ASTROID_MENU_OPTIONS_ADD_ROW'),
        'TPL_ASTROID_MEGA_BADGE_OPTIONS' => Text::_('TPL_ASTROID_MEGA_BADGE_OPTIONS'),
        'TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT' => Text::_('TPL_ASTROID_MEGA_BADGE_OPTIONS_TEXT'),
        'TPL_ASTROID_MENU_BADGE' => Text::_('TPL_ASTROID_MENU_BADGE'),
        'TPL_ASTROID_MENU_BADGE_TEXT' => Text::_('TPL_ASTROID_MENU_BADGE_TEXT'),
        'TPL_ASTROID_MENU_BADGE_COLOR' => Text::_('TPL_ASTROID_MENU_BADGE_COLOR'),
        'TPL_ASTROID_MENU_BADGE_BGCOLOR' => Text::_('TPL_ASTROID_MENU_BADGE_BGCOLOR'),
    ]
]
?>
<script type="application/json" id="astroid-menu-params"><?php echo json_encode($astroidMenu); ?></script>
<div id="astroid-menu-options"></div>
