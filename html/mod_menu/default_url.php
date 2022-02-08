<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$attributes = array();

if ($item->anchor_title) {
	$attributes['title'] = $item->anchor_title;
}

if ($item->anchor_css) {
	$attributes['class'] = $item->anchor_css;
}

if ($item->anchor_rel) {
	$attributes['rel'] = $item->anchor_rel;
}

$astroid_menu_options = $item->getParams()->get('astroid_menu_options', []);
$astroid_menu_options = (array) $astroid_menu_options;

$linktype = $item->title;

if ($item->menu_image) {
	if ($item->menu_image_css) {
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
	} else {
		$linktype = JHtml::_('image', $item->menu_image, $item->title);
	}

	if ($item->getParams()->get('menu_text', 1)) {
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1) {
	$attributes['target'] = '_blank';
	$attributes['rel'] = 'noopener noreferrer';

	if ($item->anchor_rel == 'nofollow') {
		$attributes['rel'] .= ' nofollow';
	}
} elseif ($item->browserNav == 2) {
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $params->get('window_open');

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

// Show icon html start here
if (isset($astroid_menu_options['icon']) && !empty($astroid_menu_options['icon'])) {
	$iconHtml = '<i class="' . $astroid_menu_options['icon'] . '"></i> ';
} else {
	$iconHtml = "";
}
// Show icon html End here


// Show icon badge here
if (isset($astroid_menu_options['badge']) && !empty($astroid_menu_options['badge'])) {
	$badgeHtml = '<sup><span class="menu-item-badge" style="background:' . $astroid_menu_options['badge_bgcolor'] . ';color:' . $astroid_menu_options['badge_color'] . '">' . $astroid_menu_options['badge_text'] . '</span></sup>';
} else {
	$badgeHtml = "";
}
// Show icon badge End here

// Show icon subtitle here
if (isset($astroid_menu_options['subtitle']) && !empty($astroid_menu_options['subtitle'])) {
	$subtitle = '<small class="nav-subtitle">' . $astroid_menu_options['subtitle'] . '</small>';
} else {
	$subtitle = "";
}
// Show icon subtitle End here

echo JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), '<span class="nav-title">' . $iconHtml . $linktype . $badgeHtml . '</span>' . $subtitle, $attributes);
