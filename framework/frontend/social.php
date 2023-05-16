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
use Astroid\Helper\Style;
$document   = Astroid\Framework::getDocument();
$params     = Astroid\Framework::getTemplate()->getParams();

$enable_social_profiler     = $params->get('enable_social_profiler', 1);
$social_profiles            = $params->get('social_profiles', []);
$style                      = $params->get('social_profiles_style', 1);
$gutter                     = $params->get('social_profiles_gutter', '');
$fontsize                   = $params->get('social_profiles_fontsize', '16px');
$social_icon_color          = Style::getColor($params->get('social_icon_color', ''));
$social_icon_color_hover    = Style::getColor($params->get('social_icon_color_hover', ''));

if (!$enable_social_profiler) return false;

if (!empty($social_profiles)) {
    $social_profiles = json_decode($social_profiles);
}
$class              = $gutter ? 'gx-'.$gutter : '';
$styles             = '';
$social_style       =   new Style('.astroid-social-icons');
$social_style_dark  =   new Style('.astroid-social-icons', 'dark');
if (!empty($fontsize)) {
    $social_style->addCss('font-size', $fontsize);
}
if (!empty($social_icon_color) && $style == 1) {
    $social_style->link()->addCss('color', $social_icon_color['light']. '!important');
    $social_style_dark->link()->addCss('color', $social_icon_color['dark']. '!important');
}
if (!empty($social_icon_color_hover) && $style == 1) {
    $social_style->link()->hover()->addCss('color', $social_icon_color_hover['light'] . '!important');
    $social_style_dark->link()->hover()->addCss('color', $social_icon_color_hover['dark'] . '!important');
}
$social_style->render();
$social_style_dark->render();
?>

<div class="astroid-social-icons row<?php echo !empty($class) ? ' ' . $class : ''; ?>">
    <?php
    foreach ($social_profiles as $social_profile) {
        switch ($social_profile->id) {
            case 'whatsapp':
                $social_profile_link = 'https://wa.me/' . $social_profile->link;
                break;
            case 'telegram':
                $social_profile_link = 'https://t.me/' . $social_profile->link;
                break;
            case 'skype':
                $social_profile_link = 'skype:' . $social_profile->link . '?chat';
                break;
            default:
                $social_profile_link = $social_profile->link;
                break;
        }
        $sid = md5($social_profile->color . $social_profile_link . $social_profile->icon);
        echo '<div class="col"><a title="' . ($social_profile->title ? $social_profile->title : 'Social Icon') . '" ' . ($style != 1 ? ' aria-label="' . $social_profile->title . '" style="color:' . $social_profile->color . '"' : '') . ' href="' . $social_profile_link . '" target="_blank" rel="noopener"><i class="' . $social_profile->icon . '"></i></a></div>';
    }
    ?>
</div>