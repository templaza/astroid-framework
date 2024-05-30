<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Astroid\Helper;
use Joomla\CMS\Factory;

extract($displayData);
$accordions     = $params->get('accordions', '');
if (empty($accordions)) {
    return false;
}
$accordions     = json_decode($accordions);
if (!count($accordions)) {
    return false;
}
$style          = $params->get('style', '');
$style          = $style !== '' ? ' '. $style : '';
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('bootstrap.collapse');
echo '<div class="accordion'.$style.'" id="accordion-'.$element->id.'">';
foreach ($accordions as $key => $accordions) {
    $item_params = Helper::loadParams($accordions->params);
    echo '<div class="accordion-item">';

    echo '<h2 class="accordion-header">';
    echo '<button class="accordion-button'.($key != 0 ? ' collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$element->id.$key.'" aria-expanded="true" aria-controls="collapse'.$element->id.$key.'">'.$item_params->get('title', '').'</button>';
    echo '</h2>';

    echo '<div id="collapse'.$element->id.$key.'" class="accordion-collapse collapse'.($key == 0 ? ' show' : '').'" data-bs-parent="#accordion-'.$element->id.'">';
    echo '<div class="accordion-body">'. $item_params->get('content', '') . '</div>';
    echo '</div>';

    echo '</div>';
}
echo '</div>';