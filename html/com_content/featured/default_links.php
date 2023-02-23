<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

if (ASTROID_JOOMLA_VERSION > 3) {
	\JLoader::registerAlias('ContentHelperRoute', 'Joomla\Component\Content\Site\Helper\RouteHelper');
} else {
	include_once(JPATH_COMPONENT . '/helpers/route.php');
}

?>
<ul class="list-group">
	<?php foreach ($this->link_items as &$item) : ?>
		<li class="list-group-item">
			<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
				<?php echo $item->title; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>