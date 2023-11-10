<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Layout\FileLayout;

$renderer = new FileLayout('chromes.astroidxhtml', JPATH_LIBRARIES . '/astroid/framework/layouts');
echo $renderer->render($displayData);