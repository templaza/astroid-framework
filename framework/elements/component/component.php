<?php
/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/component/component.php folder to create and override
 * See https://docs.joomdev.com/article/override-core-layouts/ for documentation
 */
use Joomla\CMS\Factory;
// No direct access.
defined('_JEXEC') or die;

$doc = Factory::getApplication()->getDocument();
$data = $doc->getBuffer('component');
$sPattern = '/\s*/m';
$sReplace = '';
$ndata = preg_replace($sPattern, $sReplace, $data);
if (empty($ndata)) {
   return;
}
?>
<main class="astroid-component-area">
   <jdoc:include type="component" />
</main>