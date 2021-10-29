<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;
extract($displayData);
$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();
$plugin_params = Astroid\Helper::getPluginParams();
$mediaVersion = Astroid\Helper::joomlaMediaVersion();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="generator" content="Astroid Framework | Template Manager" />
    <title><?php echo $template->title; ?></title>
    <link href="<?php echo ASTROID_MEDIA_URL . 'images/favicon.png'; ?>" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!--[if IE]><script src="<?php echo \JURI::root(); ?>media/system/js/html5fallback.js?<?php echo $mediaVersion; ?>"></script><![endif]-->
    <!--[if IE]><script src="<?php echo \JURI::root(); ?>media/system/js/polyfill.filter.js?<?php echo $mediaVersion; ?>"></script><![endif]-->
    <!--[if IE]><script src="<?php echo \JURI::root(); ?>media/jui/js/html5.js?<?php echo $mediaVersion; ?>"></script><![endif]-->
    <script>
        var SITE_URL = '<?php echo \JURI::root(); ?>';
        var BASE_URL = '<?php echo \JURI::root(); ?>administrator/';
        var TEMPLATE_NAME = '<?php echo $template->template; ?>-<?php echo $template->id; ?>';
        var SYSTEM_FONTS = <?php echo json_encode(array_keys(Astroid\Helper\Font::$system_fonts)); ?>;
        var LIBRARY_FONTS = <?php echo json_encode(array_keys(Astroid\Helper\Font::getUploadedFonts($template->template))); ?>;
        var TEMPLATE_PRESETS = <?php echo \json_encode($template->getPresets()); ?>;
        var ASTROID_IS_MOBILE = false;
        var ASTROID_GRIDS = <?php echo \json_encode(Astroid\Helper\Constants::$layout_grids); ?>;
        var astroid_shortcut_enable = <?php echo $plugin_params->get('astroid_shortcut_enable', 1) ? 'true' : 'false'; ?>;
        var TPL_ASTROID_NEW_FOLDER_NAME_LBL = "<?php echo \JText::_('TPL_ASTROID_NEW_FOLDER_NAME_LBL'); ?>";
        var TPL_ASTROID_NEW_FOLDER_NAME_INVALID = "<?php echo \JText::_('TPL_ASTROID_NEW_FOLDER_NAME_INVALID'); ?>";
        var TIMEZONE = '<?php echo $config = JFactory::getConfig()->get('offset'); ?>';
    </script>
    <astroid:include type="head-styles" /> <!-- head styles -->
    <astroid:include type="head-scripts" /> <!-- head scripts -->
</head>

<body ng-app="astroid-framework" id="astroid-framework" ng-controller="astroidController">
    <!-- loader --> <?php $document->include('manager.loader'); ?>
    <div id="astroid-wrapper" class="container-fluid">
        <div id="astroid-manager-disabled"></div>
        <div class="row position-relative">
            <!-- sidebar --> <?php $document->include('manager.sidebar'); ?>
            <!-- content --> <?php $document->include('manager.content'); ?>
        </div>
        <!-- navbar --> <?php $document->include('manager.navbar'); ?>
    </div>
    <!-- form model --> <?php $document->include('manager.formmodal'); ?>
    <!-- shortcut keys --> <?php $document->include('manager.help'); ?>

    <!-- hidden links for export and download -->
    <a href="#" class="d-none" data-template-name="<?php echo \JFilterOutput::stringURLSafe($template->title); ?>" id="export-link">Export Settings</a>
    <a href="#" class="d-none" id="downloadAnchorElem">Export Preset</a>

    <astroid:include type="body-scripts" /> <!-- body scripts -->
    <!-- javascript --> <?php $document->include('manager.script'); ?>
    <!-- javascript --> <?php $document->include('manager.templates'); ?>
    <astroid:include type="debug" /> <!-- astroid debug -->
</body>

</html>