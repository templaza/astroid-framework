<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$document   = Astroid\Framework::getDocument();
$params     = Astroid\Framework::getTemplate()->getParams();

$enable_social_profiler     = $params->get('enable_social_profiler', 1);
$social_profiles            = $params->get('social_profiles', []);
$style                      = $params->get('social_profiles_style', 1);
$gutter                     = $params->get('social_profiles_gutter', '');
$fontsize                   = $params->get('social_profiles_fontsize', '16px');
$social_icon_color          = $params->get('social_icon_color', '');
$social_icon_color_hover    = $params->get('social_icon_color_hover', '');

if (!$enable_social_profiler) return false;

if (!empty($social_profiles)) {
    $social_profiles = json_decode($social_profiles);
}
$class              = $gutter ? 'gx-'.$gutter : '';
$styles             = '';
if (!empty($fontsize)) {
    $styles         .=  '.astroid-social-icons {font-size:'.$fontsize.'}';
}
if (!empty($social_icon_color) && $style == 1) {
    $styles         .= '.astroid-social-icons a{ color: ' . $social_icon_color . ' !important;}';
}
if (!empty($social_icon_color_hover) && $style == 1) {
    $styles         .= '.astroid-social-icons a:hover{ color: ' . $social_icon_color_hover . ' !important;}';
}
$document->addStyledeclaration($styles);
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