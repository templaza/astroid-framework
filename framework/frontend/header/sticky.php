<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;

extract($displayData);
$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();
$params = $template->getParams();
$color_mode = $template->getColorMode();

$header_menu = $params->get('header_menu', 'mainmenu');
$header_breakpoint = $params->get('header_breakpoint', 'lg');
$enable_offcanvas = $params->get('enable_offcanvas', FALSE);
$offcanvas_animation = $params->get('offcanvas_animation', 'st-effect-1');
$offcanvas_direction = $params->get('offcanvas_direction', 'offcanvasDirLeft');
$offcanvas_position = $params->get('offcanvas_position', 'offcanvasRight');
$offcanvas_togglevisibility = $params->get('offcanvas_togglevisibility', 'd-block');
$class = ['astroid-header', 'astroid-header-sticky'];
$stickyheader = $params->get('stickyheader', 'static');
$header_mobile_menu = $params->get('header_mobile_menu', '');
$class[] = 'header-' . $stickyheader . '-desktop';
$stickyheadermobile = $params->get('stickyheadermobile', 'static');
$class[] = 'header-' . $stickyheadermobile . '-mobile';
$stickyheadertablet = $params->get('stickyheadertablet', 'static');
$class[] = 'header-' . $stickyheadertablet . '-tablet';
$navClass = ['nav', 'astroid-nav', 'd-none', 'd-'.$header_breakpoint.'-flex'];
$navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'px-3', 'd-none', 'd-'.$header_breakpoint.'-block'];
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
         <div class="d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
            <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
               <button class="button" type="button" aria-label="Mobile Menu Toggle"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
            </div>
         </div>
      <?php } ?>
      <div class="header-left-section d-flex justify-content-start<?php echo $stickey_mode == 'left' ? ' flex-'.$header_breakpoint.'-grow-1' : ''; ?>">
          <?php if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
              <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex me-4 offcanvas-button '.$offcanvas_position.'">'; ?>
              <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
              <?php echo '</div>'; ?>
          <?php } ?>
         <?php $document->include('logo', ['position' => 'sticky']); ?>
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
         echo '<div class="header-center-section d-none d-'.$header_breakpoint.'-flex justify-content-center' . ($stickey_mode == 'center' ? ' flex-'.$header_breakpoint.'-grow-1' : '') . '">';
         // header nav starts
         Astroid\Component\Menu::getMenu($header_menu, $navClass, null, 'left', 'sticky', $navWrapperClass);
         // header nav ends
         echo '</div>';
      }
      ?>
      <?php if ($block_1_type != 'blank' || $stickey_mode == 'right' || $enable_offcanvas || $color_mode) : ?>
         <div class="header-right-section d-flex justify-content-end<?php echo $stickey_mode == 'right' ? ' flex-'.$header_breakpoint.'-grow-1' : ''; ?>">
            <?php
            if ($stickey_mode == 'right') {
               // header nav starts
               Astroid\Component\Menu::getMenu($header_menu, $navClass, null, 'left', 'sticky', $navWrapperClass);
               // header nav ends
            }
            ?>
            <?php if ($block_1_type != 'blank') : ?>
               <div class="header-right-block d-none d-<?php echo $header_breakpoint; ?>-block align-self-center">
                  <?php
                  if ($block_1_type == 'position') {
                     echo '<div class="header-block-item d-flex">';
                     echo $document->position($block_1_position, 'xhtml');
                     echo '</div>';
                  }
                  if ($block_1_type == 'custom') {
                     echo '<div class="header-block-item d-flex">';
                     echo $block_1_custom;
                     echo '</div>';
                  }
                  ?>
               </div>
            <?php endif; ?>
             <?php
             //Color Mode
             if ($color_mode) {
                 echo '<div class="d-flex justify-content-end align-items-center ms-4 astroid-color-mode">';
                 $document->include('colormode');
                 echo '</div>';
             }
             ?>
             <?php if ($enable_offcanvas) { ?>
                 <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' ms-4 offcanvas-button offcanvasRight">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
             <?php } ?>
         </div>
      <?php endif; ?>
   </div>
</header>
<!-- header ends -->