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
$heading = $params->get('astroid_article_review_heading', '');
$summery = $params->get('astroid_article_review_summery', '');
$good_things = $params->get('astroid_article_review_good', '');
$bad_things = $params->get('astroid_article_review_bad', '');
$overall_rating = $params->get('astroid_article_review_rating', 0);
$button_text = $params->get('astroid_article_button_action', '');
$button_link = $params->get('astroid_article_button_link', '');
$criterias = $params->get('astroid_article_review_criterias', []);

if (empty($overall_rating)) {
   $score = 0;
   $items = 0;
   foreach ($criterias as $criteria) {
      $score += $criteria['score'];
      $items++;
   }
   $overall_rating = $score / $items;
}
?>
<div class="article-review">
   <div class="review-head row">
      <div class="review-total-score col-auto">
         <div class="review-total-score-wrapper">
            <span class="score-value"><?php echo $overall_rating; ?></span>
            <span class="score-label"><?php echo JText::_('ASTROID_ARTICLE_TOTAL_SCORE'); ?></span>
         </div>
      </div>
      <div class="review-content col">
         <?php if (!empty($heading)) { ?>
            <h3 class="review-heading"><?php echo $heading; ?></h3>
         <?php } ?>
         <?php if (!empty($summery)) { ?>
            <p class="review-summary"><?php echo $summery; ?></p>
         <?php } ?>
      </div>
   </div>
   <div class="review-criterias">
      <?php
      foreach ($criterias as $criteria) {
         ?>
         <div class="review-criteria">
            <div class="review-criteria-title">
               <p class="review-criteria-label"><?php echo $criteria['title']; ?></p>
               <p class="review-criteria-rating"><?php echo $criteria['score']; ?>/10</p>
            </div>
            <div class="review-criteria-progress progress">
               <div class="progress-bar progress-bar-viewport-animation" data-value="<?php echo $criteria['score'] * 10; ?>" role="progressbar" style="width: 0%" aria-valuenow="<?php echo $criteria['score']; ?>" aria-valuemin="0" aria-valuemax="10"></div>
            </div>
         </div>
         <?php
      }
      ?>
   </div>
   <div class="review-good-bad row">
      <div class="col-md-6">
         <h4 class="pros-heading text-success"><?php echo JText::_('ASTROID_ARTICLE_PROS_LBL'); ?></h4>
         <ul class="pros-cons-list pros-list">
            <?php
            foreach (explode("\n", $good_things) as $good_thing) {
               ?>
               <li><i class="fas fa-check text-success"></i> <?php echo $good_thing; ?></li>
            <?php } ?>
         </ul>
      </div>
      <div class="col-md-6">
         <h4 class="cons-heading text-danger"><?php echo JText::_('ASTROID_ARTICLE_CONS_LBL'); ?></h4>
         <ul class="pros-cons-list cons-list">
            <?php
            foreach (explode("\n", $bad_things) as $bad_thing) {
               ?>
               <li><i class="fas fa-minus text-danger"></i> <?php echo $bad_thing; ?></li>
            <?php } ?>
         </ul>
      </div>
   </div>
   <?php if (!empty($button_text) && !empty($button_link)) { ?>
      <div class="row btn-prosandcons-wrapper">
         <div class="col">
            <a href="<?php echo $button_link; ?>" class="btn btn-prosandcons"><?php echo $button_text; ?></a>
         </div>
      </div>
   <?php } ?>
</div>