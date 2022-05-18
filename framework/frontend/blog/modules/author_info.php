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

// Get User Details
$user = JFactory::getUser($article->created_by);
$params = new JRegistry();
$params->loadString($user->params, 'JSON');
// Get social profiles
$socials = $params->get('astroid_author_social', '[]');

if (is_string($socials)) {
   $socials = \json_decode($socials, true);
} else {
   $items = [];
   foreach ($socials as $social) {
      $items['icon'][] = $social->icon;
      $items['link'][] = $social->link;
   }
   $socials = $items;
}

$hash_email = md5(strtolower(trim($user->email)));
?>

<div class="author-wrap">
   <div class="author-body">
      <div class="author-header">
         <?php if (!empty($params->get('astroid_author_picture', 0))) { ?>
            <div class="author-thumb">
               <?php if ($params->get('astroid_author_picture', 'gravatar') == "upload") { ?>
                  <?php if (!empty($params->get('upload', ''))) { ?>
                     <img width="100" src="<?php echo JURI::base() . $params->get('upload', ''); ?>" alt="<?php echo $user->name."'s Avatar"; ?>" />
                  <?php } else { ?>
                     <img width="100" src="<?php echo JURI::base(); ?>images/<?php echo $template->template; ?>/user-avatar.png" alt="<?php echo $user->name."'s Avatar"; ?>" />
                  <?php } ?>
               <?php } ?>
               <?php if ($params->get('astroid_author_picture', '') == "gravatar") { ?>
                  <img src="https://www.gravatar.com/avatar/<?php echo $hash_email; ?>" alt="<?php echo $user->name."'s Avatar"; ?>" />
               <?php } ?>
            </div>
         <?php } ?>
         <div class="author-info">
            <h3 class="author-name"><?php echo $user->name; ?></h3>
            <?php if (!empty($socials)) { ?>
               <ul class="author-social-links">
                  <?php foreach ($socials['icon'] as $key => $icon) { ?>
                     <?php if (empty($socials['link'][$key])) continue; ?>
                     <li class="author-social-link">
                        <a target="_blank" rel="noopener" href="<?php echo $socials['link'][$key]; ?>"><i class="<?php echo $icon; ?> fa-lg" aria-hidden="true"></i><span class="visually-hidden"><?php echo $icon; ?></span></a>
                     </li>
                  <?php } ?>
               </ul>
            <?php } ?>
         </div>
      </div>
      <?php if (!empty($params->get('astroid_author_aboutme', ''))) { ?>
         <p class="author-description"><?php echo $params->get('astroid_author_aboutme', ''); ?></p>
      <?php } ?>
   </div>
</div>