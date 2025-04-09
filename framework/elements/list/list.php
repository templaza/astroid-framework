<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Helper\Style;
use Astroid\Helper;
use Astroid\Helper\SubForm;

extract($displayData);
$list_items     = new SubForm($params->get('list_items', ''));
if (!count($list_items->getData())) {
    return false;
}
$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-list-title', $title_font_style, null, $element->isRoot);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$content_font_style =   $params->get('content_font_style');
if (!empty($content_font_style)) {
    Style::renderTypography('#'.$element->id.' .as-list-desc', $content_font_style, null, $element->isRoot);
}

$item_margin    =   $params->get('item_margin', '');
$item_padding   =   $params->get('item_padding', '');
$list_style     =   $params->get('list_style', 'ul');
$title_width    =   intval($params->get('title_width', 3));

$tag = match ($list_style) {
    'ol', 'list-group-numbered' => 'ol',
    'list-description' => 'dl',
    default => 'ul',
};

$class = match ($list_style) {
    'list-unstyled', 'list-inline', 'list-group' => $list_style,
    'list-group-flush', 'list-group-numbered' => 'list-group '. $list_style,
    'list-description' => 'row',
    default => ''
};

$class_item = match ($list_style) {
    'list-group', 'list-group-flush', 'list-group-numbered' => 'list-item list-group-item d-flex align-items-start',
    'list-inline' => 'list-item list-inline-item',
    default => 'list-item'
};

$class_item_inner = match ($list_style) {
    'list-group-numbered' => ' ms-2',
    default => ''
};

echo '<'.$tag.' class="' . $class . '">';
foreach ($list_items->getData() as $list) {
    $icon_type      =   $list->params->get('icon_type', 'fontawesome');
    if ($icon_type === 'fontawesome') {
        $icon       =   $list->params->get('fa_icon', '');
    } else {
        $icon       =   $list->params->get('custom_icon', '');
    }
    $title          =   ($icon ? '<i class="'.$icon.' me-2"></i>' : '').$list->params->get('title', '');
    $description    =   $list->params->get('description', '');
    if ($list_style === 'list-description') {
        echo '<dt class="as-list-title col-'.$title_width.'">'.$title.'</dt>';
        echo '<dd class="as-list-desc col-'.($title_width < 12 ? 12-$title_width : 12).'">'.$description.'</dd>';
    } else {
        echo '<li class="'.$class_item.'">';
        echo '<div class="list-item-inner'.$class_item_inner.'">';
        echo $title ? '<'.$title_html_element.' class="as-list-title">'. $title . '</'.$title_html_element.'>' : '';
        echo $description ? '<div class="as-list-desc">'. $description . '</div>' : '';
        echo '</div>';
        echo '</li>';
    }
}
echo '</'.$tag.'>';

if (!empty($title_heading_margin)) {
    Style::setSpacingStyle($element->style->child('.as-list-title'), $title_heading_margin, 'margin');
}

// Item Margin
if (!empty($item_margin)) {
    Style::setSpacingStyle($element->style->child('.list-item'), $item_margin, 'margin');
}
// Item Padding
if (!empty($item_padding)) {
    Style::setSpacingStyle($element->style->child('.list-item'), $item_padding);
}