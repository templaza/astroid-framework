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

use Astroid\Framework;
use Astroid\Helper;
use Joomla\CMS\Factory;

extract($displayData);
$document           =   Framework::getDocument();
$astroid_libs       =   json_decode($params->get('astroid_libs', '[]'), true);
if (count($astroid_libs)) {
    foreach ($astroid_libs as $lib) {
        switch ($lib['value']) {
            case 'gsap':
                $document->loadGSAP();
                break;
            case 'gsap.Flip':
                $document->loadGSAP('Flip');
                break;
            case 'fancybox':
                $document->loadFancyBox();
                break;
            case 'masonry':
                $document->loadMasonry();
                break;
            case 'slick':
                $document->loadSlick();
                break;
            case 'imagesloaded':
                $document->loadImagesLoaded();
                break;
        }
    }
}
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$external_css       =   $params->get('external_css', '');
if (!empty($external_css)) {
    $external_css   =   json_decode($external_css);
    if (count($external_css)) {
        foreach ($external_css as $key => $css) {
            $css_params     =   Helper::loadParams($css->params);
            $wa->registerAndUseStyle($element->id.'.css-'.$key, $css_params->get('url', ''));
        }
    }
}
$external_js        =   $params->get('external_js', '');
if (!empty($external_js)) {
    $external_js    =   json_decode($external_js);
    if (count($external_js)) {
        foreach ($external_js as $key => $js) {
            $js_params      =   Helper::loadParams($js->params);
            $wa->registerAndUseScript($element->id.'.js-'.$key, $js_params->get('url', ''));
        }
    }
}
echo $params->get('custom_html', '');
$custom_css     =   $params->get('custom_css', '');
$custom_js      =   $params->get('custom_js', '');
if (!empty($custom_css)) {
    $wa->addInlineStyle($custom_css);
}
if (!empty($custom_js)) {
    $wa->addInlineScript($custom_js);
}