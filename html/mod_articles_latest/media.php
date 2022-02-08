<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

?>
<div class="latestnews view-media">
   <ul class="list-group">
      <?php foreach ($list as $item) : $image = json_decode($item->images); ?>
         <li itemscope itemtype="https://schema.org/Article">
            <?php if ($image->image_intro != "") : ?>
               <a class="article-media" href="<?php echo $item->link; ?>" itemprop="url">
                  <img src="<?php echo Uri::root() . $image->image_intro; ?>" alt="<?php echo htmlspecialchars($image->image_fulltext_alt); ?>">
               </a>
            <?php endif; ?>
            <a class="article-title" href="<?php echo $item->link; ?>" itemprop="url">
               <span itemprop="name">
                  <?php echo $item->title; ?>
               </span>
            </a>
         </li>
      <?php endforeach; ?>
   </ul>
</div>