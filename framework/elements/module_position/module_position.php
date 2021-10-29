<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 * See https://docs.joomdev.com/article/override-core-layouts/ for documentation
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$position = $params->get('position', '');
if (empty($position)) {
    return;
}

echo Astroid\Framework::getDocument()->_positionContent($position, 'before');
$modules = \JModuleHelper::getModules($position);
if (count($modules)) {
    echo '<jdoc:include type="modules" name="' . $position . '" style="astroidxhtml" />';
}
echo Astroid\Framework::getDocument()->_positionContent($position, 'after');
