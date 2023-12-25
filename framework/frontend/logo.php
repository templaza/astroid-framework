<?php

/**
 * @package   Astroid Framework
 * @author    TemPlaza https://www.templaza.com
 * @copyright Copyright (C) 2011 - 2021 TemPlaza.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Astroid\Helper\Style;
defined('_JEXEC') or die;

extract($displayData);
$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

// Logo Alt Text
$app = Factory::getApplication();
$sitename = $app->get('sitename');

$logo_type = $params->get('logo_type', 'none'); // Logo Type

if ($logo_type === 'none') {
    return;
}

$header_mode = $params->get('header_mode', 'horizontal');
$header_stacked_menu_mode = $params->get('header_stacked_menu_mode', 'center');

if ($logo_type == 'text') {
    $config = Factory::getApplication()->getConfig();
    $logo_text = $params->get('logo_text', $config->get('sitename')); // Logo Text
    $tag_line = $params->get('tag_line', ''); // Logo Tagline
} else {
    // Logo file
    $default_logo = $params->get('defult_logo', false);
    $default_logo_dark = $params->get('default_logo_dark', false);
    $mobile_logo = $params->get('mobile_logo', false);
    $mobile_logo_dark = $params->get('mobile_logo_dark', false);
    $stickey_header_logo = $params->get('stickey_header_logo', false);
    $stickey_header_logo_dark = $params->get('stickey_header_logo_dark', false);
}
$class = ['astroid-logo', 'astroid-logo-' . $logo_type, 'd-flex align-items-center'];

$logo_link_type = $params->get('logo_link_type', 'default');
$logo_link = Uri::root();
$logo_link_target = '_self';
if ($logo_link_type === 'custom') {
    $logo_link = $params->get('logo_link_custom', '');
    if ($params->get('logo_link_target_blank', 0)) {
        $logo_link_target = '_blank';
    }
}
$position = $position ?? '';
?>
<!-- logo starts -->
<?php if ($logo_type == 'text') : ?>
    <!-- text logo starts -->
    <?php
    $mr = ($header_mode == 'stacked' && ($header_stacked_menu_mode == 'seperated' || $header_stacked_menu_mode == 'center')) ? '' : ' mr-0 mr-lg-4';
    ?>
    <div class="logo-wrapper <?php echo implode(' ', $class); ?> flex-column<?php echo $mr; ?>">
        <?php if ($logo_link_type != 'none') : ?><a target="<?php echo $logo_link_target; ?>" class="site-title" href="<?php echo $logo_link; ?>"><?php endif; ?><?php echo $logo_text; ?><?php if ($logo_link_type != 'none') : ?></a><?php endif; ?>
        <?php
        if ($tag_line) {
            echo '<p class="site-tagline">'. $tag_line .'</p>';
        }
        ?>
    </div>
    <!-- text logo ends -->
<?php endif; ?>
<?php if ($logo_type == 'image') : ?>
    <!-- image logo starts -->
    <?php
    $mr = ($header_mode == 'stacked' && ($header_stacked_menu_mode == 'seperated' || $header_stacked_menu_mode == 'center')) ? '' : ' mr-0 mr-lg-4';
    ?>
    <div class="logo-wrapper astroid-logo">
        <?php if ($logo_link_type != 'none') : ?>
        <a target="<?php echo $logo_link_target; ?>" class="<?php echo implode(' ', $class); ?><?php echo $mr; ?>" href="<?php echo $logo_link; ?>">
        <?php endif; ?>
            <?php if ($position != 'sticky') : // start main logo
                // Default logo
                $default_logo_width     =   $params->get('default_logo_width', '');
                $default_logo_height    =   $params->get('default_logo_height', '');
                $default_logo_style     =   !empty($default_logo_width) ? ' width="'.$default_logo_width.'"' : '';
                $default_logo_style     .=  !empty($default_logo_height) ? ' height="'.$default_logo_height.'"' : '';
                $default_logo_style     =   $default_logo_style != '' ? $default_logo_style : ' width="250px" height="250px"';

                // Set style for image logo default
                $style = new Style('.astroid-logo');
                if (!empty($default_logo_width)) {
                    $style->child('> .astroid-logo-default')->addCss('max-width', $default_logo_width);
                }
                if (!empty($default_logo_height)) {
                    $style->child('> .astroid-logo-default')->addCss('max-height', $default_logo_height);
                }
                $style->render();
                ?>
            <?php
            if (!empty($default_logo)) {
                if (File::getExt($default_logo) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $default_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-default" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $default_logo);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-default"'.$default_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
            <?php
            if (!empty($default_logo_dark)) {
                if (File::getExt($default_logo_dark) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $default_logo_dark; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-default dark" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $default_logo_dark);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-default dark"'.$default_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
            <?php endif; // end of main logo ?>
            <?php if ($position == 'sticky') : //Start sticky logo
                // Mobile logo
                $sticky_logo_width      =   $params->get('sticky_logo_width', '');
                $sticky_logo_height     =   $params->get('sticky_logo_height', '60px');
                $sticky_logo_style      =   !empty($sticky_logo_width) ? ' width="'.$sticky_logo_width.'"' : '';
                $sticky_logo_style      .=  !empty($sticky_logo_height) ? ' height="'.$sticky_logo_height.'"' : '';
                $sticky_logo_style      =   $sticky_logo_style != '' ? $sticky_logo_style : ' width="200px" height="200px"';

                // Set style for image logo
                $style = new Style('.astroid-logo > .astroid-logo-sticky');
                if (!empty($sticky_logo_width)) {
                    $style->addCss('max-width', $sticky_logo_width);
                }
                if (!empty($sticky_logo_height)) {
                    $style->addCss('max-height', $sticky_logo_height);
                }
                $style->render();
                ?>
            <?php if (!empty($stickey_header_logo)) {
                if (File::getExt($stickey_header_logo) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $stickey_header_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-sticky" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $stickey_header_logo);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-sticky"'.$sticky_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
            <?php if (!empty($stickey_header_logo_dark)) {
                if (File::getExt($stickey_header_logo_dark) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $stickey_header_logo_dark; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-sticky dark d-none" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $stickey_header_logo_dark);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-sticky dark d-none"'.$sticky_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
            <?php endif; //end of sticky ?>

            <?php // Start mobile logo
            // Mobile logo
            $mobile_logo_width      =   $params->get('mobile_logo_width', '');
            $mobile_logo_height     =   $params->get('mobile_logo_height', '');
            $mobile_logo_style      =   !empty($mobile_logo_width) ? ' width="'.$mobile_logo_width.'"' : '';
            $mobile_logo_style      .=  !empty($mobile_logo_height) ? ' height="'.$mobile_logo_height.'"' : '';
            $mobile_logo_style      =   $mobile_logo_style != '' ? $mobile_logo_style : ' width="200px" height="200px"';

            // Set style for image logo mobile
            $style = new Style('.astroid-logo');
            if (!empty($mobile_logo_width)) {
                $style->child('> .astroid-logo-mobile')->addCss('max-width', $mobile_logo_width);
            }
            if (!empty($mobile_logo_height)) {
                $style->child('> .astroid-logo-mobile')->addCss('max-height', $mobile_logo_height);
            }
            $style->render();
            if (!empty($mobile_logo)) {
                if (File::getExt($mobile_logo) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $mobile_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-mobile" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $mobile_logo);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-mobile"'.$mobile_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
            <?php if (!empty($mobile_logo_dark)) {
                if (File::getExt($mobile_logo_dark) !== 'svg') {
                    ?><img src="<?php echo Uri::root() . Astroid\Helper\Media::getPath() . '/' . $mobile_logo_dark; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-mobile dark d-none" /><?php
                } else {
                    $logo_svg = file_get_contents(JPATH_ROOT . '/' . Astroid\Helper\Media::getPath() . '/' . $mobile_logo_dark);
                    $logo_svg = preg_replace('/\<svg(.*?)\>/is', '<svg$1 class="astroid-logo-mobile dark d-none"'.$mobile_logo_style.'>', $logo_svg);
                    echo $logo_svg;
                }
            } ?>
        <?php if ($logo_link_type != 'none') : ?>
        </a>
        <?php endif; ?>
    </div>
    <!-- image logo ends -->
<?php endif; ?>
<!-- logo ends -->