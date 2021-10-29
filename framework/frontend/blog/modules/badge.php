<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/blog/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$params = $article->attribs;
if (is_string($params)) {
    $params = $article->params;
}
$status = (int) $params->get('astroid_article_badge', 0);
if (!$status) {
    return;
}
$type = $params->get('astroid_article_badge_type', 2);
if ($type != 1) {
?>
    <div class="article-badge article-badge-<?php echo $type; ?>"><?php echo \JText::_('ASTROID_ARTICLE_OPTIONS_BADGE_' . $type . '_LBL'); ?></div>
<?php } else { ?>
    <div class="article-badge article-badge-1 article-badge-custom-<?php echo $article->id; ?>"><?php echo \JText::_($params->get('astroid_article_badge_text', '')); ?></div>
<?php
    $color = $params->get('astroid_article_badge_text_color', '#fff');
    $bg = $params->get('astroid_article_badge_color', '#000');
    $style = '.article-badge.article-badge-custom-' . $article->id . '{color: ' . $color . '; background-color: ' . $bg . ';} .article-badge.article-badge-custom-' . $article->id . ':after{ border-left-color: ' . $bg . '}';
    Astroid\Framework::getDocument()->addStyleDeclaration($style);
} ?>