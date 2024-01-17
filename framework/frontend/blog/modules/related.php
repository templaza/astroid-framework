<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/blog/ folder to create and override
 */
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
// No direct access.
defined('_JEXEC') or die;

extract($displayData);
if (empty($items)) {
   return;
}
$document = Astroid\Framework::getDocument();
?>
<div class="relatedposts-wrap">
   <h4><?php echo Text::_('ASTROID_ARTICLE_RELATED_LBL'); ?></h4>
   <div class="relateditems row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
      <?php foreach ($items as $item) : $images = json_decode($item->images);
         $astroidArticle = new Astroid\Article($item, true);
      ?>
         <div>
            <div class="card">
               <?php
               if (!empty($images->image_intro)) {
               ?>
                  <a href="<?php echo $item->route; ?>">
                     <img class="card-img-top" src="<?php echo $images->image_intro; ?>" data-holder-rendered="true">
                  </a>
               <?php } ?>
               <div class="card-body">
                  <h3 class="related-article-title">
                     <a href="<?php echo $item->route; ?>"><?php echo $item->title; ?></a>
                  </h3>
                  <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $item, 'params' => $item->params, 'astroidArticle' => $astroidArticle, 'position' => 'above']); ?>
                   <?php
                   if ($display_posttypeicon) {
                       $document->include('blog.modules.posttype', ['article' => $astroidArticle]);
                   }
                   if ($display_badge) {
                       $document->include('blog.modules.badge', ['article' => $astroidArticle]);
                   }
                   ?>
               </div>
            </div>
         </div>
      <?php endforeach; ?>
   </div>
</div>