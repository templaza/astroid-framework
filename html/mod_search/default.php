<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

// Including fallback code for the placeholder attribute in the search field.
// Joomla core mod_search includes it too but I don't think we need it.
//JHtml::_('jquery.framework');
//JHtml::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

if ($width) {
	$moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
	$css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
	Factory::getDocument()->addStyleDeclaration($css);
	$width = ' size="' . $width . '"';
} else {
	$width = '';
}
?>
<div class="search">
	<form action="<?php echo Route::_('index.php'); ?>" method="post" class="form-group">
		<div class="input-group">
			<?php
			$output = '<!-- <label for="mod-search-searchword' . $module->id . '" class="element-invisible">' . $label . '</label> -->';
			$output .= '<input name="searchword" id="mod-search-searchword' . $module->id . '" maxlength="' . $maxlength . '"  class="inputbox form-control search-query input-medium" type="search"' . $width;
			$output .= ' placeholder="' . $text . '" />';

			if ($button) :
				if ($imagebutton) :
					$btn_output = ' <div class="input-group-prepend"><input type="image" alt="' . $button_text . '" class="button btn btn-primary" src="' . $img . '" onclick="this.form.searchword.focus();"/></div>';
				else :
					$btn_output = ' <div class="input-group-prepend"><button class="button btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button></div>';
				endif;

				switch ($button_pos):
					case 'top':
						$output = $btn_output . '<br />' . $output;
						break;

					case 'bottom':
						$output .= '<br />' . $btn_output;
						break;

					case 'right':
						$output .= $btn_output;
						break;

					case 'left':
					default:
						$output = $btn_output . $output;
						break;
				endswitch;
			endif;

			echo $output;
			?>
			<input type="hidden" name="task" value="search" />
			<input type="hidden" name="option" value="com_search" />
			<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
		</div>
	</form>
</div>