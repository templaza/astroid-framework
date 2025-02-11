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

$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();
$params = $template->getParams();
$color_mode = $template->getColorMode();

$header = $params->get('header', TRUE);
$header_mode = $params->get('header_mode', 'horizontal');

if (!($header && !empty($header_mode) && $header_mode == 'sidebar')) {
    return;
}

$mode = $params->get('header_sidebar_menu_mode', 'left');
if ($mode == 'topbar') {
    $sidebar_position = $params->get('sidebar_position', 'left');
} else {
    $sidebar_position = $mode;
}

$block_2_type = $params->get('header_block_2_type', 'blank');
$block_2_position = $params->get('header_block_2_position', '');
$block_2_custom = $params->get('header_block_2_custom', '');

$block_1_type = $params->get('header_block_1_type', 'blank');
$block_1_position = $params->get('header_block_1_position', '');
$block_1_custom = $params->get('header_block_1_custom', '');

$block_3_type = $params->get('header_block_3_type', 'blank');
$block_3_position = $params->get('header_block_3_position', '');
$block_3_custom = $params->get('header_block_3_custom', '');

$header_menu = $params->get('header_menu', 'mainmenu');
$header_menu_method = $params->get('header_menu_method', 'default');
$header_menu_module_position = $params->get('header_menu_module_position', 'astroid-header-menu');
$enable_offcanvas = $params->get('enable_offcanvas', FALSE);
$header_mobile_menu = $params->get('header_mobile_menu', '');
$offcanvas_animation = $params->get('offcanvas_animation', 'st-effect-1');
$offcanvas_togglevisibility = $params->get('offcanvas_togglevisibility', 'd-block');
$class = ['astroid-header', 'astroid-sidebar-header', 'col-12', 'col-xl-auto', 'astroid-sidebar-' . $mode, 'sidebar-dir-' . $sidebar_position, 'has-sidebar'];
if ($sidebar_position == 'right') {
    $class[] = 'order-xl-1';
}
$navClass = ['nav', 'astroid-nav', 'd-none', 'd-lg-flex'];
$navWrapperClass = ['align-self-center', 'px-2', 'd-none', 'd-lg-block'];
$position_count = 0;
?>
<!-- header starts -->
<?php if ($mode == 'topbar') : ?>
    <div class="astroid-header astroid-sidebar-header astroid-sidebar-header-topbar">
        <div class="astroid-sidebar-header-inner row">
            <div class="astroid-sidebar-logo col-12 col-lg d-flex align-items-center">
                <?php if (!empty($header_mobile_menu)) { ?>
                    <div class="justify-content-start astroid-sidebar-mobile-menu">
                        <div class="header-mobilemenu-trigger burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                            <button aria-label="Mobile Menu Toggle" class="button" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                        </div>
                    </div>
                <?php } ?>
                <div class="flex-grow-1">
                    <?php $document->include('logo'); ?>
                </div>
            </div>
            <?php
            $position_count ++;
            if (${'block_'.$position_count.'_type'} != 'blank') : ?>
                <div class="astroid-sidebar-block d-none d-lg-block col-lg-auto astroid-sidebar-block-<?php echo $position_count; ?>">
                    <?php
                    if (${'block_'.$position_count.'_type'} == 'position') {
                        echo '<div class="header-block-item d-flex align-item-center as-gutter-lg">';
                        echo $document->position(${'block_'.$position_count.'_position'}, 'astroidxhtml');
                        echo '</div>';
                    }
                    if (${'block_'.$position_count.'_type'} == 'custom') {
                        echo '<div class="header-block-item d-flex align-item-center as-gutter-lg">';
                        echo ${'block_'.$position_count.'_custom'};
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<div class="<?php echo implode(' ', $class); ?>">
    <div class="astroid-sidebar-content sticky-top">
        <div class="astroid-sidebar-collapsable">
            <i class="fa"></i>
        </div>
        <?php if ($mode != 'topbar') : ?>
        <div class="astroid-sidebar-logo mb-xl-4">
            <?php if (!empty($header_mobile_menu)) { ?>
                <div class="justify-content-start astroid-sidebar-mobile-menu">
                    <div class="header-mobilemenu-trigger burger-menu-button align-self-center" data-offcanvas="#astroid-mobilemenu" data-effect="mobilemenu-slide">
                        <button aria-label="Mobile Menu Toggle" class="button" type="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button>
                    </div>
                </div>
            <?php } ?>
            <div class="flex-grow-1">
                <?php $document->include('logo'); ?>
            </div>
        </div>
        <?php endif; ?>
        <?php
        $position_count ++;
        if (${'block_'.$position_count.'_type'} != 'blank') : ?>
            <div class="astroid-sidebar-block astroid-sidebar-block-<?php echo $position_count; ?>">
                <?php
                if (${'block_'.$position_count.'_type'} == 'position') {
                    echo '<div class="header-block-item">';
                    echo $document->position(${'block_'.$position_count.'_position'}, 'astroidxhtml');
                    echo '</div>';
                }
                if (${'block_'.$position_count.'_type'} == 'custom') {
                    echo '<div class="header-block-item">';
                    echo ${'block_'.$position_count.'_custom'};
                    echo '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
        <div class="astroid-sidebar-menu">
            <?php
            if ($header_menu_method == 'module_position') {
                echo $document->position($header_menu_module_position);
            } else {
                Astroid\Component\Menu::getSidebarMenu($header_menu);
            }
            ?>
        </div>
        <?php
        $position_count ++;
        if (${'block_'.$position_count.'_type'} != 'blank') : ?>
            <div class="astroid-sidebar-block astroid-sidebar-block-<?php echo $position_count; ?>">
                <?php
                if (${'block_'.$position_count.'_type'} == 'position') {
                    echo '<div class="header-block-item">';
                    echo $document->position(${'block_'.$position_count.'_position'}, 'astroidxhtml');
                    echo '</div>';
                }
                if (${'block_'.$position_count.'_type'} == 'custom') {
                    echo '<div class="header-block-item">';
                    echo ${'block_'.$position_count.'_custom'};
                    echo '</div>';
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- header ends -->