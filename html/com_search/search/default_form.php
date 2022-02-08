<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.tooltip');

$lang = Factory::getApplication()->getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

?>
<form id="searchForm" action="<?php echo Route::_('index.php?option=com_search'); ?>" method="post">
	<div class="form-row align-items-center">
		<div class="input-group mb-3">
			<input type="text" class="inputbox form-control" name="searchword" title="<?php echo Text::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" placeholder="<?php echo Text::_('COM_SEARCH_SEARCH_KEYWORD'); ?>" id="search-searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" />
			<div class="input-group-append">
				<button class="btn btn-primary hasTooltip" name="Search" onclick="this.form.submit()" title="<?php echo HTMLHelper::_('tooltipText', 'COM_SEARCH_SEARCH'); ?>">
					<i class="fas fa-search"></i>
					<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
				</button>
			</div>
		</div>
		<input type="hidden" name="task" value="search" />
		<div class="clearfix"></div>
	</div>
	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)) : ?>
			<p>
				<?php echo Text::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', '<span class="badge badge-info">' . $this->total . '</span>'); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php if ($this->params->get('search_phrases', 1)) : ?>
		<fieldset class="phrases">
			<legend>
				<?php echo Text::_('COM_SEARCH_FOR'); ?>
			</legend>
			<div class="phrases-box">
				<?php echo $this->lists['searchphrase']; ?>
			</div>
			<div class="ordering-box">
				<label for="ordering" class="ordering">
					<?php echo Text::_('COM_SEARCH_ORDERING'); ?>
				</label>
				<?php echo $this->lists['ordering']; ?>
			</div>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
			<legend>
				<?php echo Text::_('COM_SEARCH_SEARCH_ONLY'); ?>
			</legend>
			<?php foreach ($this->searchareas['search'] as $val => $txt) : ?>
				<?php $checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : ''; ?>
				<label for="area-<?php echo $val; ?>" class="checkbox">
					<input type="checkbox" name="areas[]" value="<?php echo $val; ?>" id="area-<?php echo $val; ?>" <?php echo $checked; ?> />
					<?php echo Text::_($txt); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->total > 0) : ?>
		<div class="form-limit">
			<label for="limit">
				<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	<?php endif; ?>
</form>