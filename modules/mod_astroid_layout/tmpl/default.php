<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
use Astroid\Element\Layout;
use Astroid\Element\Section;
$layout = Layout::loadModuleLayout($module->id);
$devices = isset($layout['devices']) && $layout['devices'] ? $layout['devices'] : [
    [
        'code'=> 'lg',
        'icon'=> 'fa-solid fa-computer',
        'title'=> 'title'
    ]
];
$content = '';
foreach ($layout['sections'] as $section) {
    $section = new Section($section, $devices);
    $content .= $section->render();
}
echo $content;