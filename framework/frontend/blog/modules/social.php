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

$params = Astroid\Framework::getTemplate()->getParams();
$type = $params->get('article_socialshare_type', 'none');
if ($type == 'none') {
   return;
}
// Addthis Social Share Start 
if ($type == 'addthis') {
	$article_socialshare_addthis = $params->get('article_socialshare_addthis', ''); ?>
		<?php if(!empty($article_socialshare_addthis)){ ?>
			<div class="astroid-socialshare">
				<?php echo $article_socialshare_addthis; ?>
			</div>
		<?php } ?>
	<?php
}

// Sharethis Social Share Start 
if ($type == 'sharethis') {
	$article_socialshare_sharethis = $params->get('article_socialshare_sharethis', ''); ?>
	<?php if(!empty($article_socialshare_sharethis)){?>
		<?php $doc = JFactory::getDocument(); $doc->addScript('//platform-api.sharethis.com/js/sharethis.js#property='.$article_socialshare_sharethis.'&product=inline-share-buttons'); ?>
			<div class="astroid-socialshare">
				<div class="sharethis-inline-share-buttons"></div>
			</div>
	<?php } ?>
<?php } ?>