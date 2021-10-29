<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
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

$enable_backtotop = $params->get('backtotop', 1);
if (!$enable_backtotop) {
   return;
}

$style = '';
$astyle = '';
$class = [];
$html = '';
$backtotop_icon         = $params->get('backtotop_icon', 'fas fa-arrow-up');
$backtotop_icon_size    = $params->get('backtotop_icon_size', 20);
$backtotop_icon_padding = $params->get('backtotop_icon_padding', 10);
$backtotop_icon_border_size = $params->get('backtotop_icon_border_size', 0);
$backtotop_icon_color   = $params->get('backtotop_icon_color', '');
$backtotop_icon_bgcolor = $params->get('backtotop_icon_bgcolor', '');
$backtotop_icon_bdcolor = $params->get('backtotop_icon_bordercolor', '');
$backtotop_icon_style   = $params->get('backtotop_icon_style', 'circle');
$backtotop_on_mobile    = $params->get('backtotop_on_mobile', 1);
$paddingpercent         = 10;
$padding                = ($backtotop_icon_size / $paddingpercent);
$style                  .= 'font-size:' . $backtotop_icon_size . 'px;';
if ($backtotop_icon_color) {
   $style               .= 'color:' . $backtotop_icon_color . ';';
}
switch ($backtotop_icon_style) {
   case 'rounded':
      $astyle .= 'border-radius : ' . round($padding) . 'px;';
      break;
   case 'square':
      $style .= 'line-height:' . $backtotop_icon_size . 'px;  padding: ' . round($padding) . 'px';
      break;
   default:
      $style .= 'height:' . $backtotop_icon_size . 'px; width:' . $backtotop_icon_size . 'px; line-height:' . $backtotop_icon_size . 'px; text-align:center;';
      break;
}
$astyle     .= 'background:' . $backtotop_icon_bgcolor . ';';
$astyle     .= 'padding:' . $backtotop_icon_padding . 'px;';
if (!empty($backtotop_icon_bdcolor) && !empty($backtotop_icon_border_size)) {
    $astyle     .= 'border: '.$backtotop_icon_border_size.'px solid ' . $backtotop_icon_bdcolor . ';';
}
$class[] = $backtotop_icon_style;

if (!$backtotop_on_mobile) {
   $class[] = 'hideonsm';
   $class[] = 'hideonxs';
}

$html .= '<a title="Back to Top" id="astroid-backtotop" class="' . implode(' ', $class) . '" href="javascript:void(0)" style="' . $astyle . '"><i class="' . $backtotop_icon . '" style="' . $style . '"></i></a>';
echo $html;
