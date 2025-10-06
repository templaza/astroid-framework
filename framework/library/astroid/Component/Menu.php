<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Component;

defined('_JEXEC') or die;

use Astroid\Framework;
use Joomla\Module\Menu\Site\Helper\MenuHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Multilanguage;

class Menu
{

    public static $parentlist = [];

    public static function getMenu($menutype = '', $nav_class = [], $logo = null, $logoOdd = 'left', $headerType = 'horizontal', $nav_wrapper_class = [], $startLevel = null, $endLevel = null, $base=null)
    {
        if (empty($menutype)) {
            return '';
        }

        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript('astroid.megamenu', 'astroid/megamenu.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
        $wa->registerAndUseScript('astroid.jquery.hoverIntent', 'astroid/jquery.hoverIntent.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);

        $header_endLevel = $params->get('header_endLevel', 0);
        if ($endLevel !== null) {
            $header_endLevel = $endLevel;
        }
        $header_startLevel = $params->get('header_startLevel', 1);
        if ($startLevel !== null) {
            $header_startLevel = $startLevel;
        }
        $header_menu_params = '{"menutype":"' . $menutype . '","base":"'.$base.'","startLevel":"' . $header_startLevel . '","endLevel":"' . $header_endLevel . '","showAllChildren":"1","tag_id":"","class_sfx":"","window_open":"","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';

        $menu_params = new Registry();
        $menu_params->loadString($header_menu_params);

        $list = MenuHelper::getList($menu_params);
        $base = MenuHelper::getBase($menu_params);
        $active = MenuHelper::getActive($menu_params);
        $default = MenuHelper::getDefault();

        $active_id = $active->id;
        $default_id = $default->id;
        $path = $base->tree;
        $showAll = 1;

        $return = [];
        // Menu Wrapper
        echo '<nav class="' . (!empty($nav_wrapper_class) ? ' ' . implode(' ', $nav_wrapper_class) : '') . '" aria-label="'.$headerType.' menu">'
            . '<ul class="' . implode(' ', $nav_class) . '">';


        $megamenu = false;
        $count_menu = 0;
        foreach ($list as $i => &$item) {
            if ($item->level == 1) {
                $count_menu++;
            }
            if ($item->parent == 1) {
                self::$parentlist[] = $item->id;
            }
        }
        $logo_position = $count_menu / 2;
        $logo_position = (int) $logo_position;
        if ($count_menu % 2 != 0) {
            $logo_position = $logoOdd == 'left' ? $logo_position + 1 : $logo_position;
        }

        $logo_position_count = 0;
        $astroid_menu_options = new \stdClass();
        $li_content = [];

        foreach ($list as $i => &$item) {
            if (in_array($item->id, self::$parentlist)) {
                $item->parent = 1;
            }
            $options = self::getAstroidMenuOptions($item, $list);
            $class = self::getLiClass($item, $options, $default_id, $active_id, $path);

            if ($item->level == 1) {
                // Code for adding Centered Logo
                if (($logo_position_count == $logo_position) && !empty($logo)) {
                    echo '<li class="nav-item nav-stacked-logo text-center px-4">';
                    $document->include('logo');
                    echo '</li>';
                }
                $logo_position_count++;
            }

            if ($options->megamenu && $item->level == 1) {
                echo '<li data-position="' . $options->alignment . '" class="' . \implode(' ', $class) . '">';
                $document->include('header.menu.link', ['item' => $item, 'options' => $options, 'mobilemenu' => false, 'active' => in_array('nav-item-active', $class), 'header' => $headerType]);

                if ((!$header_endLevel && $header_endLevel == 0) || isset($header_endLevel) && $header_endLevel > 1) {
                    echo self::getMegaMenu($item, $options, $list);
                }

                echo '</li>';
            } elseif (!$options->megamenu) {
                echo '<li data-position="' . $options->alignment . '" class="' . \implode(' ', $class) . '">';
                $document->include('header.menu.link', ['item' => $item, 'options' => $options, 'mobilemenu' => false, 'active' => in_array('nav-item-active', $class), 'header' => $headerType]);

                if ($item->level == 1 && $item->parent) {
                    echo '<div class="megamenu-container nav-submenu-container nav-item-level-' . $item->level . '">';
                }
                // The next item is deeper.
                if ($item->deeper) {
                    echo '<ul class="nav-submenu" style="width:' . $options->width . '">';
                }
                // The next item is shallower.
                elseif ($item->shallower) {
                    echo '</li>';
                    $l = $item->level;
                    for ($i = 0; $i < $item->level_diff; $i++) {
                        if ($l > 2) {
                            echo '</ul>' . '</li>';
                        } else {
                            echo '</ul></div>' . '</li>';
                        }
                        $l--;
                    }
                }
                // The next item is on the same level.
                else {
                    if ($item->level == 1 && $item->parent) {
                        echo '</div>';
                    }
                    echo '</li>';
                }
            }
        }

        if (count($list) == 1 && $logo_position == 1 && !empty($logo)) {
            echo '<li class="nav-item nav-stacked-logo text-center">';
            $document->include('logo');
            echo '</li>';
        }

        echo '</ul>'
            . '</nav>';
    }

    // Joomla Functions

    public static function getMegaMenu($item, $options, $items)
    {
        $document = Framework::getDocument();
        if (!empty($options->rows)) {
            echo '<div style="width:' . $options->width . '" class="megamenu-container">';
            foreach ($options->rows as $row) {
                echo '<div class="row">';
                foreach ($row['cols'] as $col) {
                    echo '<div class="col col-md-' . $col['size'] . '">';
                    try {
                        foreach ($col['elements'] as $element) {
                            if ($element['type'] == "module") {
                                $modules = ModuleHelper::getModuleList();
                                foreach ($modules as $module) {
                                    if ($module->id == $element['id']) {
                                        $params = \json_decode($module->params, true);
                                        if (!isset($params['style']) || empty($params['style'])) {
                                            $style = "html5";
                                        } else {
                                            $style = $params['style'];
                                        }
                                        echo '<div class="megamenu-item megamenu-module">';
                                        echo ModuleHelper::renderModule($module, ['style' => $style]);
                                        echo "</div>";
                                    }
                                }
                            } else if ($item->parent) {
                                $base = self::getBase();
                                $active = self::getActive();
                                $default = self::getDefault();
                                $active_id = $active->id;
                                $default_id = $default->id;
                                $path = $base->tree;
                                echo '<ul class="nav-submenu megamenu-submenu-level-1">';
                                foreach ($items as $i => $subitem) {
                                    if ($subitem->id != $element['id']) {
                                        continue;
                                    }
                                    $subitem->anchor_css = empty($subitem->anchor_css) ? 'megamenu-title' : ' ' . $subitem->anchor_css;
                                    $options = self::getAstroidMenuOptions($subitem, $items);
                                    $class = self::getLiClass($subitem, $options, $default_id, $active_id, $path);
                                    echo '<li class="megamenu-menu-item' . (empty($class) ? '' : ' ' . implode(' ', $class)) . '">';
                                    $document->include('header.menu.link', ['item' => $subitem, 'options' => $options, 'mobilemenu' => false, 'active' => in_array('nav-item-active', $class)]);
                                    if ($subitem->parent) {
                                        self::getMegaMenuSubItems($subitem, $items);
                                    }
                                    echo '</li>';
                                }
                                echo '</ul>';
                            }
                        }
                    } catch (\Exception $e) {
                    }
                    echo '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    }

    public static function getMegaMenuSubItems($parent, $listAll)
    {
        $base = self::getBase();
        $active = self::getActive();
        $default = self::getDefault();
        $active_id = $active->id;
        $default_id = $default->id;
        $path = $base->tree;
        $document = Framework::getDocument();

        $return = [];

        $list = [];

        foreach ($listAll as $i => &$item) {
            if ($item->parent_id != $parent->id) {
                continue;
            }
            $list[] = $item;
        }
        if ($parent->level == 2 && ($parent->type == "heading" || $parent->type == "separator")) {
            echo '<ul class="nav-submenu-static d-block">';
        } else {
            echo '<ul class="nav-submenu">';
        }
        foreach ($list as $i => &$item) {
            $options = self::getAstroidMenuOptions($item, $list);
            $class = self::getLiClass($item, $options, $default_id, $active_id, $path);

            echo '<li class="' . \implode(' ', $class) . '">';
            $document->include('header.menu.link', ['item' => $item, 'options' => $options, 'mobilemenu' => false, 'active' => in_array('nav-item-active', $class)]);
            if ($item->parent) {
                self::getMegaMenuSubItems($item, $listAll);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    public static function getList($menutype)
    {
        $app = Factory::getApplication();
        $menu = $app->getMenu();

        // Get active menu item
        $base = self::getBase();
        $user = Factory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        asort($levels);

        $path = $base->tree;
        $start = 1;
        $end = 0;
        $showAll = 1;
        $items = $menu->getItems('menutype', $menutype);
        $hidden_parents = array();
        $lastitem = 0;

        $item_params = $item->getParams();
        if ($items) {
            foreach ($items as $i => $item) {
                $item->parent = false;

                if (isset($items[$lastitem]) && $items[$lastitem]->id == $item->parent_id && $item_params->get('menu_show', 1) == 1) {
                    $items[$lastitem]->parent = true;
                }

                if (($start && $start > $item->level) || ($end && $item->level > $end) || (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path)) || ($start > 1 && !in_array($item->tree[$start - 2], $path))) {
                    unset($items[$i]);
                    continue;
                }

                // Exclude item with menu item option set to exclude from menu modules
                if (($item_params->get('menu_show', 1) == 0) || in_array($item->parent_id, $hidden_parents)) {
                    $hidden_parents[] = $item->id;
                    unset($items[$i]);
                    continue;
                }

                $item->deeper = false;
                $item->shallower = false;
                $item->level_diff = 0;

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
                    $items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
                    $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                }

                $lastitem = $i;
                $item->active = false;
                $item->flink = $item->link;

                // Reverted back for CMS version 2.5.6
                switch ($item->type) {
                    case 'separator':
                        break;

                    case 'heading':
                        // No further action needed.
                        break;

                    case 'url':
                        if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                            // If this is an internal Joomla link, ensure the Itemid is set.
                            $item->flink = $item->link . '&Itemid=' . $item->id;
                        }
                        break;

                    case 'alias':
                        $item->flink = 'index.php?Itemid=' . $item_params->get('aliasoptions');
                        break;

                    default:
                        $item->flink = 'index.php?Itemid=' . $item->id;
                        break;
                }

                if ((strpos($item->flink, 'index.php?') !== false) && strcasecmp(substr($item->flink, 0, 4), 'http')) {
                    $item->flink = Route::_($item->flink, true, $item_params->get('secure'));
                } else {
                    $item->flink = Route::_($item->flink);
                }

                // We prevent the double encoding because for some reason the $item is shared for menu modules and we get double encoding
                // when the cause of that is found the argument should be removed
                $item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
                $item->anchor_css = htmlspecialchars($item_params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
                $item->anchor_title = htmlspecialchars($item_params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
                $item->anchor_rel = htmlspecialchars($item_params->get('menu-anchor_rel', ''), ENT_COMPAT, 'UTF-8', false);
                $item->menu_image = $item_params->get('menu_image', '') ?
                    htmlspecialchars($item_params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
                $item->menu_image_css = htmlspecialchars($item_params->get('menu_image_css', ''), ENT_COMPAT, 'UTF-8', false);
            }

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper = (($start ?: 1) > $items[$lastitem]->level);
                $items[$lastitem]->shallower = (($start ?: 1) < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
            }
        }

        return $items;
    }

    public static function getBase()
    {
        $menu = Factory::getApplication()->getMenu();
        $active = $menu->getActive();


        if ($active) {
            return $active;
        }

        return self::getActive();
    }

    public static function getActive()
    {
        $menu = Factory::getApplication()->getMenu();
        return $menu->getActive() ?: self::getDefault();
    }

    public static function getDefault()
    {
        $menu = Factory::getApplication()->getMenu();
        $lang = Factory::getLanguage();

        // Look for the home menu
        if (Multilanguage::isEnabled()) {
            return $menu->getDefault($lang->getTag());
        } else {
            return $menu->getDefault();
        }
    }

    public static function getAstroidMenuOptions($item, $list)
    {
        $item_params = $item->getParams();
        $astroid_menu_options = $item_params->get('astroid_menu_options', []);
        $astroid_menu_options = (array) $astroid_menu_options;
        // set defaults
        $data = new \stdClass();
        $data->megamenu = 0;
        $data->icononly = 0;
        $data->subtitle = '';
        $data->icon = '';
        $data->customclass = '';
        $data->width = '';
        $data->alignment = '';
        $data->rows = [];
        $data->badge = 0;
        $data->badge_text = '';
        $data->badge_color = '#FFF';
        $data->badge_bgcolor = '#000';

        if (isset($astroid_menu_options['megamenu']) && (int) $astroid_menu_options['megamenu']) {
            $data->megamenu = 1;
        }
        if (isset($astroid_menu_options['showtitle']) && (int) $astroid_menu_options['showtitle']) {
            $data->icononly = 1;
        }
        if (isset($astroid_menu_options['subtitle']) && !empty($astroid_menu_options['subtitle'])) {
            $data->subtitle = $astroid_menu_options['subtitle'];
        }
        if (isset($astroid_menu_options['icon']) && !empty($astroid_menu_options['icon'])) {
            $data->icon = $astroid_menu_options['icon'];
        }
        if (isset($astroid_menu_options['customclass']) && !empty($astroid_menu_options['customclass'])) {
            $data->customclass = $astroid_menu_options['customclass'];
        }
        if (isset($astroid_menu_options['rows']) && !empty($astroid_menu_options['rows'])) {
            $data->rows = \json_decode($astroid_menu_options['rows'], true);
        }
        if (!$data->megamenu) {
            if (isset($astroid_menu_options['width']) && !empty($astroid_menu_options['width'])) {
                $data->width = $astroid_menu_options['width'];
            } else {
                $data->width = '320px';
            }
            if (isset($astroid_menu_options['alignment']) && !empty($astroid_menu_options['alignment'])) {
                $data->alignment = $astroid_menu_options['alignment'];
            } else {
                $data->alignment = 'right';
            }
        } else {
            if (isset($astroid_menu_options['megamenu_width']) && !empty($astroid_menu_options['megamenu_width'])) {
                $data->width = $astroid_menu_options['megamenu_width'];
            } else {
                $data->width = '980px';
            }
            if (isset($astroid_menu_options['megamenu_direction']) && !empty($astroid_menu_options['megamenu_direction'])) {
                $data->alignment = $astroid_menu_options['megamenu_direction'];
            } else {
                $data->alignment = 'center';
            }
        }
        if ($data->alignment == 'full') {
            $data->width = '100vw';
        }
        if ($data->alignment == 'edge') {
            $data->width = '100vw';
        }

        if ($item->level > 1) {
            $data->megamenu = self::isParentMegamenu($item->parent_id, $list);
        }
        if (isset($astroid_menu_options['badge']) && (int) $astroid_menu_options['badge']) {
            $data->badge = 1;
        }
        if (isset($astroid_menu_options['badge_text']) && $astroid_menu_options['badge_text']) {
            $data->badge_text = $astroid_menu_options['badge_text'];
        }
        if (isset($astroid_menu_options['badge_color']) && $astroid_menu_options['badge_color']) {
            $data->badge_color = $astroid_menu_options['badge_color'];
        }
        if (isset($astroid_menu_options['badge_bgcolor']) && $astroid_menu_options['badge_bgcolor']) {
            $data->badge_bgcolor = $astroid_menu_options['badge_bgcolor'];
        }

        return $data;
    }

    public static function isParentMegamenu($pid, $list)
    {
        $parent = null;
        foreach ($list as $item) {
            if ($item->id == $pid) {
                $parent = $item;
                break;
            }
        }
        if ($parent === null) {
            return 0;
        }
        if ($parent->level > 1) {
            return self::isParentMegamenu($parent->parent_id, $list);
        } else {
            $options = self::getAstroidMenuOptions($parent, $list);
            return $options->megamenu;
        }
    }

    public static function getLiClass($item, $options, $default_id, $active_id, $path)
    {
        $item_params = $item->getParams();
        $params = Framework::getTemplate()->getParams();

        $header_endLevel = $params->get('header_endLevel', 0);

        $class = [];
        if ($item->level != 1) {
            $class[] = 'nav-item-submenu';
        } else {
            $class[] = 'nav-item';
        }
        $class[] = 'nav-item-id-' . $item->id;
        $class[] = 'nav-item-level-' . $item->level;

        if ($item->id == $default_id) {
            $class[] = 'nav-item-default';
        }

        if ($item->id == $active_id || ($item->type === 'alias' && $item_params->get('aliasoptions') == $active_id)) {
            $class[] = 'nav-item-current';
        }

        if (in_array($item->id, $path)) {
            $class[] = 'nav-item-active';
        } elseif ($item->type === 'alias') {
            $aliasToId = $item_params->get('aliasoptions');
            if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                $class[] = 'nav-item-active';
            } elseif (in_array($aliasToId, $path)) {
                $class[] = 'nav-item-alias-parent-active';
            }
        }

        if ($item->type === 'separator') {
            $class[] = 'nav-item-divider';
        }

        if ($item->deeper) {
            $class[] = 'nav-item-deeper';
        }

        if (($item->parent || $options->megamenu) && ($item->level != $header_endLevel)) {
            $class[] = 'nav-item-parent';
        }
        if ((($item->parent || $options->megamenu) && $item->level == 1) && ($item->level != $header_endLevel)) {
            $class[] = 'has-megamenu';
        }

        if ($options->megamenu) {
            $class[] = 'nav-item-megamenu';
        } else if (($item->parent) && ($item->level != $header_endLevel)) {
            $class[] = 'nav-item-dropdown';
        }

        if (!empty($options->customclass)) {
            $class[] = $options->customclass;
        }

        if (!$params->get('dropdown_arrow', 0)) {
            $class[] = 'no-dropdown-icon';
        }
        return $class;
    }

    public static function getMobileMenu($menutype = '', $nav_class = [])
    {
        if (empty($menutype)) {
            return '';
        }

        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();

        $header_menu_params = '{"menutype":"' . $menutype . '","base":"","startLevel":"' . $params->get('header_mobile_startLevel', 1) . '","endLevel":"' . $params->get('header_mobile_endLevel', 0) . '","showAllChildren":"1","tag_id":"","class_sfx":"","window_open":"","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';

        $menu_params = new Registry();
        $menu_params->loadString($header_menu_params);

        $list = MenuHelper::getList($menu_params);
        $base = MenuHelper::getBase($menu_params);
        $active = MenuHelper::getActive($menu_params);
        $default = MenuHelper::getDefault();

        $active_id = $active->id;
        $default_id = $default->id;
        $path = $base->tree;
        $showAll = 1;

        echo '<ul class="astroid-mobile-menu d-none' . (empty($nav_class) ? '' : ' ' . implode(' ', $nav_class)) . '">';
        $megamenu = false;
        $count_menu = 0;
        foreach ($list as $i => &$item) {
            if ($item->level == 1) {
                $count_menu++;
            }
        }
        foreach ($list as $i => &$item) {
            $options = self::getAstroidMenuOptions($item, $list);
            $class = self::getLiClass($item, $options, $default_id, $active_id, $path);
            echo '<li class="' . \implode(' ', $class) . '">';
            $document->include('header.menu.link', ['item' => $item, 'options' => $options, 'mobilemenu' => true, 'active' => in_array('nav-item-active', $class)]);
            if ($item->deeper) {
                echo '<ul class="nav-child list-group navbar-subnav level-' . $item->level . '">';
            } elseif ($item->shallower) {
                echo '</li>';
                echo str_repeat('</ul></li>', $item->level_diff);
            } else {
                echo '</li>';
            }
        }
        echo '</ul>';
    }

    public static function getSidebarMenu($menutype = '')
    {
        if (empty($menutype)) {
            return '';
        }

        $params = Framework::getTemplate()->getParams();
        $document = Framework::getDocument();

        $header_menu_params = '{"menutype":"' . $menutype . '","base":"","startLevel":"1","endLevel":"' . $params->get('header_endLevel', 0) . '","showAllChildren":"1","tag_id":"","class_sfx":"","window_open":"","layout":"_:default","moduleclass_sfx":"","cache":"1","cache_time":"900","cachemode":"itemid","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';

        $menu_params = new Registry();
        $menu_params->loadString($header_menu_params);

        $list = MenuHelper::getList($menu_params);
        $base = MenuHelper::getBase($menu_params);
        $active = MenuHelper::getActive($menu_params);
        $default = MenuHelper::getDefault();

        $active_id = $active->id;
        $default_id = $default->id;
        $path = $base->tree;
        $showAll = 1;

        echo '<ul class="astroid-sidebar-menu">';
        $megamenu = false;
        $count_menu = 0;
        foreach ($list as $i => &$item) {
            if ($item->level == 1) {
                $count_menu++;
            }
        }
        foreach ($list as $i => &$item) {
            $options = self::getAstroidMenuOptions($item, $list);
            $class = self::getLiClass($item, $options, $default_id, $active_id, $path);
            echo '<li class="' . \implode(' ', $class) . '">';
            echo '<div class="nav-item-inner d-flex align-items-center'.(in_array('nav-item-active', $class) ? ' active' : '').'">';
            $document->include('header.menu.link', ['item' => $item, 'options' => $options, 'mobilemenu' => false, 'slidemenu' => 1, 'active' => in_array('nav-item-active', $class)]);
            echo '</div>';
            if ($item->deeper) {
                echo '<ul class="nav-child navbar-subnav level-' . $item->level . '">';
            } elseif ($item->shallower) {
                echo '</li>';
                echo str_repeat('</ul></li>', $item->level_diff);
            } else {
                echo '</li>';
            }
        }
        echo '</ul>';
    }
}
