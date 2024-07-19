<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /astroid/ folder.
 * Just copy the file to JROOT/media/templates/site/YOUR_ASTROID_TEMPLATE/astroid/elements/module_position/module_position.php folder to create and override
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;

extract($displayData);
$item       =   $options['article'];
$article_params = $item->article->params;

if ($article_params->get('show_tags', 1) && !empty($item->article->tags->itemTags)) {
    $item->article->tagLayout = new FileLayout('joomla.content.tags');
    echo $item->article->tagLayout->render($item->article->tags->itemTags);
}