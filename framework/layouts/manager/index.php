<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

use Joomla\CMS\Factory;

defined('JPATH_BASE') or die;
extract($displayData);
$app      = Factory::getApplication();
$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();
$plugin_params = Astroid\Helper::getPluginParams();
$color_mode =   $plugin_params->get('astroid_color_mode_enable', 0);
$color_mode_theme  =   $app->input->cookie->get('astroid_colormode', 'light');
?>
<!DOCTYPE html>
<html id="astroid-html" data-bs-theme="<?php echo $color_mode_theme; ?>">
<head>
    <meta charset="utf-8" />
    <meta name="generator" content="Astroid Framework | Template Manager" />
    <title><?php echo $template->title; ?></title>
    <link href="<?php echo ASTROID_MEDIA_URL . 'images/favicon.png'; ?>" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script type="application/json" id="astroid-script-options"><?php echo json_encode($document->getScriptOptions()) ?></script>
    <?php echo $document->getStylesheets(); ?>
    <?php echo $document->getScripts('head'); ?>
</head>
<body>
<div id="astroid-app"></div>
<?php echo $document->getScripts('body'); ?>
</body>
</html>