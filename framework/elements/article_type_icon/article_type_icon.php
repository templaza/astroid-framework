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
extract($displayData);
$item = $options['article'];
$article_type = $item->attribs->get('astroid_article_type', 'regular');

$document = Astroid\Framework::getDocument();
$document->loadASIcon();
$icon = match ($article_type) {
    'quote' => 'as-icon as-icon-quote-open text-primary-emphasis',
    'review' => 'as-icon as-icon-star text-warning-emphasis',
    'video' => 'as-icon as-icon-film-play text-danger-emphasis',
    'audio' => 'as-icon as-icon-music-note3 text-info-emphasis',
    'gallery' => 'as-icon as-icon-film2 text-warning-emphasis',
    default => 'as-icon as-icon-document text-secondary-emphasis',
};

echo '<div class="article-type-icon"><i class="' . $icon . '"></i></div>';
$article_icon_size = $params->get('article_icon_size', '');
$article_icon_size_data = json_decode($article_icon_size, true);
$element->style->child('.article-type-icon')->addResponsiveCSS('font-size', $article_icon_size_data, $article_icon_size_data['postfix']);