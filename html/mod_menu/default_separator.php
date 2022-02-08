<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';

$linktype   = $item->title;

$astroid_menu_options = $item->getParams()->get('astroid_menu_options', []);
$astroid_menu_options = (array) $astroid_menu_options;

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
?>
<span class="separator <?php echo $anchor_css; ?>" <?php echo $title; ?>><?php echo '<span class="nav-title">' . $iconHtml . $linktype . $badgeHtml . '</span>' . $subtitle; ?></span>