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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

if (ASTROID_JOOMLA_VERSION > 3) {
   \JLoader::registerAlias('ContentHelperRoute', 'Joomla\Component\Content\Site\Helper\RouteHelper');
} else {
   include_once(JPATH_COMPONENT . '/helpers/route.php');
}

// Astroid Article/Blog
$astroidArticle = new AstroidFrameworkArticle($this->item, true);

$template = Astroid\Framework::getTemplate();
$document = Astroid\Framework::getDocument();

// Create shortcuts to some parameters.
$params = $this->item->params;
$canEdit = $params->get('access-edit');
$info = $params->get('info_block_position', 0);
$images = json_decode($this->item->images);

$tpl_params = $template->getParams();
$useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date') || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') ||  $tpl_params->get('astroid_readtime', 1));

// Post Format
$post_attribs = new Registry(json_decode($this->item->attribs));
$post_format = $post_attribs->get('post_format', 'standard');

$info_block_layout = ASTROID_JOOMLA_VERSION > 3 ? 'joomla.content.info_block' : 'joomla.content.info_block.block';

$isUnpublished = $this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(Factory::getDate()) || ((strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate());

?>
<?php if ($isUnpublished) : ?>
   <div class="system-unpublished">
   <?php endif; ?>
   <?php
   $image = $astroidArticle->getImage();
   if ((!empty($images->image_intro)) && $post_format == 'standard') {
      echo LayoutHelper::render('joomla.content.intro_image', $this->item);
   } else if (is_string($image) && !empty($image)) {
      $document->include('blog.modules.image', ['image' => $image, 'title' => $this->item->title, 'item' => $this->item]);
   } else {
      echo LayoutHelper::render('joomla.content.post_formats.post_' . $post_format, array('params' => $post_attribs, 'item' => $this->item));
   }
   ?>
   <div class="card-body<?php echo $tpl_params->get('show_post_format') ? ' has-post-format' : ''; ?><?php echo (!empty($image) ? ' has-image' : ''); ?>">

      <?php $astroidArticle->renderArticleBadge(); ?>

      <?php echo LayoutHelper::render('joomla.content.post_formats.icons', $post_format); ?>

      <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
         <?php echo LayoutHelper::render($info_block_layout, array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'above')); ?>
      <?php endif; ?>

      <div class="article-title item-title">
         <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
      </div>

      <div class="article-intro-text">
         <?php echo $this->item->introtext; ?>
      </div>

      <?php if ($info == 1 || $info == 2) : ?>
         <?php if ($useDefList) : ?>
            <?php echo LayoutHelper::render($info_block_layout, array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'below')); ?>
         <?php endif; ?>
      <?php endif; ?>



      <?php if (!$params->get('show_intro')) : ?>
         <?php echo $this->item->event->afterDisplayTitle; ?>
      <?php endif; ?>

      <?php echo $this->item->event->beforeDisplayContent; ?>

      <?php
      if ($params->get('show_readmore') && $this->item->readmore) :
         if ($params->get('access-view')) :
            $link = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
         else :
            $menu = Factory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active->id;
            $link1 = Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
            $returnURL = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
            $link = new Uri($link1);
            $link->setVar('return', base64_encode($returnURL));
         endif;
      ?>
         <?php echo LayoutHelper::render('joomla.content.readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); ?>
      <?php endif; ?>
   </div>

   <?php if ($isUnpublished) : ?>
   </div>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>