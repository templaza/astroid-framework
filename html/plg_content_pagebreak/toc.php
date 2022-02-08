<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;
?>
<div class="float-right article-index">

	<?php if ($headingtext) : ?>
		<h3><?php echo $headingtext; ?></h3>
	<?php endif; ?>

	<ul class="nav-tabs nav-stacked">
		<?php foreach ($list as $listItem) : ?>
			<?php $class = $listItem->liClass ? ' class="' . $listItem->liClass . '"' : ''; ?>
			<li<?php echo $class; ?>>
				<a href="<?php echo $listItem->link; ?>" class="<?php echo $listItem->class; ?>">
					<?php echo $listItem->title; ?>
				</a>
				</li>
			<?php endforeach; ?>
	</ul>
</div>