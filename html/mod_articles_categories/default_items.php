<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_categories
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

if (ASTROID_JOOMLA_VERSION > 3) {
	\JLoader::registerAlias('ContentHelperRoute', 'Joomla\Component\Content\Site\Helper\RouteHelper');
} else {
	include_once(JPATH_COMPONENT . '/helpers/route.php');
}

$input  = Factory::getApplication()->input;
$option = $input->getCmd('option');
$view   = $input->getCmd('view');
$id     = $input->getInt('id');

foreach ($list as $item) : ?>
	<?php
	$classes = ['list-group-item', 'border-0', 'py-0', 'px-0'];
	?>
	<?php if ($id == $item->id && $view == 'category' && $option == 'com_content') $classes[] = "active"; ?>
	<li class="<?php echo implode(' ', $classes) ?>"> <?php $levelup = $item->level - $startLevel - 1; ?>

		<h<?php echo $params->get('item_heading') + $levelup; ?>>
			<a href="<?php echo Route::_(ContentHelperRoute::getCategoryRoute($item->id)); ?>">
				<?php echo $item->title; ?>
				<?php if ($params->get('numitems')) : ?>
					(<?php echo $item->numitems; ?>)
				<?php endif; ?>
			</a>
		</h<?php echo $params->get('item_heading') + $levelup; ?>>

		<?php if ($params->get('show_description', 0)) : ?>
			<?php echo HTMLHelper::_('content.prepare', $item->description, $item->getParams(), 'mod_articles_categories.content'); ?>
		<?php endif; ?>
		<?php if (
			$params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0)
				|| ($params->get('maxlevel') >= ($item->level - $startLevel)))
			&& count($item->getChildren())
		) : ?>
			<?php echo '<ul class="list-group list-group-flush">'; ?>
			<?php $temp = $list; ?>
			<?php $list = $item->getChildren(); ?>
			<?php require ModuleHelper::getLayoutPath('mod_articles_categories', $params->get('layout', 'default') . '_items'); ?>
			<?php $list = $temp; ?>
			<?php echo '</ul>'; ?>
		<?php endif; ?>
	</li>
<?php endforeach; ?>