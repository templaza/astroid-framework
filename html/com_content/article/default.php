<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

jimport('astroid.framework.article');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

if (ASTROID_JOOMLA_VERSION > 3) {
   \JLoader::registerAlias('ContentHelperRoute', 'Joomla\Component\Content\Site\Helper\RouteHelper');
} else {
   include_once(JPATH_COMPONENT . '/helpers/route.php');
}

if (ASTROID_JOOMLA_VERSION < 4) {
   JHtml::_('behavior.caption');
}

// Astroid Article/Blog
$astroidArticle = new AstroidFrameworkArticle($this->item);

$template = Astroid\Framework::getTemplate();

// Create shortcuts to some parameters.
$params = $this->item->params;
$urls = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user = Factory::getUser();
$info = $params->get('info_block_position', 0);
$images = json_decode($this->item->images);

$url = Route::_(ContentHelperRoute::getArticleRoute($this->item->id . ':' . $this->item->alias, $this->item->catid, $this->item->language));
$root = Uri::base();
$root = new Uri($root);
$url = $root->getScheme() . '://' . $root->getHost() . $url;

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$info_block_layout = ASTROID_JOOMLA_VERSION > 3 ? 'joomla.content.info_block' : 'joomla.content.info_block.block';

?>
<div class="com-content-article item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
   <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getConfig()->get('language') : $this->item->language; ?>" />
   <?php if ($this->params->get('show_page_heading')) : ?>
      <div class="item-title">
         <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
      </div>
   <?php
   endif;
   if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
      echo $this->item->pagination;
   }
   ?>

   <?php // Todo Not that elegant would be nice to group the params 
   ?>
   <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam || $template->params->get('astroid_readtime', 1));
   ?>

   <?php if (!$useDefList && $this->print) : ?>
      <div id="pop-print" class="btn hidden-print">
         <?php echo HTMLHelper::_('icon.print_screen', $this->item, $params); ?>
      </div>
      <div class="clearfix"> </div>
   <?php endif; ?>
   <?php $astroidArticle->render('above-title'); ?>
   <?php if (($params->get('show_title') || $params->get('show_author'))) : ?>
      <div class="item-title">
         <?php if ($params->get('show_title')) : ?>
            <h2 itemprop="headline">
               <?php echo $this->escape($this->item->title); ?>
            </h2>
         <?php endif; ?>
         <?php if ($this->item->state == 0) : ?>
            <span class="badge badge-warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
         <?php endif; ?>
         <?php if (strtotime($this->item->publish_up) > strtotime(Factory::getDate())) : ?>
            <span class="badge badge-warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
         <?php endif; ?>
         <?php if (ASTROID_JOOMLA_VERSION == 3 && ((strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate())) : ?>
            <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
         <?php endif; ?>
         <?php if (ASTROID_JOOMLA_VERSION == 4 && (!is_null($this->item->publish_down) && (strtotime($this->item->publish_down) < strtotime(Factory::getDate())))) : ?>
            <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
         <?php endif; ?>
      </div>
   <?php endif; ?>
   <?php if (!$this->print) : ?>
      <?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
         <?php echo LayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
      <?php endif; ?>
   <?php else : ?>
      <?php if ($useDefList) : ?>
         <div id="pop-print" class="btn hidden-print">
            <?php echo HTMLHelper::_('icon.print_screen', $this->item, $params); ?>
         </div>
      <?php endif; ?>
   <?php endif; ?>

   <?php // Content is generated by content plugin event "onContentAfterTitle"  
   ?>
   <?php echo $this->item->event->afterDisplayTitle; ?>

   <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
      <?php echo LayoutHelper::render($info_block_layout, array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'above')); ?>
   <?php endif; ?>

   <?php // Content is generated by content plugin event "onContentBeforeDisplay" 
   ?>
   <?php echo $this->item->event->beforeDisplayContent; ?>

   <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))) :
   ?>
      <?php echo $this->loadTemplate('links'); ?>
   <?php endif; ?>
   <?php if ($params->get('access-view')) : ?>
      <?php
      if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) :
         echo $this->item->pagination;
      endif;
      ?>
      <?php
      if (isset($this->item->toc)) :
         echo $this->item->toc;
      endif;
      ?>
      <?php echo LayoutHelper::render('joomla.content.full_image', $this->item); ?>
      <?php $astroidArticle->render('before-content'); ?>
      <div itemprop="articleBody">
         <?php echo $this->item->text; ?>
      </div>
      <?php $astroidArticle->render('after-content'); ?>

      <?php if ($info == 1 || $info == 2) : ?>
         <?php if ($useDefList) : ?>
            <?php echo LayoutHelper::render($info_block_layout, array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'below')); ?>
         <?php endif; ?>
      <?php endif; ?>

      <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
         <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
         <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
      <?php endif; ?>
      <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
         <?php echo $this->loadTemplate('links'); ?>
      <?php endif; ?>
      <?php // Content is generated by content plugin event "onContentAfterDisplay" 
      ?>
      <?php echo $this->item->event->afterDisplayContent; ?>
      <?php
      if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) :
         echo $this->item->pagination;
      ?>
      <?php endif; ?>
      <?php // Optional teaser intro text for guests 
      ?>
   <?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
      <?php echo LayoutHelper::render('joomla.content.intro_image', $this->item); ?>
      <?php echo HTMLHelper::_('content.prepare', $this->item->introtext); ?>
      <?php // Optional link to let them register to see the whole article. 
      ?>
      <?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
         <?php $menu = Factory::getApplication()->getMenu(); ?>
         <?php $active = $menu->getActive(); ?>
         <?php $itemId = $active->id; ?>
         <?php $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
         <?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
         <div class="readmore">
            <a href="<?php echo $link; ?>" class="register">
               <?php $attribs = json_decode($this->item->attribs); ?>
               <?php
               if ($attribs->alternative_readmore == null) :
                  echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
               elseif ($readmore = $attribs->alternative_readmore) :
                  echo $readmore;
                  if ($params->get('show_readmore_title', 0) != 0) :
                     echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
                  endif;
               elseif ($params->get('show_readmore_title', 0) == 0) :
                  echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE');
               else :
                  echo Text::_('COM_CONTENT_READ_MORE');
                  echo HTMLHelper::_('string.truncate', $this->item->title, $params->get('readmore_limit'));
               endif;
               ?>
            </a>
         </div>
      <?php endif; ?>
   <?php endif; ?>
   <?php
   if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) :
      echo $this->item->pagination;
   ?>
   <?php endif; ?>
   <?php $astroidArticle->renderSocialShare(); ?>
   <?php $astroidArticle->renderAuthorInfo(); ?>
   <?php $astroidArticle->renderComments(); ?>
   <?php $astroidArticle->renderRelatedPosts(); ?>
</div>