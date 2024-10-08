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
use Astroid\Component\Article;
use Astroid\Framework;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

extract($displayData);
$catids         = json_decode($params->get('catids', '[]'), true);

if (!count($catids)) {
    return false;
}
$categories = [];
foreach ($catids as $catid) {
    $categories[]   =   $catid['value'];
}
$document           =   Framework::getDocument();
$limit              =   $params->get('limit', 3);
$ordering           =   $params->get('ordering', 'latest');
$items = Article::getArticles($limit, $ordering, $categories);

$style = new Style('#'. $element->id);
$style_dark = new Style('#'. $element->id, 'dark');

// Image Options
$img_rounded_size   =   $params->get('img_rounded_size', '3');
$img_border_radius  =   $params->get('img_border_radius', '');
if ($img_border_radius == 'rounded') {
    $img_border_radius  = ' ' . $img_border_radius . '-' . $img_rounded_size;
} else {
    $img_border_radius  = $img_border_radius !== '' ? ' ' . $img_border_radius : '';
}

$title_html_element =   $params->get('title_html_element', 'h3');
$title_font_style   =   $params->get('title_font_style');
if (!empty($title_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-heading', $title_font_style);
}
$title_heading_margin=  $params->get('title_heading_margin', '');

$info_after_title   =   json_decode($params->get('info_after_title', '[]'), true);

$info_font_style    =   $params->get('info_font_style');
if (!empty($info_font_style)) {
    Style::renderTypography('#'.$element->id.' .astroid-article-info', $info_font_style);
}

$info_margin                =   $params->get('info_margin', '');

echo '<div class="article-listing as-image-hover-menu">';
foreach ($items as $key => $item) {
    $link           =   RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language);
    $media          =   '';
    if (!empty($item->image_thumbnail)) {
        $media      =   ' data-img="'. $item->image_thumbnail .'"';
    }
    echo '<div class="astroid-article-item as-image-hover-item '.$item->post_format.'"'.$media.'>';

    if (!empty($item->title)) {
        echo '<'.$title_html_element.' class="astroid-article-heading as-image-hover-text"><a href="'.Route::_($link).'" title="'. $item->title . '" class="as-image-hover-text-inner">'. $item->title . '</a></'.$title_html_element.'>';
    }
    if (count($info_after_title)) {
        echo '<dl class="astroid-article-info as-image-hover-sub after-title as-gutter-x-lg">';
        echo '<dt class="article-info-term">'.Text::_('COM_CONTENT_ARTICLE_INFO').'</dt>';
        foreach ($info_after_title as $info_item) {
            echo LayoutHelper::render('joomla.content.info_block.' . $info_item['value'], array('item' => $item, 'params' => $item->params));
        }
        echo '</dl>';
    }
    echo '</div>';
}
echo '</div>';
$document->loadGSAP();
$document->loadImagesLoaded();
$mainframe = Factory::getApplication();
$wa = $mainframe->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('astroid.image_hover_menu', 'media/astroid/assets/vendor/image_hover_menu/css/base.min.css');
$wa->registerAndUseScript('astroid.image_hover_menu', 'media/astroid/assets/vendor/image_hover_menu/js/index.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
if (!empty($title_heading_margin)) {
    $margin = \json_decode($title_heading_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.astroid-article-heading')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
if (!empty($info_margin)) {
    $margin = \json_decode($info_margin, false);
    foreach ($margin as $device => $props) {
        $style->child('.astroid-article-info.after-title')->addStyle(Style::spacingValue($props, "margin"), $device);
    }
}
$style->render();
$style_dark->render();