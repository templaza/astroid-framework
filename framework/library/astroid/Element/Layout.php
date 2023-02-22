<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

namespace Astroid\Element;

use Astroid\Framework;

defined('_JEXEC') or die;

class Layout
{
    public static function render()
    {
        Framework::getDebugger()->log('Render Layout');
        $template = Framework::getTemplate();
        $layout = $template->getLayout();
        $content = '';
        foreach ($layout['sections'] as $section) {
            $section = new Section($section);
            $content .= $section->render();
        }
        Framework::getDebugger()->log('Render Layout');
        return $content;
    }
}
