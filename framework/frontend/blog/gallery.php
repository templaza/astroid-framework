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

$params = $article->params;
$items = $params->get('astroid_article_gallery_items', []);
if (empty($items)) {
   return;
}

$index = 0;
$active = true;
$width = $params->get('astroid_article_gallery_width', '');
$width = !empty($width) ? 'max-width:' . $width : '';
$hasItems = false;
?>
<div style="<?php echo $width; ?>" id="article-gallery" class="article-gallery carousel mb-3 slide" data-ride="carousel">
   <?php if (!empty($params->get('astroid_article_gallery_bullets', 1))) { ?>
      <ol class="carousel-indicators">
         <?php foreach ($items as $item) { ?>
            <?php
            if (empty($item['image'])) {
               continue;
            }
            ?>
            <li data-target="#article-gallery" data-slide-to="<?php echo $index; ?>" class="<?php echo $active ? 'active' : ''; ?>"></li>
         <?php
            $index++;
            $active = false;
         }
         $active = true;
         ?>
      </ol>
   <?php } ?>
   <div class="carousel-inner">
      <?php foreach ($items as $item) { ?>
         <?php
         if (empty($item['image'])) {
            continue;
         }
         $hasItems = true;
         ?>
         <div class="carousel-item<?php echo $active ? ' active' : ''; ?>">
            <?php if (!empty($item['image'])) { ?>
               <img class="d-block w-100" src="<?php echo JURI::root() . $item['image']; ?>" alt="<?php echo empty($item['title']) ? '' : $item['title']; ?>">
            <?php } ?>
            <?php if (!empty($item['title']) || !empty($item['description'])) { ?>
               <div class="carousel-caption d-none d-md-block">
                  <?php if (!empty($item['title'])) { ?>
                     <h5><?php echo $item['title']; ?></h5>
                  <?php } ?>
                  <?php if (!empty($item['description'])) { ?>
                     <p><?php echo $item['description']; ?></p>
                  <?php } ?>
               </div>
            <?php } ?>
         </div>
      <?php
         $active = false;
      }
      ?>
   </div>
   <?php if ($hasItems && !empty($params->get('astroid_article_gallery_navigation', 1))) { ?>
      <a class="carousel-control-prev" href="#article-gallery" role="button" data-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true"></span>
         <span class="sr-only"><?php echo JText::_('JPREVIOUS'); ?></span>
      </a>
      <a class="carousel-control-next" href="#article-gallery" role="button" data-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true"></span>
         <span class="sr-only"><?php echo JText::_('JNEXT'); ?></span>
      </a>
   <?php } ?>
</div>