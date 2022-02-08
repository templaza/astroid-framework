<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('JPATH_BASE') or die;


use Joomla\CMS\HTML\HTMLHelper;

?>
<dd class="createdby" itemprop="author" itemscope itemtype="https://schema.org/Person">
	<?php $author = ($displayData['item']->created_by_alias ?: $displayData['item']->author); ?>
	<?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
	<?php if (!empty($displayData['item']->contact_link) && $displayData['params']->get('link_author') == true) : ?>
		<i class="far fa-user"></i>
		<?php echo HTMLHelper::_('link', $displayData['item']->contact_link, $author, array('itemprop' => 'url')); ?>
	<?php else : ?>
		<i class="far fa-user"></i>
		<?php echo $author; ?>
	<?php endif; ?>
</dd>