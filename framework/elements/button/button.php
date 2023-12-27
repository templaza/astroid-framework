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

use Astroid\Helper\Style;

extract($displayData);
$buttons        = $params->get('buttons', '');
if (empty($buttons)) {
    return false;
}
$buttons        = json_decode($buttons);
if (!count($buttons)) {
    return false;
}
$button_group   = intval($params->get('button_group', 0));
$button_size    = $params->get('button_size', '');
$button_size    = $button_size ? ' '. $button_size : '';
echo '<div class="'.($button_group ? 'btn-group' : 'as-gutter-lg').'" role="group">';
foreach ($buttons as $button) {
    $btn_params = Style::getSubFormParams($button->params);
    echo '<a id="btn-'.$button->id.'" href="' .$btn_params['link']. '" class="btn btn-' .(intval($btn_params['button_outline']) ? 'outline-' : '').$btn_params['button_style'].$button_size. '">'.$btn_params['title'].'</a>';
}
echo '</div>';