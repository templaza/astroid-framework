<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;
use Astroid\Framework;
use Astroid\Article;

// Astroid Article/Blog
if (!isset($astroidArticle)) {
    $astroidArticle = new Article($this->item, true);
}

$template = Framework::getTemplate();
$document = Framework::getDocument();

$is_lead    = $this->item->is_leaditem ?? false;
$is_intro   = $this->item->is_introitem ?? false;

// Create shortcuts to some parameters.
$params = $this->item->params;
$canEdit = $params->get('access-edit');
$info = $params->get('info_block_position', 0);
$images = json_decode($this->item->images);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$tpl_params = $template->getParams();

// Post Format
$post_attribs = new Registry(json_decode($this->item->attribs));
$post_format = $post_attribs->get('post_format', 'standard');
$astroid_article_type = $post_attribs->get('astroid_article_type', '');
$astroid_article_video_type = $post_attribs->get('astroid_article_video_type', '');

// Image position
$image_width = array();
if ($is_lead) {
    $image_width            =   Article::getImageWidth($params, 'lead', $this->item->key_idx);
    $image_position         =   $image_width['position'];
} elseif ($is_intro) {
    $image_width            =   Article::getImageWidth($params, 'intro', $this->item->key_idx);
    $image_position         =   $image_width['position'];
} else {
    $image_position         =   'top';
}

if (empty($image_position)) {
    $image_position    =   'top';
}

$image_width_cls    =   '';
if ($image_position == 'left' || $image_position == 'right') {
    $image_width_cls    =   $image_width['xl'] . $image_width['lg'] . $image_width['md'] . $image_width['sm'] . $image_width['default'];
}

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

$clsItemContainer   = $astroidArticle->getStyle('container');
$clsItemBody        = $astroidArticle->getStyle('body');

//Blog Layout
$blog_layout = $params->get('as_blog_layout', '');
$as_overlay_hover = $params->get('as_overlay_hover', 0);
$overlay_content_position = $params->get('as_overlay_content_position', 'justify-content-end');
$category_show_intro = $params->get('category_show_intro', 1);
$category_hide_extrafields = $params->get('category_hide_extrafields', 0);
$clsItemContainer .= $blog_layout == 'overlay' ? ' as-blog-overlay ' . $overlay_content_position . ($as_overlay_hover == 1 ? ' as-overlay-hover' : '') : '';
$clsItemContainer .= $category_hide_extrafields ? ' as-hide-extrafields' : '';
$clsItemContainer .= ($image_position == 'left' || $image_position == 'right') ? ' border-top media-'.$image_position : '';
$clsItemBody .= $tpl_params->get('show_post_format') ? ' has-post-format' : '';
$clsItemBody .= $blog_layout == 'overlay' ? ' card-img-overlay as-light ' . $overlay_content_position : '';
?>
<div class="item-content item-media-<?php echo $image_position; ?> post-<?php echo $astroid_article_type; ?> position-relative<?php echo (!empty($clsItemContainer) ? ' '.$clsItemContainer : '') . ($image_position == 'bottom' ? ' d-flex flex-column-reverse' : ''); ?>">
    <?php if ($isUnpublished) : ?>
    <div class="system-unpublished">
        <?php endif; ?>
        <?php
        if ($astroid_article_type == 'video' && $astroid_article_video_type == 'local') {
            $astroid_article_video_local = $post_attribs->get('astroid_article_video_local', '');
            $image = '<div class="as-article-video-local h-100 ratio ratio-16x9" data-as-video-bg="'.Uri::base('true').'/images/'.$astroid_article_video_local.'"'.(!empty($images->image_intro) ? ' data-as-video-poster="'.Uri::base('true').'/'.$images->image_intro.'"' : '').'></div>';
        } else {
            $image = $astroidArticle->getImage();
        }
        if (((!empty($images->image_intro)) && $post_format == 'standard') || (is_string($image) && !empty($image))) {
            if (($image_position == 'left' || $image_position == 'right' || $image_position == 'bottom') && $blog_layout!=='overlay') {
                if (($image_position == 'left' || $image_position == 'right')) {
                    echo '<div class="row g-0 position-relative">';
                    echo '<div class="astroid-media-'.$image_position. ' astroid-img-cover position-relative' .$image_width_cls.'">';
                } else {
                    echo '<div class="astroid-media-'.$image_position.' mt-4">';
                }
            }
        }
        // Generate media
        if ((!empty($images->image_intro)) && $post_format == 'standard' && ($astroid_article_type !== 'video' || $astroid_article_video_type !== 'local')) {
            echo LayoutHelper::render('joomla.content.intro_image', $this->item);
        } else if (is_string($image) && !empty($image)) {
            echo '<div class="item-image">';
            if ($astroid_article_type == 'video' && $astroid_article_video_type == 'local') {
                $document->loadVideoBG();
                echo $image;
            } else {
                $document->include('blog.modules.image', ['image' => $image, 'title' => $this->item->title, 'item' => $this->item]);
            }
            echo '</div>';
        } else {
            echo LayoutHelper::render('joomla.content.post_formats.post_' . $post_format, array('params' => $post_attribs, 'item' => $this->item));
        }

        if (((!empty($images->image_intro)) && $post_format == 'standard') || (is_string($image) && !empty($image))) {
            if (($image_position == 'left' || $image_position == 'right' || $image_position == 'bottom') && $blog_layout!=='overlay') {
                echo '</div>';
                echo ($image_position == 'left' || $image_position == 'right') ? '<div class="astroid-content-media-'.$image_position.' col'.$image_width['expand'].'">' : '';
            }
        }
        ?>
        <div class="d-flex flex-column<?php
        echo (!empty($image) ? ' has-image' : '');
        echo (!empty($clsItemBody) ? ' '.$clsItemBody : ''); ?>">
            <?php $astroidArticle->renderPostTypeIcon(); ?>
            <?php $astroidArticle->renderArticleBadge(); ?>
            <?php if ($canEdit) : ?>
                <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
            <?php endif; ?>

            <?php // @todo Not that elegant would be nice to group the params ?>
            <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
                || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam); ?>

            <div class="article-title item-title">
                <?php echo LayoutHelper::render('joomla.content.blog_style_default_item_title', $this->item); ?>
            </div>
            <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
                <?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'above')); ?>
            <?php endif; ?>
            <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
            <?php endif; ?>

            <?php if (!$params->get('show_intro')) : ?>
                <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
                <?php echo $this->item->event->afterDisplayTitle; ?>
            <?php endif; ?>

            <?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
            <?php echo $this->item->event->beforeDisplayContent; ?>
            <?php if ($category_show_intro) : ?>
            <div class="article-intro-text">
                <?php echo $this->item->introtext; ?>
            </div>
            <?php endif; ?>
            <?php if ($info == 1 || $info == 2) : ?>
                <?php if ($useDefList) : ?>
                    <?php echo LayoutHelper::render('joomla.content.info_block', array('item' => $this->item, 'params' => $params, 'astroidArticle' => $astroidArticle, 'position' => 'below')); ?>
                <?php endif; ?>
                <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                    <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($params->get('show_readmore') && $this->item->readmore) :
                // Link
                if ($params->get('access-view')) :
                    $link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
                else :
                    $menu = Factory::getApplication()->getMenu();
                    $active = $menu->getActive();
                    $itemId = $active->id;
                    $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
                    $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
                endif; ?>
                <?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $this->item, 'params' => $params, 'link' => $link]); ?>
            <?php endif; ?>
            <?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
            <?php echo $this->item->event->afterDisplayContent; ?>
        </div>
        <?php
        if (((!empty($images->image_intro)) && $post_format == 'standard') || (is_string($image) && !empty($image))) {
            if (($image_position == 'left' || $image_position == 'right') && $blog_layout!=='overlay') {
                echo '</div>';
                echo '</div>';
            }
        }
        ?>
        <?php if ($isUnpublished) : ?>
    </div>
<?php endif; ?>
</div>