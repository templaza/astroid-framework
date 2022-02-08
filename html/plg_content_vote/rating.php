<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Content.vote
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Layout variables
 * -----------------
 * @var   string   $context  The context of the content being passed to the plugin
 * @var   object   &$row     The article object
 * @var   object   &$params  The article params
 * @var   integer  $page     The 'page' number
 * @var   array    $parts    The context segments
 * @var   string   $path     Path to this file
 */
jimport('astroid.framework.template');
$template = Astroid\Framework::getTemplate();

if (!$template->params->get('article_rating', 1)) {
   if ($context == 'com_content.category' || $context == 'com_content.featured') {
      return;
   }
   $rating = (int) $row->rating;

   // Look for images in template if available
   $starImageOn = '<i class="fas fa-star mr-1"></i>';
   $starImageOff = '<i class="far fa-star mr-1"></i>';

   $img = '';
   for ($i = 0; $i < $rating; $i++) {
      $img .= $starImageOn;
   }

   for ($i = $rating; $i < 5; $i++) {
      $img .= $starImageOff;
   }
?>
   <div class="content_rating" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
      <p class="unseen element-invisible sr-only">
         <?php echo Text::sprintf('PLG_VOTE_USER_RATING', '<span itemprop="ratingValue">' . $rating . '</span>', '<span itemprop="bestRating">5</span>'); ?>
         <meta itemprop="ratingCount" content="<?php echo (int) $row->rating_count; ?>" />
         <meta itemprop="worstRating" content="0" />
      </p>
      <p class="mb-2">
         <?php echo $img; ?>
      </p>
   </div>
<?php } else { ?>
   <?php
   if ($context == 'com_content.categories') {
      return;
   }
   $rating = (int) $row->rating;
   ?>
   <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
      <meta itemprop="ratingValue" content="<?php echo $rating; ?>" />
      <meta itemprop="bestRating" content="5" />
      <meta itemprop="ratingCount" content="<?php echo (int) $row->rating_count; ?>" />
      <meta itemprop="worstRating" content="0" />
   </div>
<?php } ?>