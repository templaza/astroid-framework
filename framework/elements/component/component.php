<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/component/component.php folder to create and override
 * See https://docs.joomdev.com/article/override-core-layouts/ for documentation
 */
// No direct access.
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$data = $doc->getBuffer('component');
$sPattern = '/\s*/m';
$sReplace = '';
$ndata = preg_replace($sPattern, $sReplace, $data);
if (empty($ndata)) {
   return;
}
?>
<div class="astroid-component-area">
   <jdoc:include type="component" />
</div>