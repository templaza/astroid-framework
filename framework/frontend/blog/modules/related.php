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
jimport('astroid.framework.article');

extract($displayData);
if (empty($items)) {
   return;
}
?>
<div class="relatedposts-wrap">
   <h4><?php echo JText::_('ASTROID_ARTICLE_RELATED_LBL'); ?></h4>
   <div class="relateditems row">
      <?php foreach ($items as $item) : $images = json_decode($item->images);
         $astroidArticle = new AstroidFrameworkArticle($item, true);
      ?>
         <div class="col-md-6 p-2">
            <div class="card h-100 mb-4">
               <?php
               if (!empty($images->image_intro)) {
               ?>
                  <a href="<?php echo $item->route; ?>">
                     <img class="card-img-top" src="<?php echo $images->image_intro; ?>" data-holder-rendered="true">
                  </a>
               <?php } ?>
               <div class="card-body">
                  <?php
                  if ($display_posttypeicon) {
                     Astroid\Framework::getDocument()->include('blog.modules.posttype', ['article' => $astroidArticle]);
                  }
                  if ($display_badge) {
                     Astroid\Framework::getDocument()->include('blog.modules.badge', ['article' => $astroidArticle]);
                  }
                  ?>
                  <small class="text-muted"> <?php echo $item->category_title; ?></small>
                  <h3 class="related-article-title">
                     <a href="<?php echo $item->route; ?>"><?php echo $item->title; ?></a>
                  </h3>
                  <?php echo $item->introtext; ?>
               </div>
            </div>
         </div>
      <?php endforeach; ?>
   </div>
</div>