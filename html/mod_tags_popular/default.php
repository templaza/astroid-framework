<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

if (ASTROID_JOOMLA_VERSION > 3) {
	\JLoader::registerAlias('TagsHelperRoute', 'Joomla\Component\Tags\Site\Helper\RouteHelper');
} else {
	include_once(JPATH_BASE . '/components/com_tags/helpers/route.php');
}
?>

<div class="tagspopular">
	<?php if (!count($list)) : ?>
		<div class="alert alert-no-items"><?php echo JText::_('MOD_TAGS_POPULAR_NO_ITEMS_FOUND'); ?></div>
	<?php else : ?>
		<?php foreach ($list as $item) : ?>
			<a class="badge badge-light" href="<?php echo JRoute::_(TagsHelperRoute::getTagRoute($item->tag_id . ':' . $item->alias)); ?>" role="button"><?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'); ?>
				<?php if ($display_count) : ?>
					<span class="tag-count badge badge-info"><?php echo $item->count; ?></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>