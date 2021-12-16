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
defined('_JEXEC') or die;

extract($displayData);
$params = Astroid\Framework::getTemplate()->getParams();
$document = Astroid\Framework::getDocument();

// Logo Alt Text
$app = JFactory::getApplication();
$sitename = $app->get('sitename');

$logo_type = $params->get('logo_type', 'none'); // Logo Type

if ($logo_type === 'none') {
    return;
}

$header_mode = $params->get('header_mode', 'horizontal');
$header_stacked_menu_mode = $params->get('header_stacked_menu_mode', 'center');

if ($logo_type == 'text') {
    $config = JFactory::getConfig();
    $logo_text = $params->get('logo_text', $config->get('sitename')); // Logo Text
    $tag_line = $params->get('tag_line', ''); // Logo Tagline
} else {
    // Logo file
    $default_logo = $params->get('defult_logo', false);
    $mobile_logo = $params->get('mobile_logo', false);
    $stickey_header_logo = $params->get('stickey_header_logo', false);
}
$class = ['astroid-logo', 'astroid-logo-' . $logo_type, 'd-flex align-items-center'];

$logo_link_type = $params->get('logo_link_type', 'default');
$logo_link = \JURI::root();
$logo_link_target = '_self';
if ($logo_link_type === 'custom') {
    $logo_link = $params->get('logo_link_custom', '');
    if ($params->get('logo_link_target_blank', 0)) {
        $logo_link_target = '_blank';
    }
}

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
            <?php if (!empty($default_logo)) { ?>
                <img src="<?php echo JURI::root() . Astroid\Helper\Media::getPath() . '/' . $default_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-default" />
            <?php } ?>
            <?php if (!empty($mobile_logo)) { ?>
                <img src="<?php echo JURI::root() . Astroid\Helper\Media::getPath() . '/' . $mobile_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-mobile" />
            <?php } ?>
            <?php if (!empty($stickey_header_logo)) { ?>
                <img src="<?php echo JURI::root() . Astroid\Helper\Media::getPath() . '/' . $stickey_header_logo; ?>" alt="<?php echo $sitename; ?>" class="astroid-logo-sticky" />
            <?php } ?>
        <?php if ($logo_link_type != 'none') : ?>
        </a>
        <?php endif; ?>
    </div>
    <!-- image logo ends -->
<?php endif; ?>
<!-- logo ends -->