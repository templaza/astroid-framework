<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/templates/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 * See https://docs.joomdev.com/article/override-core-layouts/ for documentation
 */

use Joomla\CMS\Helper\ModuleHelper;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$position = $params->get('position', '');
$module_styles = $params->get('module_styles', 'astroidxhtml');
if (empty($position)) {
    return;
}

echo Astroid\Framework::getDocument()->_positionContent($position, 'before');
$modules = ModuleHelper::getModules($position);
if (count($modules)) {
    echo '<jdoc:include type="modules" name="' . $position . '" style="'.$module_styles.'" />';
}
echo Astroid\Framework::getDocument()->_positionContent($position, 'after');