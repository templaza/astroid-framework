<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
$menu               =   $params->get('menutype');
$base               =   $params->get('base');
$startLevel         =   $params->get('startLevel');
$endLevel           =   $params->get('endLevel');
$showAllChildren    =   $params->get('showAllChildren');
$menu_breakpoint    =   $params->get('menu_breakpoint');
$navClass = ['nav', 'astroid-nav', 'd-none', 'd-'.$menu_breakpoint.'-flex'];
$navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'px-3', 'd-none', 'd-'.$menu_breakpoint.'-block'];

$id = '';

if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . $tagId . '"';
}
?>
<div<?php echo $id; ?> data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-header-offset="true" data-transition-speed="300" data-megamenu-animation="fade" data-easing="linear" data-astroid-trigger="hover" data-megamenu-submenu-class=".nav-submenu" class="mod-astroid-menu <?php echo $class_sfx; ?>">
    <?php
    // header nav starts
    Astroid\Component\Menu::getMenu($menu, $navClass, null, 'left', 'module', $navWrapperClass, $base, $startLevel, $endLevel, $showAllChildren);
    // header nav ends
    ?>
</div>