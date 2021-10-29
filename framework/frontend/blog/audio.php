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
$source = $params->get('astroid_article_audio_source', 'soundcloud');
$soundcloud = $params->get('astroid_article_audio_soundcloud', '');
$spotify = $params->get('astroid_article_audio_spotify', '');
?>
<?php if ($source == 'soundcloud' && !empty($soundcloud)) { ?>
   <div class="mb-3">
      <?php echo $soundcloud; ?>
   </div>
<?php }
?>
<?php if ($source == 'spotify' && !empty($spotify)) { ?>
   <div class="mb-3">
      <iframe src="https://embed.spotify.com/?uri=<?php echo $spotify; ?>" width="100%" height="80" style="border:0" allowtransparency="true"></iframe>
   </div>
   <?php
}?>