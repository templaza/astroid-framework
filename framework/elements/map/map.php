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

use Astroid\Helper\Style;
use Joomla\CMS\Factory;
use Astroid\Helper;
use Joomla\CMS\Filter\OutputFilter;

extract($displayData);

$map_option = $params->get('map_option', '');
$gmap_data  =   new stdClass();
$gmap_data->title = $params->get('title', '');
$gmap_data->height = $params->get('height', '');
$gmap_data->zoom = $params->get('zoom', 0);
switch ($map_option) {
    case 'basic':
        $gmap_data->location = $params->get('location', '');
        echo '<div class="d-flex">';
        echo '<iframe loading="lazy" class="as-gmap w-100" src="https://maps.google.com/maps?q='.OutputFilter::stringUrlSafe($gmap_data->location).'&amp;t=m&amp;z='.$gmap_data->zoom.'&amp;output=embed&amp;iwloc=near" title="'.$gmap_data->title.'" aria-label="'.$gmap_data->title.'"></iframe>';
        echo '</div>';
        break;
    case 'advanced':
        $map = $params->get('map', '');
        if (!$map) return false;
        $map = explode(',', $map);
        $gmap_data->lat = trim($map[0]);
        $gmap_data->lng = trim($map[1]);
        $gmap_data->infowindow  = $params->get('infowindow', '');
        $gmap_data->type = $params->get('type', 'roadmap');
        $gmap_data->mousescroll = $params->get('mousescroll', 1);
        $gmap_data->show_controllers = $params->get('show_controllers', 1);

        $multi_location         = $params->get('multi_location', 0);
        $multi_location_items   = json_decode($params->get('multi_location_items', ''));
        $location_addr = [];
        if ($multi_location && !empty($multi_location_items) && count($multi_location_items)) {
            foreach ($multi_location_items as $key => $location_item)
            {
                $item    =   Style::getSubFormParams($location_item->params);
                $lat_long = explode(',', $item['location_item']);
                $location_addr[] = array('address' => $item['location_popup_text'], 'latitude' => trim($lat_long[0]), 'longitude' => trim($lat_long[1]));
            }
        }
        $gmap_data->locations = $location_addr;

        $pluginParams   =   Helper::getPluginParams();
        $gmap_api       =   $pluginParams->get('gmap_api', '');
        echo '<div id="as-widget-map-' . $element->id . '" class="as-gmap d-none">'.json_encode($gmap_data).'</div>';
        Factory::getDocument()->getWebAssetManager()->registerAndUseScript('gmap.api', "https://maps.googleapis.com/maps/api/js?key=" . $gmap_api .'&loading=async', [], ['async' => 'true'], []);
        Factory::getDocument()->getWebAssetManager()->registerAndUseScript('astroid.gmap', "astroid/gmap.min.js", ['relative' => true, 'version' => 'auto'], [], ['jquery', 'gmap.api']);
        break;
}
Style::addCssBySelector('#'. $element->id . ' .as-gmap', 'height', $gmap_data->height.'px');