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
$template   = Astroid\Framework::getTemplate();
$params = $template->getParams();
$color_mode_type = $params->get('astroid_color_mode_enable', 0);
if ($color_mode_type != 1) {
    return;
}
$color_mode = $template->getColorMode();
if ($color_mode) {
    $enable_color_mode_transform    =   $params->get('enable_color_mode_transform', 0);
    if (!$enable_color_mode_transform) {
        echo '<div class="d-flex align-items-center astroid-color-mode"><div class="form-check form-switch"><input class="form-check-input switcher" type="checkbox" aria-label="Color Mode" role="switch"'.($color_mode == 'dark' ? ' checked' : '').'></div></div>';
    }
}