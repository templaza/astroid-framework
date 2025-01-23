<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */

// No direct access.
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
extract($displayData);
Astroid\Framework::getDebugger()->log('Render Body');

$document = Astroid\Framework::getDocument();
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

Astroid\Helper\Head::meta(); // site meta
Astroid\Helper\Head::favicon(); // site favicon

if ($document->isDev()) { // check is dev
    $document->include('comingsoon'); // load coming soon and return
    return;
}
$document->include('bodyStart'); // Body Start
$document->include('preloader'); // load preloader
$document->include('backtotop'); // load back to top

$params = Astroid\Framework::getTemplate()->getParams();
$layout = Astroid\Framework::getTemplate()->getLayout();

$header = $params->get('header', TRUE);
$header_mode = $params->get('header_mode', 'horizontal');

$container_class = ['astroid-container']; // container class
$astroid_content_class = ['astroid-content']; // astroid_content_class
if ($header && !empty($header_mode) && $header_mode == 'sidebar') {
    $astroid_content_class[] = 'has-sidebar';
    $mode = $params->get('header_sidebar_menu_mode', 'left');
    if ($mode == 'topbar') {
        $sidebar_position = $params->get('sidebar_position', 'left');
    } else {
        $sidebar_position = $mode;
    }
    $astroid_content_class[] = 'sidebar-dir-' . $sidebar_position;
    array_push($container_class, 'row', 'g-0');
    $astroid_content_class[] = 'col';
}
if ($header && !empty($header_mode) && $header_mode != 'sidebar') {
    $wa->registerAndUseScript('astroid.jquery.easing', 'astroid/jquery.easing.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
}

// Astroid Layout Background
$background         =   $params->get('container_background_setting', '');
$overlay_type       =   $params->get('container_background_image_overlay', '');
$as_layout_attrs    =   '';
$as_layout_cls      =   '';
if ($background === 'video') {
    $video          =   $params->get('container_background_video', '');
    $poster         =   $params->get('container_background_image', '');
    $as_layout_attrs    .=  ' data-as-video-bg="'.Uri::base(true) . '/' . Astroid\Helper\Media::getPath() . '/' . $video.'"';
    $as_layout_attrs    .=  ' data-as-video-poster="'.Uri::base(true) . '/' . Astroid\Helper\Media::getPath() . '/' . $poster.'"';
    $as_layout_attrs    .=  ' data-as-video-position="fixed"';
    $document->loadVideoBG();
}
if (!empty($overlay_type)) {
    if ($background === 'video') {
        $as_layout_cls  .=  ' position-relative';
    } else {
        $as_layout_cls  .=  ' position-relative astroid-element-overlay';
    }
}
?>
<!-- astroid container -->
<div class="<?php echo implode(' ', $container_class) ?>">
    <?php
    $document->include('containerStart'); // Container Start
    $document->include('header.sidebar'); // sidebar
    ?>
    <!-- astroid content -->
    <div class="<?php echo implode(' ', $astroid_content_class); ?>">
        <?php $document->include('contentStart'); // Content Start 
        ?>
        <!-- astroid layout -->
        <div class="astroid-layout astroid-layout-<?php echo $params->get('template_layout', 'wide'). $as_layout_cls; ?>"<?php echo $as_layout_attrs; ?>>
            <?php $document->include('layoutStart'); // Layout Start 
            ?>
            <!-- astroid wrapper -->
            <div class="astroid-wrapper">
                <?php $document->include('wrapperStart'); // Wrapper Start 
                ?>
                <?php echo Astroid\Element\Layout::render(); ?>
                <?php $document->include('wrapperEnd'); // Wrapper End 
                ?>
            </div>
            <!-- end of astroid wrapper -->
            <?php $document->include('layoutEnd'); // Layout End 
            ?>
        </div>
        <!-- end of astroid layout -->
        <?php $document->include('contentEnd'); // Content End 
        ?>
    </div>
    <!-- end of astroid content -->
    <?php $document->include('containerEnd'); // Container End
    $document->include('offcanvas'); // offcanvas
    $document->include('mobilemenu'); // mobile menu
    ?>
</div>
<!-- end of astroid container -->
<?php $document->include('bodyEnd'); // Body End ?>
<?php Astroid\Framework::getDebugger()->log('Render Body'); ?>
<jdoc:include type="modules" name="debug" style="none" />