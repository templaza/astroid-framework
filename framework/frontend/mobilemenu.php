<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

$header = $params->get('header', TRUE);
$header_mobile_menu = $params->get('header_mobile_menu', '');
$mobile_menu_heading_text = $params->get('mobile_menu_heading_text', '');

echo '<div id="astroid-mobilemenu-wrap">';
if ($header && !empty($header_mobile_menu)) :
    $dir = 'left';
    $header = $params->get('header', TRUE);
    $header_mode = $params->get('header_mode', 'horizontal');
    $mode = $params->get('header_sidebar_menu_mode', 'left');
    if ($header_mode == 'sidebar') {
        if ($mode == 'topbar') {
            $dir = $params->get('sidebar_position', 'left');
        } else {
            $dir = $mode;
        }
    }
    $offcanvas_direction = match($dir) {
        'left' => 'offcanvas-start',
        'right' => 'offcanvas-end',
        default => 'offcanvas-start',
    };
    $document->getWA()->useScript('bootstrap.offcanvas');
    $document->getWA()->useScript('astroid.mobilemenu');

    echo '<div class="astroid-mobilemenu offcanvas ' . $offcanvas_direction . '" tabindex="-1" aria-labelledby="astroid-mobilemenu-label" id="astroid-mobilemenu">';
    echo '<div class="offcanvas-header burger-menu-button">';
    echo '<h5 class="offcanvas-title" id="astroid-mobilemenu-label">' . $mobile_menu_heading_text . '</h5>';
    echo '<button type="button" data-bs-dismiss="offcanvas" aria-label="Close" class="button close-offcanvas offcanvas-close-btn">';
    echo '<span class="box">';
    echo '<span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span>';
    echo '</span>';
    echo '</button>';
    echo '</div>';

    echo Astroid\Component\Menu::getMobileMenu($header_mobile_menu);

    echo '</div>';

    $style = '.mobilemenu-slide.astroid-mobilemenu{visibility:visible;-webkit-transform:translate3d(' . ($dir == 'left' ? '-' : '') . '100%, 0, 0);transform:translate3d(' . ($dir == 'left' ? '-' : '') . '100%, 0, 0);}.mobilemenu-slide.astroid-mobilemenu-open .mobilemenu-slide.astroid-mobilemenu {visibility:visible;-webkit-transform:translate3d(0, 0, 0);transform:translate3d(0, 0, 0);}.mobilemenu-slide.astroid-mobilemenu::after{display:none;}';
    $document->addStyledeclaration($style);
endif;
echo '</div>';
?>