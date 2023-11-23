<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;

use Astroid\Article;
use Joomla\CMS\Language\Text;

$blockPosition = $displayData['params']->get('info_block_position', 0);
if (!isset($displayData['astroidArticle'])) {
   $displayData['astroidArticle'] = new Article($displayData['item']);
}
?>
<dl class="article-info muted">
   <?php
   if (
       $displayData['position'] === 'above' && ($blockPosition == 0 || $blockPosition == 2)
       || $displayData['position'] === 'below' && ($blockPosition == 1)
   ) : ?>
       <dt class="article-info-term">
           <?php if ($displayData['params']->get('info_block_show_title', 1)) : ?>
               <?php echo Text::_('COM_CONTENT_ARTICLE_INFO'); ?>
           <?php endif; ?>
       </dt>

       <?php if ($displayData['params']->get('show_author') && !empty($displayData['item']->author)) : ?>
           <?php echo $this->sublayout('author', $displayData); ?>
       <?php endif; ?>

       <?php if ($displayData['params']->get('show_parent_category') && !empty($displayData['item']->parent_id)) : ?>
           <?php echo $this->sublayout('parent_category', $displayData); ?>
       <?php endif; ?>

       <?php if ($displayData['params']->get('show_category')) : ?>
           <?php echo $this->sublayout('category', $displayData); ?>
       <?php endif; ?>

       <?php if ($displayData['params']->get('show_associations')) : ?>
           <?php echo $this->sublayout('associations', $displayData); ?>
       <?php endif; ?>

       <?php $displayData['astroidArticle']->renderReadTime(); ?>

       <?php if ($displayData['params']->get('show_publish_date')) : ?>
           <?php echo $this->sublayout('publish_date', $displayData); ?>
       <?php endif; ?>
   <?php endif; ?>

   <?php
   if (
       $displayData['position'] === 'above' && ($blockPosition == 0)
       || $displayData['position'] === 'below' && ($blockPosition == 1 || $blockPosition == 2)
   ) : ?>
      <?php if ($displayData['params']->get('show_create_date')) : ?>
         <?php echo $this->sublayout('create_date', $displayData); ?>
      <?php endif; ?>

      <?php if ($displayData['params']->get('show_modify_date')) : ?>
         <?php echo $this->sublayout('modify_date', $displayData); ?>
      <?php endif; ?>

      <?php if ($displayData['params']->get('show_hits')) : ?>
         <?php echo $this->sublayout('hits', $displayData); ?>
      <?php endif; ?>
   <?php endif; ?>
</dl>