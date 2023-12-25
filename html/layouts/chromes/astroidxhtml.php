<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
use Joomla\CMS\Layout\FileLayout;
/**
 * TO OVERRIDE THIS LAYOUT
 * 1. Remove line "require Astroid\Helper\Overrides::getHTMLTemplate()";
 * 2. Copy source code from libraries/astroid/framework/html/{find a file that you will override here}
 * 3. Paste source code to below and start to edit.
 */
$renderer = new FileLayout('chromes.astroidxhtml', JPATH_LIBRARIES . '/astroid/framework/html/layouts');
echo $renderer->render($displayData);