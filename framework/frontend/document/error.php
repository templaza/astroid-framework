<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
use Joomla\CMS\Factory;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$document = Astroid\Framework::getDocument();
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

Astroid\Helper\Head::meta(); // site meta
Astroid\Helper\Head::scripts(); // site scripts
Astroid\Helper\Head::favicon(); // site favicon

$document->addScript('vendor/jquery/jquery-3.4.1.min.js', 'body');
$document->addScript('vendor/bootstrap/js/popper.min.js', 'body');
$document->addScript('vendor/bootstrap/js/bootstrap.min.js', 'body');
if ($document->isDev()) { // check is dev
    $document->include('comingsoon'); // load coming soon and return
    return;
}
$document->include('preloader'); // load preloader
$document->include('backtotop'); // load back to top

$params = Astroid\Framework::getTemplate()->getParams();
$layout = Astroid\Framework::getTemplate()->getLayout();

$header = $params->get('header', TRUE);
$header_mode = $params->get('header_mode', 'horizontal');

$astroid_content_class = ['astroid-content']; // astroid_content_class
if ($header && !empty($header_mode) && $header_mode == 'sidebar') {
    $astroid_content_class[] = 'has-sidebar';
    $astroid_content_class[] = 'sidebar-dir-' . $params->get('header_sidebar_menu_mode', 'left');
}
if ($header && !empty($header_mode) && $header_mode != 'sidebar') {
//    $document->addScript('vendor/jquery/jquery.easing.min.js', 'body');
    $wa->registerAndUseScript('astroid.jquery.easing', 'astroid/jquery.easing.min.js', ['relative' => true, 'version' => 'auto'], [], ['jquery']);
}
?>
<!-- astroid container -->
<div class="astroid-container">
    <?php
    $document->include('header.sidebar'); // sidebar
    $document->include('offcanvas'); // offcanvas
    $document->include('mobilemenu'); // mobile menu
    ?>
    <!-- astroid content -->
    <div class="<?php echo implode(' ', $astroid_content_class); ?>">
        <!-- astroid layout -->
        <div class="astroid-layout astroid-layout-<?php echo $params->get('template_layout', 'wide') ?>">
            <!-- astroid wrapper -->
            <div class="astroid-wrapper">
                <?php echo Astroid\Element\Layout::render(); ?>
            </div>
            <!-- end of astroid wrapper -->
        </div>
        <!-- end of astroid layout -->
    </div>
    <!-- end of astroid content -->
</div>
<!-- end of astroid container -->
<?php Astroid\Framework::getDebugger()->log('Render Body'); ?>