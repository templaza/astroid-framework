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
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die;
extract($displayData);
$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

$header = $params->get('header', TRUE);
$enable_offcanvas = $params->get('enable_offcanvas', FALSE);
if (!$header || !$enable_offcanvas) {
   return;
}

$module_position = 'offcanvas';
$document->getWA()->useScript('bootstrap.offcanvas');
$hasMenu = $document->hasModule($module_position, 'mod_menu');
if ($hasMenu) {
    $document->getWA()->useScript('astroid.mobilemenu');
}

$togglevisibility = $params->get('offcanvas_togglevisibility', 'd-block');
$offcanvas_heading_text = $params->get('offcanvas_heading_text', '');
$panelwidth = $params->get('offcanvas_panelwidth', '320px');
$offcanvas_direction = match($params->get('offcanvas_direction', 'offcanvasDirLeft')) {
    'offcanvasDirLeft' => 'offcanvas-start',
    'offcanvasDirRight' => 'offcanvas-end',
    default => 'offcanvas-start',
};

echo '<div class="astroid-offcanvas offcanvas ' . $offcanvas_direction . '" tabindex="-1" id="astroid-offcanvas" aria-labelledby="astroid-offcanvas-label" data-lenis-prevent>';
echo '<div class="offcanvas-header burger-menu-button">';
echo '<h5 class="offcanvas-title" id="astroid-offcanvas-label">' . $offcanvas_heading_text . '</h5>';
echo '<button type="button" class="button close-offcanvas offcanvas-close-btn" data-bs-dismiss="offcanvas" aria-label="Close">';
echo '<span class="box">';
echo '<span class="inner"><span class="visually-hidden">Off-Canvas Toggle</span></span>';
echo '</span>';
echo '</button>';
echo '</div>';
echo '<div class="offcanvas-body pt-0">';

$content = $document->position($module_position, 'astroidxhtml');
if (empty($content)) {
    echo '<div class="alert alert-danger">' . Text::_('TPL_OFFCANVAS_EMPTY_ERROR') . '</div>';
} else {
    echo $content;
}

echo '</div>';
echo '</div>';

$style = '.astroid-offcanvas {--bs-offcanvas-width: ' . $panelwidth . ';} .astroid-offcanvas .dropdown-menus {width: ' . $panelwidth . ' !important;}';
$document->addStyledeclaration($style);
?>