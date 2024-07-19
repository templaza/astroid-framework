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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Associations;

extract($displayData);
$position   =   $params->get('infoblock_position','above');
$item       =   $options['article'];
$article_params = $item->article->params;
$assocParam =   (Associations::isEnabled() && $article_params->get('show_associations'));
$info       =   $article_params->get('info_block_position', 0);
$useDefList =   $article_params->get('show_modify_date') || $article_params->get('show_publish_date') || $article_params->get('show_create_date')
    || $article_params->get('show_hits') || $article_params->get('show_category') || $article_params->get('show_parent_category') || $article_params->get('show_author') || $assocParam;

if ($useDefList) {
    if ($position == 'above' && ($info == 0 || $info == 2)) {
        echo LayoutHelper::render('joomla.content.info_block', ['item' => $item->article, 'params' => $article_params, 'astroidArticle' => $item, 'position' => 'above']);
    }
    if ($position == 'below' && ($info == 1 || $info == 2)) {
        echo LayoutHelper::render('joomla.content.info_block', ['item' => $item->article, 'params' => $article_params, 'astroidArticle' => $item, 'position' => 'below']);
    }
}