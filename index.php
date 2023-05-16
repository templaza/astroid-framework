<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// No direct access.
defined('_JEXEC') or die;
defined('_ASTROID') or die('Please install and activate <a href="https://www.astroidframe.work/" target="_blank">Astroid Framework</a> in order to use this template.');

if (file_exists(__DIR__ . "/helper.php")) {
   require_once __DIR__ . "/helper.php"; // Template's Helper
}
$color_mode         =   Astroid\Framework::getTemplate()->getColorMode();
$color_mode_theme   =   $color_mode ? ' data-bs-theme="'.$color_mode.'"' : '';
$document = Astroid\Framework::getDocument(); // Astroid Document
// Output as HTML5
$this->setHtml5(true);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"<?php echo $color_mode_theme; ?>>

<head>
   <astroid:include type="head-meta" /> <!-- document meta -->
   <jdoc:include type="head" /> <!-- joomla head -->
   <astroid:include type="head-styles" /> <!-- head styles -->
   <astroid:include type="head-scripts" /> <!-- head scripts -->
</head> <!-- document head -->

<body class="<?php echo $document->getBodyClass(); ?>">
   <?php $document->include('document.body'); ?>
   <!-- body and layout -->
   <astroid:include type="body-scripts" /> <!-- body scripts -->
</body> <!-- document body -->

</html> <!-- document end -->