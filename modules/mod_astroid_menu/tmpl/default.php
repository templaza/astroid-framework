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
$navClass = ['astroid-nav', 'd-none', 'd-'.$menu_breakpoint.'-flex', 'align-items-center'];
$navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'd-none', 'd-'.$menu_breakpoint.'-block'];

$id = '';

if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . $tagId . '"';
}
?>
<?php if ($menu_mode == 'site') : ?>
<div<?php echo $id; ?> class="mod-astroid-menu <?php echo $class_sfx; ?>">
    <?php
    // header nav starts
    Astroid\Component\Menu::getMenu($menu, $navClass, (bool)$logo_between, 'left', 'module', $navWrapperClass, $params, $startLevel, $endLevel, $base);
    // header nav ends
    ?>
</div>
<?php
elseif ($menu_mode == 'sidebar') :
    Astroid\Component\Menu::getSidebarMenu($menu);
elseif ($menu_mode == 'mobile') :
    $mobilemenu_visibility = $params->get('mobilemenu_visibility', 'lg');
    $document = Astroid\Framework::getDocument();
    $document->getWA()->useScript('bootstrap.offcanvas');
    $document->getWA()->useScript('astroid.mobilemenu');
    
    // Map document text direction to a physical side for offcanvas defaults.
    $direction = strtolower((string) $document->getDocument()->getDirection());
    $dir = $direction === 'rtl' ? 'right' : 'left';

    $offcanvas_direction = match($dir) {
        'left' => 'offcanvas-start',
        'right' => 'offcanvas-end',
        default => 'offcanvas-start',
    };
    ?>
    <div class="d-flex d-<?php echo $mobilemenu_visibility; ?>-none justify-content-start">
        <div class="header-mobilemenu-trigger d-<?php echo $mobilemenu_visibility; ?>-none burger-menu-button align-self-center">
            <button data-bs-toggle="offcanvas" data-bs-target="#astroid-mobilemenu-<?php echo $module->id; ?>" aria-controls="astroid-mobilemenu" type="button" class="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
        </div>
    </div>

    <div class="astroid-mobilemenu offcanvas <?php echo $offcanvas_direction; ?>" tabindex="-1" aria-labelledby="astroid-mobilemenu-label-<?php echo $module->id; ?>" id="astroid-mobilemenu-<?php echo $module->id; ?>" data-lenis-prevent>
        <div class="offcanvas-header burger-menu-button">
            <h5 class="offcanvas-title" id="astroid-mobilemenu-label-<?php echo $module->id; ?>"></h5>
            <button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="button close-offcanvas offcanvas-close-btn"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
        </div>
        <?php Astroid\Component\Menu::getMobileMenu($menu); ?>
    </div>
<?php endif; ?>
