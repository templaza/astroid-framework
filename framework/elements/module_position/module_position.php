<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/{YOUR_TEMPLATE_NAME}/astroid/elements/module_position/module_position.php folder to create and override
 */

use Joomla\CMS\Helper\ModuleHelper;
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$type = $params->get('type', '');
if ($type == 'module') {
    $moduleid = $params->get('module', 0);
    if (!empty($moduleid)) {
        $document = Astroid\Framework::getDocument();
        echo $document->loadModule("{loadmoduleid $moduleid}");
    }
} else {
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
}