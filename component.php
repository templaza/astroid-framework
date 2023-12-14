<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
/** @var JDocumentHtml $this */
$app = Factory::getApplication();

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="head" />
    <astroid:include type="head-styles" /> <!-- head styles -->
    <astroid:include type="head-scripts" /> <!-- head scripts -->
</head>
<body class="contentpane component">
<jdoc:include type="message" />
<jdoc:include type="component" />
<?php Astroid\Helper\Head::scripts(); // site scripts ?>
<astroid:include type="body-scripts" /> <!-- body scripts -->
</body>
</html>
