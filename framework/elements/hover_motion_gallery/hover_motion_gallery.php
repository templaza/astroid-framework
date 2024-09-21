<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Framework;
use Astroid\Helper;
use Astroid\Helper\Style;
use Joomla\CMS\Factory;

extract($displayData);
$images       = $params->get('images', '');
if (empty($images)) {
    return false;
}
$images       = json_decode($images);
if (!count($images)) {
    return false;
}
$document           =   Framework::getDocument();

$rows       =   $params->get('rows', 5);
$columns    =   ceil(count($images) / $rows);

$images     =   array_merge($images, array_slice($images, 0, $columns));
$rows++;

$view_detail        =   $params->get('view_detail', 1);
$view_detail        =   $view_detail ? ' as-item-click' : '';
$rounded_size       =   $params->get('rounded_size', '3');
$border_radius      =   $params->get('border_radius', '');
$height             =   $params->get('height', '80vh');
$angle              =   $params->get('angle', '-5');
$gap                =   $params->get('gap', '1');
if ($border_radius == 'rounded') {
    $border_radius  = ' ' . $border_radius . '-' . $rounded_size;
} else {
    $border_radius  = $border_radius !== '' ? ' ' . $border_radius : '';
}

echo '<div class="as-hover-motion-gallery loading">';
echo '<div class="as-hover-motion-gallery-inner">';
foreach ($images as $index => $image) {
    $image_params   =   Helper::loadParams($image->params);
    if (($index + 1) % $columns === 1) {
        echo '<div class="as-hover-motion-row">';
    }
    if (!empty($image_params->get('image'))) {
        echo '<div class="as-hover-motion-row-item">';
        echo '<div class="as-hover-motion-row-item-inner position-relative overflow-hidden' . $border_radius . $view_detail . '">';
        echo '<img src="'. Astroid\Helper\Media::getMediaPath($image_params->get('image')).'" alt="'.$image_params->get('title').'" class="as-hover-motion-row-item-image">';
        echo '</div>';
        echo '</div>';
    }
    if (($index + 1) % $columns === 0 || $index + 1 === count($images)) {
        echo '</div>';
    }
}
echo '</div>';
echo '</div>';

$document->loadGSAP('Flip');
$document->loadImagesLoaded();
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('astroid.hovermotion', 'media/astroid/assets/vendor/hover_motion/css/base.min.css');
$wa->registerAndUseScript('astroid.hovermotion', 'media/astroid/assets/vendor/hover_motion/js/index.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
$element->style->child('.as-hover-motion-gallery')->addCss('height', $height);
$element->style->child('.as-hover-motion-gallery-inner')->addCss('grid-template-rows', 'repeat(' . $rows . ',1fr)');
$element->style->child('.as-hover-motion-row')->addCss('grid-template-columns', 'repeat(' . $columns . ',1fr)');
if (!empty($angle)) {
    $element->style->child('.as-hover-motion-gallery-inner')->addCss('--angle', $angle . 'deg');
}
$element->style->child('.as-hover-motion-gallery::after')->addCss('background-image', Style::getGradientValue($params->get('gallery_overlay_gradient', '')));
if (!empty($gap)) {
    $element->style->child('.as-hover-motion-gallery-inner')->addCss('gap', $gap . 'rem');
    $element->style->child('.as-hover-motion-row')->addCss('gap', $gap . 'rem');
}