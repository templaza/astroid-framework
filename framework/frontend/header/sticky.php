<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;

extract($displayData);
$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

$header_menu = $params->get('header_menu', 'mainmenu');
$enable_offcanvas = $params->get('enable_offcanvas', FALSE);
$offcanvas_animation = $params->get('offcanvas_animation', 'st-effect-1');
$offcanvas_direction = $params->get('offcanvas_direction', 'offcanvasDirLeft');
$offcanvas_togglevisibility = $params->get('offcanvas_togglevisibility', 'd-block');
$class = ['astroid-header', 'astroid-header-sticky'];
$stickyheader = $params->get('stickyheader', 'static');
$header_mobile_menu = $params->get('header_mobile_menu', '');
$class[] = 'header-' . $stickyheader . '-desktop';
$stickyheadermobile = $params->get('stickyheadermobile', 'static');
$class[] = 'header-' . $stickyheadermobile . '-mobile';
$stickyheadertablet = $params->get('stickyheadertablet', 'static');
$class[] = 'header-' . $stickyheadertablet . '-tablet';
$navClass = ['nav', 'astroid-nav', 'd-none', 'd-lg-flex'];
$navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'px-2', 'd-none', 'd-lg-block'];
$mode = $params->get('header_horizontal_menu_mode', 'left');
$stickey_mode = $params->get('stickey_horizontal_menu_mode', 'left');
$block_1_type = $params->get('stickey_block_1_type', 'left');
$block_1_position = $params->get('stickey_block_1_position', '');
$block_1_custom = $params->get('stickey_block_1_custom', '');
switch ($stickey_mode) {
   case 'left':
      $navWrapperClass[] = 'mr-auto';
      break;
   case 'right':
      $navWrapperClass[] = 'ml-auto';
      break;
   case 'center':
      $navWrapperClass[] = 'mx-auto';
      break;
}
?>
<!-- header starts -->
<header id="astroid-sticky-header" data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="<?php echo implode(' ', $class); ?> d-none">
   <div class="container d-flex flex-row justify-content-between">
      <?php if (!empty($header_mobile_menu)) { ?>
         <div class="d-flex d-lg-none justify-content-start">
            <div class="header-mobilemenu-trigger d-lg-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
               <button class="button" type="button" aria-label="Mobile Menu Toggle"><span class="box"><span class="inner"></span></span></button>
            </div>
         </div>
      <?php } ?>
      <div class="header-left-section d-flex justify-content-start<?php echo $stickey_mode == 'left' ? ' flex-lg-grow-1' : ''; ?>">
         <?php $document->include('logo'); ?>
         <?php
         if ($stickey_mode == 'left') {
            // header nav starts
            Astroid\Component\Menu::getMenu($header_menu, $navClass, null, 'left', 'sticky', $navWrapperClass);
            // header nav ends
         }
         ?>
      </div>
      <?php if (!$enable_offcanvas && ($stickey_mode == 'left' || $stickey_mode == 'center')) : ?>
         <div></div>
      <?php endif; ?>
      <?php
      if ($stickey_mode == 'center') {
         echo '<div class="header-center-section d-none d-lg-flex justify-content-center' . ($stickey_mode == 'center' ? ' flex-lg-grow-1' : '') . '">';
         // header nav starts
         Astroid\Component\Menu::getMenu($header_menu, $navClass, null, 'left', 'sticky', $navWrapperClass);
         // header nav ends
         echo '</div>';
      }
      ?>
      <?php if ($block_1_type != 'blank' || $stickey_mode == 'right' || $enable_offcanvas) : ?>
         <div class="header-right-section d-flex justify-content-end<?php echo $stickey_mode == 'right' ? ' flex-lg-grow-1' : ''; ?>">
            <?php
            if ($stickey_mode == 'right') {
               // header nav starts
               Astroid\Component\Menu::getMenu($header_menu, $navClass, null, 'left', 'sticky', $navWrapperClass);
               // header nav ends
            }
            ?>
            <?php if ($enable_offcanvas) { ?>
               <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
            <?php } ?>
            <?php if ($block_1_type != 'blank') : ?>
               <div class="header-right-block d-none d-lg-block align-self-center px-2">
                  <?php
                  if ($block_1_type == 'position') {
                     echo '<div class="header-block-item">';
                     echo $document->position($block_1_position, 'xhtml');
                     echo '</div>';
                  }
                  if ($block_1_type == 'custom') {
                     echo '<div class="header-block-item">';
                     echo $block_1_custom;
                     echo '</div>';
                  }
                  ?>
               </div>
            <?php endif; ?>
         </div>
      <?php endif; ?>
   </div>
</header>
<!-- header ends -->