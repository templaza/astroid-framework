<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
$menu_mode          =   $params->get('menu_mode', 'site');
$menu               =   $params->get('menutype', '');
$base               =   $params->get('base', '');
$startLevel         =   $params->get('startLevel');
$endLevel           =   $params->get('endLevel');
$showAllChildren    =   $params->get('showAllChildren', 1);
$logo_between       =   $params->get('logo_between', 0);
$menu_breakpoint    =   $params->get('menu_breakpoint');
$navClass = ['nav', 'astroid-nav', 'd-none', 'd-'.$menu_breakpoint.'-flex'];
$navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'px-3', 'd-none', 'd-'.$menu_breakpoint.'-block'];

$id = '';

if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . $tagId . '"';
}
?>
<?php if ($menu_mode == 'site') : ?>
<div<?php echo $id; ?> data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?> data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="mod-astroid-menu <?php echo $class_sfx; ?>">
    <?php
    // header nav starts
    Astroid\Component\Menu::getMenu($menu, $navClass, (bool)$logo_between, 'left', 'module', $navWrapperClass, $startLevel, $endLevel, $base);
    // header nav ends
    ?>
</div>
<?php
elseif ($menu_mode == 'sidebar') :
    Astroid\Component\Menu::getSidebarMenu($menu);
elseif ($menu_mode == 'mobile') :
    $dir    =   'left';
    $document = Astroid\Framework::getDocument();
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->registerAndUseScript('astroid.offcanvas', 'astroid/offcanvas.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
    $wa->registerAndUseScript('astroid.mobilemenu', 'astroid/mobilemenu.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
    $style = '.mobilemenu-slide.astroid-mobilemenu{visibility:visible;-webkit-transform:translate3d(' . ($dir == 'left' ? '-' : '') . '100%, 0, 0);transform:translate3d(' . ($dir == 'left' ? '-' : '') . '100%, 0, 0);}.mobilemenu-slide.astroid-mobilemenu-open .mobilemenu-slide.astroid-mobilemenu {visibility:visible;-webkit-transform:translate3d(0, 0, 0);transform:translate3d(0, 0, 0);}.mobilemenu-slide.astroid-mobilemenu::after{display:none;}';
    $document->addStyledeclaration($style);
    ?>
    <div class="astroid-mobilemenu d-none d-init dir-<?php echo $dir; ?>" data-class-prefix="astroid-mobilemenu" id="astroid-mobilemenu">
        <div class="burger-menu-button active">
            <button aria-label="Mobile Menu Toggle" type="button" class="button close-offcanvas offcanvas-close-btn"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
        </div>
        <?php Astroid\Component\Menu::getMobileMenu($menu); ?>
    </div>
<?php endif; ?>