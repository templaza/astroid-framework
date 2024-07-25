<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Astroid Article/Blog
if (!isset($astroidArticle)) {
    $astroidArticle = new Astroid\Article($this->item, false, $this->params);
}
echo '<div class="com-content-article item-page'.$this->pageclass_sfx.'" itemscope itemtype="https://schema.org/Article">';
echo '<meta itemprop="inLanguage" content="'.(($this->item->language === '*') ? Factory::getApplication()->get('language') : $this->item->language).'">';
$astroidArticle->renderLayout();
echo '</div>';