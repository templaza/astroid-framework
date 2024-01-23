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
use Astroid\Helper\Style;
$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

$enable_backtotop = $params->get('backtotop', 1);
if (!$enable_backtotop) {
   return;
}

$class = [];
$html = '';
$backtotop_icon         = $params->get('backtotop_icon', 'fas fa-arrow-up');
$backtotop_icon_size    = $params->get('backtotop_icon_size', 20);
$backtotop_icon_padding = $params->get('backtotop_icon_padding', 10);
$backtotop_icon_border_size = $params->get('backtotop_icon_border_size', 0);
$backtotop_icon_color   = Style::getColor($params->get('backtotop_icon_color', ''));
$backtotop_icon_bgcolor = Style::getColor($params->get('backtotop_icon_bgcolor', ''));
$backtotop_icon_bdcolor = Style::getColor($params->get('backtotop_icon_bordercolor', ''));
$backtotop_icon_style   = $params->get('backtotop_icon_style', 'circle');
$backtotop_on_mobile    = $params->get('backtotop_on_mobile', 1);
$paddingpercent         = 10;
$padding                = ($backtotop_icon_size / $paddingpercent);
$a_style        =   new Style('#astroid-backtotop');
$a_style_dark   =   new Style('#astroid-backtotop', 'dark');
$i_style        =   new Style('#astroid-backtotop > i');
$i_style_dark   =   new Style('#astroid-backtotop > i', 'dark');
$i_style->addCss('font-size', $backtotop_icon_size . 'px');
$i_style->addCss('color', $backtotop_icon_color['light']);
$i_style_dark->addCss('color', $backtotop_icon_color['dark']);

switch ($backtotop_icon_style) {
   case 'rounded':
       $a_style->addCss('border-radius', round($padding) . 'px !important');
      break;
   case 'square':
       $i_style->addCss('line-height', $backtotop_icon_size . 'px');
       $i_style->addCss('padding', round($padding) . 'px');
      break;
   default:
       $i_style->addCss('width', $backtotop_icon_size . 'px');
       $i_style->addCss('height', $backtotop_icon_size . 'px');
       $i_style->addCss('line-height', $backtotop_icon_size . 'px');
       $i_style->addCss('text-align', 'center');
      break;
}

$a_style->addCss('background', $backtotop_icon_bgcolor['light']);
$a_style_dark->addCss('background', $backtotop_icon_bgcolor['dark']);
$a_style->addCss('padding', $backtotop_icon_padding . 'px');
if (!empty($backtotop_icon_border_size)) {
    $a_style->addCss('border', $backtotop_icon_border_size.'px solid ' . $backtotop_icon_bdcolor['light']);
    $a_style_dark->addCss('border-color', $backtotop_icon_bdcolor['dark']);
}

$a_style->render();
$i_style->render();
$a_style_dark->render();
$i_style_dark->render();
$class[] = $backtotop_icon_style;

if (!$backtotop_on_mobile) {
   $class[] = 'hideonsm';
   $class[] = 'hideonxs';
}

$html .= '<a title="Back to Top" id="astroid-backtotop" class="' . implode(' ', $class) . '" href="#"><i class="' . $backtotop_icon . '"></i></a>';
echo $html;
