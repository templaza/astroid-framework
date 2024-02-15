<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$document = Astroid\Framework::getDocument();
$template = Astroid\Framework::getTemplate();
$params = $template->getParams();
$color_mode = $template->getColorMode();

$mode = $params->get('header_stacked_menu_mode', 'center');
$block_1_type = $params->get('header_block_1_type', 'blank');
$block_1_position = $params->get('header_block_1_position', '');
$block_1_custom = $params->get('header_block_1_custom', '');
$block_2_type = $params->get('header_block_2_type', 'blank');
$block_2_position = $params->get('header_block_2_position', '');
$block_2_custom = $params->get('header_block_2_custom', '');
$block_3_type = $params->get('header_block_3_type', 'blank');
$block_3_position = $params->get('header_block_3_position', '');
$block_3_custom = $params->get('header_block_3_custom', '');
$header_mobile_menu = $params->get('header_mobile_menu', '');
$header_menu = $params->get('header_menu', '');
$header_breakpoint = $params->get('header_breakpoint', 'lg');
$odd_menu_items = $params->get('odd_menu_items', 'left');
$divided_logo_width = $params->get('divided_logo_width', 200);
$class = ['astroid-header', 'astroid-stacked-header', 'astroid-stacked-' . $mode . '-header'];
$enable_offcanvas = $params->get('enable_offcanvas', FALSE);
$offcanvas_animation = $params->get('offcanvas_animation', 'st-effect-1');
$offcanvas_direction = $params->get('offcanvas_direction', 'offcanvasDirLeft');
$offcanvas_position = $params->get('offcanvas_position', 'offcanvasRight');
$offcanvas_togglevisibility = $params->get('offcanvas_togglevisibility', 'd-block');

$navClass = ['nav', 'astroid-nav', 'justify-content-center', 'd-flex', 'align-items-center'];
$navClassLeft = ['nav', 'astroid-nav', 'justify-content-left', 'd-flex', 'align-items-left'];
$navClassDivided = ['nav', 'astroid-nav'];
if ($mode == 'divided-logo-left') {
    $navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'd-none', 'd-'.$header_breakpoint.'-block', 'w-100'];
} else {
    $navWrapperClass = ['astroid-nav-wraper', 'align-self-center', 'px-2', 'd-none', 'd-'.$header_breakpoint.'-block', 'w-100'];
}
if ($mode == 'divided-logo-left') {
    switch ($header_breakpoint) {
        case 'sm': {
            $document->addStyleDeclaration('@media (min-width: 576px) {.col-divided-logo{width: '.$divided_logo_width.'px;}}');
            break;
        }
        case 'md': {
            $document->addStyleDeclaration('@media (min-width: 768px) {.col-divided-logo{width: '.$divided_logo_width.'px;}}');
            break;
        }
        case 'lg': {
            $document->addStyleDeclaration('@media (min-width: 992px) {.col-divided-logo{width: '.$divided_logo_width.'px;}}');
            break;
        }
        case 'xl': {
            $document->addStyleDeclaration('@media (min-width: 1200px) {.col-divided-logo{width: '.$divided_logo_width.'px;}}');
            break;
        }
        case 'xxl': {
            $document->addStyleDeclaration('@media (min-width: 1400px) {.col-divided-logo{width: '.$divided_logo_width.'px;}}');
            break;
        }
    }
}

?>
<header id="astroid-header" class="<?php echo implode(' ', $class); ?>">
   <div class="d-flex">
      <div class="header-stacked-section d-flex justify-content-between flex-column w-100">
         <?php
         //Center Balance
         if ($mode == 'center-balance') {
             echo '<div class="astroid-header-center-balance w-100 d-flex justify-content-center">';
             ?>
             <?php if (!empty($header_mobile_menu)) { ?>
                 <div class="w-100 d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
                     <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                         <button class="button" aria-label="Mobile Menu Toggle" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                     </div>
                 </div>
             <?php } ?>
             <?php if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
                 <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex justify-content-start me-4 offcanvas-button '.$offcanvas_position.'">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
             <?php } ?>
             <?php
             // header block 1 starts
             if ($block_1_type == 'position') {
                 echo '<div class="w-100 d-none d-'.$header_breakpoint.'-flex justify-content-start align-items-center">';
                 echo '<div class="w-100 header-block-item d-flex justify-content-start align-items-center">';
                 echo $document->position($block_1_position, 'xhtml');
                 echo '</div>';
                 echo '</div>';
             }
             if ($block_1_type == 'custom') {
                 echo '<div class="w-100 d-none d-'.$header_breakpoint.'-flex justify-content-start align-items-center">';
                 echo '<div class="w-100 header-block-item d-flex justify-content-start align-items-center">';
                 echo $block_1_custom;
                 echo '</div>';
                 echo '</div>';
             }
             // header block 1 ends

             $logo = $document->include('logo', [], true);
             if (!empty($logo)) {
                 echo '<div class="d-flex w-100 justify-content-center">' . $logo . '</div>';
             }

             if ($enable_offcanvas || $block_2_type == 'position' || $block_2_type == 'custom' || $color_mode) {
                 echo '<div class="w-100 d-flex justify-content-end align-items-center">';
                 // header block 2 starts
                 if ($block_2_type == 'position') {
                     echo '<div class="header-block-item d-none d-'.$header_breakpoint.'-flex justify-content-end align-items-center">';
                     echo $document->position($block_2_position, 'xhtml');
                     echo '</div>';
                 }
                 if ($block_2_type == 'custom') {
                     echo '<div class="header-block-item d-none d-'.$header_breakpoint.'-flex justify-content-end align-items-center">';
                     echo $block_2_custom;
                     echo '</div>';
                 }
                 // header block 2 ends

                 if ($enable_offcanvas) {
                     ?>
                     <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' justify-content-end ms-4 offcanvas-button offcanvasRight">'; ?>
                     <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                     <?php echo '</div>'; ?>
                     <?php
                 }
                 echo '</div>';
             }

             echo '</div>';
             // header nav starts -->
             ?>
             <div data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="astroid-header-center-balance-menu w-100 d-none d-<?php echo $header_breakpoint; ?>-flex justify-content-center pt-3">
                 <?php
                 Astroid\Component\Menu::getMenu($header_menu, array_merge($navClass), null, 'left', 'stacked', $navWrapperClass);
                 ?>
             </div>
             <?php
             // header nav ends
         }

         // Center Mode
         if ($mode == 'center') {
            echo '<div class="w-100 d-flex justify-content-center">';
         ?>
            <?php if (!empty($header_mobile_menu)) { ?>
               <div class="d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
                  <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                     <button class="button" aria-label="Mobile Menu Toggle" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                  </div>
               </div>
            <?php } ?>
             <?php if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
                 <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex justify-content-start me-4 offcanvas-button '.$offcanvas_position.'">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
             <?php } ?>
            <?php
            $logo = $document->include('logo', [], true);
            if (!empty($logo)) {
               echo '<div class="d-flex w-100 justify-content-center">' . $logo . '</div>';
            }
            if ($enable_offcanvas) {
            ?>
                <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' justify-content-end ms-4 offcanvas-button offcanvasRight">'; ?>
                <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                <?php echo '</div>'; ?>
            <?php
            }
            echo '</div>';
            // header nav starts -->
            ?>
            <div data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="astroid-stacked-<?php echo $mode; ?>-menu w-100 d-none d-<?php echo $header_breakpoint; ?>-flex justify-content-center pt-3">
               <?php
               Astroid\Component\Menu::getMenu($header_menu, array_merge($navClass), null, 'left', 'stacked', $navWrapperClass);
               ?>
            </div>
            <?php
            // header nav ends
            // header block starts
            if ($block_1_type == 'position') {
               echo '<div class="w-100 header-block-item d-none d-'.$header_breakpoint.'-flex justify-content-center py-3">';
               echo $document->position($block_1_position, 'xhtml');
               echo '</div>';
            }
            if ($block_1_type == 'custom') {
               echo '<div class="w-100 header-block-item d-none d-'.$header_breakpoint.'-flex justify-content-center py-3">';
               echo $block_1_custom;
               echo '</div>';
            }

            // header block ends
         }
         if ($mode == 'seperated') {
            // header nav starts   
            ?>
            <div data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="astroid-stacked-<?php echo $mode; ?>-menu header-stacked-inner w-100 d-flex justify-content-center">
               <?php if (!empty($header_mobile_menu)) { ?>
                  <div class="d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
                     <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                        <button class="button" aria-label="Mobile Menu Toggle" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                     </div>
                  </div>
               <?php
               }
               if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
                   <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex justify-content-start offcanvas-button '.$offcanvas_position.'">'; ?>
                   <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                   <?php echo '</div>'; ?>
               <?php }

               // header block starts
               if ($block_1_type == 'position') {
                   echo '<div class="header-block-item header-block-1 d-none d-'.$header_breakpoint.'-flex justify-content-start">';
                   echo $document->position($block_1_position, 'xhtml');
                   echo '</div>';
               }
               if ($block_1_type == 'custom') {
                   echo '<div class="header-block-item header-block-1 d-none d-'.$header_breakpoint.'-flex justify-content-start">';
                   echo $block_1_custom;
                   echo '</div>';
               }

               echo '<div class="d-flex w-100 align-items-center justify-content-center">';
               $logo = $document->include('logo', [], true);
               if (!empty($logo)) {
                  echo '<div class="d-'.$header_breakpoint.'-none">' . $logo . '</div>';
               }
               Astroid\Component\Menu::getMenu($header_menu, $navClass, true, $odd_menu_items, 'stacked', $navWrapperClass);
               echo '</div>';

               // header block starts
               if ($block_2_type == 'position') {
                   echo '<div class="header-block-item header-block-2 d-none d-'.$header_breakpoint.'-flex justify-content-end">';
                   echo $document->position($block_2_position, 'xhtml');
                   echo '</div>';
               }
               if ($block_2_type == 'custom') {
                   echo '<div class="header-block-item header-block-2 d-none d-'.$header_breakpoint.'-flex justify-content-end">';
                   echo $block_2_custom;
                   echo '</div>';
               }
               // header block ends

               if ($enable_offcanvas) {
               ?>
                   <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' justify-content-end ms-4 offcanvas-button offcanvasRight">'; ?>
                   <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                   <?php echo '</div>'; ?>
               <?php
               }
               ?>
            </div>
            <?php
            // header nav ends
         }
         if ($mode == 'divided') {
            echo '<div class="w-100 d-flex justify-content-center py-3">';
            ?>
            <?php if (!empty($header_mobile_menu)) { ?>
               <div class="d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
                  <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                     <button class="button" aria-label="Mobile Menu Toggle" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                  </div>
               </div>
            <?php
            }
             echo '<div class="d-flex flex-md-shrink-0 flex-grow-1 justify-content-center justify-content-'.$header_breakpoint.'-start">';
             if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
                 <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex justify-content-start me-4 offcanvas-button '.$offcanvas_position.'">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
             <?php }
            $document->include('logo');
            echo '</div>';

            // header block starts
            if ($block_1_type == 'position') {
               echo '<div class="d-none d-'.$header_breakpoint.'-flex w-100 flex-grow-1 justify-content-end py-2 align-items-center">';
               echo '<div class="d-flex w-100 justify-content-end header-block-item align-items-center">';
               echo $document->position($block_1_position, 'xhtml');
               echo '</div>';
               echo '</div>';
            }
            if ($block_1_type == 'custom') {
                echo '<div class="d-none d-'.$header_breakpoint.'-flex w-100 flex-grow-1 justify-content-end py-2 align-items-center">';
                echo '<div class="d-flex w-100 justify-content-end header-block-item align-items-center">';
                echo $block_1_custom;
                echo '</div>';
                echo '</div>';
            }
            // header block ends

            if ($enable_offcanvas) {
            ?>
                <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' justify-content-end ms-lg-4 offcanvas-button offcanvasRight">'; ?>
                <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                <?php echo '</div>'; ?>
            <?php
            }
            echo '</div>';
            // header nav starts -->
            ?>
            <div data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="astroid-stacked-<?php echo $mode; ?>-menu as-megamenu-section d-none d-<?php echo $header_breakpoint; ?>-block py-2">
                <div class="row">
                    <?php
                    Astroid\Component\Menu::getMenu($header_menu, $navClassLeft, null, 'left', 'stacked', ['astroid-nav-wraper', 'col']);
                    // header block starts
                    if ($block_2_type == 'position') {
                        echo '<div class="d-flex col-auto">';
                        echo '<div class="d-flex align-items-center header-block-item">';
                        echo $document->position($block_2_position, 'xhtml');
                        echo '</div>';
                        echo '</div>';
                    }
                    if ($block_2_type == 'custom') {
                        echo '<div class="d-flex col-auto">';
                        echo '<div class="d-flex align-items-center header-block-item">';
                        echo $block_2_custom;
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
         <?php
            // header nav ends
         }
         if ($mode == 'divided-logo-left') {
             echo '<div class="row g-0 divided-logo-left">';
             echo '<div class="col-12 col-divided-logo">';
             echo '<div class="w-100 h-100 d-flex justify-content-center">';
             ?>
             <?php if (!empty($header_mobile_menu)) { ?>
                 <div class="d-flex d-<?php echo $header_breakpoint; ?>-none justify-content-start">
                     <div class="header-mobilemenu-trigger d-<?php echo $header_breakpoint; ?>-none burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                         <button class="button" aria-label="Mobile Menu Toggle" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                     </div>
                 </div>
                 <?php
             }
             echo '<div class="d-flex w-100 w-auto@'.$header_breakpoint.' justify-content-center justify-content-'.$header_breakpoint.'-start">';
             $document->include('logo');
             echo '</div>';

             if ($enable_offcanvas) {
                 ?>
                 <div class="d-<?php echo $header_breakpoint; ?>-none d-flex justify-content-end offcanvas-button offcanvasRight">
                     <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 </div>
                 <?php
             }
             echo '</div>';
             echo '</div>';

             echo '<div class="col d-none d-'.$header_breakpoint.'-flex flex-column justify-content-center">';
             echo '<div class="divided-menu-block">';
             if ($block_1_type != '' || $block_2_type != '') {
                 echo '<div class="header-block-items d-flex">';
                 echo '<div class="d-flex justify-content-between w-100">';
                 // header block starts
                 if ($block_1_type == 'position') {
                     echo '<div class="d-flex header-block-item justify-content-start align-items-center">';
                     echo $document->position($block_1_position, 'xhtml');
                     echo '</div>';
                 }
                 if ($block_1_type == 'custom') {
                     echo '<div class="d-flex header-block-item justify-content-start align-items-center">';
                     echo $block_1_custom;
                     echo '</div>';
                 }
                 if ($block_2_type == 'position') {
                     echo '<div class="d-flex header-block-item justify-content-end align-items-center">';
                     echo $document->position($block_2_position, 'xhtml');
                     echo '</div>';
                 }
                 if ($block_2_type == 'custom') {
                     echo '<div class="d-flex header-block-item justify-content-end align-items-center">';
                     echo $block_2_custom;
                     echo '</div>';
                 }
                 // header block ends
                 echo '</div>';
                 echo '</div>';
             }
             // header nav starts -->
             echo '<div class="megamenu-block d-flex w-100 h-100">';
             if ($enable_offcanvas && $offcanvas_position === 'offcanvasLeft') { ?>
                 <?php echo '<div class="d-none d-'.$header_breakpoint.'-flex justify-content-start me-4 offcanvas-button '.$offcanvas_position.'">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
             <?php }
             ?>
             <div data-megamenu data-megamenu-class=".has-megamenu" data-megamenu-content-class=".megamenu-container" data-dropdown-arrow="<?php echo $params->get('dropdown_arrow', 0) ? 'true' : 'false'; ?>" data-header-offset="true" data-transition-speed="<?php echo $params->get('dropdown_animation_speed', 300); ?>" data-megamenu-animation="<?php echo $params->get('dropdown_animation_type', 'fade'); ?>" data-easing="<?php echo $params->get('dropdown_animation_ease', 'linear'); ?>" data-astroid-trigger="<?php echo $params->get('dropdown_trigger', 'hover'); ?>" data-megamenu-submenu-class=".nav-submenu" class="astroid-stacked-<?php echo $mode; ?>-menu d-flex justify-content-start flex-<?php echo $header_breakpoint; ?>-grow-1">
                 <?php
                 Astroid\Component\Menu::getMenu($header_menu, $navClassLeft, null, 'left', 'stacked', $navWrapperClass);
                 ?>
             </div>
             <?php
             if ($block_3_type != '') {
                 if ($block_3_type == 'position') {
                     echo '<div class="d-flex header-block-item justify-content-end align-items-center">';
                     echo $document->position($block_3_position, 'xhtml');
                     echo '</div>';
                 }
                 if ($block_3_type == 'custom') {
                     echo '<div class="d-flex header-block-item justify-content-end align-items-center">';
                     echo $block_3_custom;
                     echo '</div>';
                 }
             }
             // header nav ends
             if ($enable_offcanvas) {
                 ?>
                 <?php echo '<div class="'.($offcanvas_position === 'offcanvasRight' ? 'd-flex' : 'd-'.$header_breakpoint.'-none d-flex').' justify-content-end ms-4 offcanvas-button offcanvasRight">'; ?>
                 <?php $document->include('offcanvas.trigger', ['offcanvas' => '#astroid-offcanvas', 'visibility' => $offcanvas_togglevisibility, 'effect' => $offcanvas_animation, 'direction' => $offcanvas_direction]); ?>
                 <?php echo '</div>'; ?>
                 <?php
             }
             echo '</div>';

             echo '</div>';

             echo '</div>';

             echo '</div>';
         }
         ?>
      </div>
   </div>
</header>