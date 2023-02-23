<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
extract($displayData);

$moduleTag     = htmlspecialchars($params->get('module_tag', 'div'), ENT_QUOTES, 'UTF-8');
$bootstrapSize = (int) $params->get('bootstrap_size', 0);
$moduleClass   = $bootstrapSize !== 0 ? ' span' . $bootstrapSize : '';
$headerTag     = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
$headerClass   = htmlspecialchars($params->get('header_class', 'page-header'), ENT_COMPAT, 'UTF-8');

if ($module->content) {
    echo '<' . $moduleTag . ' class="moduletable split-title-module' . ($params->get('moduleclass_sfx') ? htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8')  : '') . $moduleClass . '">';

    if ($module->showtitle) {
        $title = explode('|', $module->title);
        $html  = '';
        $html .= '<' . $headerTag . ' class="module-title split-title ' . $headerClass . '">';
        $index = 1;
        foreach ($title as $title_text) {
            $html .= '<span class="split-' . $index . '">' . $title_text . '</span>';
            $index++;
        }
        $html .= '</' . $headerTag . '>';
        echo $html;
    }

    echo $module->content;
    echo '</' . $moduleTag . '>';
}
