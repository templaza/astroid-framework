<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="d-flex justify-content-center">
	<div class="col-lg-5 col-md-10 <?php echo $this->pageclass_sfx ?>">
		<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		<?php endif; ?>
		<form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
			<!-- Iterate through the form fieldsets and display each one. -->
			<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
				<?php $fields = $this->form->getFieldset($fieldset->name); ?>
				<?php if (count($fields)) : ?>
					<!-- Iterate through the fields in the set and display them. -->
					<?php foreach ($fields as $field) : ?>
						<!-- If the field is hidden, just display the input. -->
						<?php if ($field->hidden) : ?>
							<?php echo $field->input; ?>
						<?php else : ?>
							<div class="form-group">
								<?php echo $field->label; ?>
								<?php if (!$field->required && $field->type != 'Spacer') : ?>
									<span class="optional">
										<?php echo Text::_('COM_USERS_OPTIONAL'); ?>
									</span>
								<?php endif; ?>
								<div class="group-control">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<div class="d-flex justify-content-between align-items-center">
				<div class="d-flex justify-content-start">
					<button type="submit" class="btn btn-primary validate">
						<?php echo Text::_('JREGISTER'); ?>
					</button>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="registration.register" />
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
				<div class="form-check d-flex justify-content-end">
					<a href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>"><?php echo Text::_('TPL_HAVE_AN_ACCOUNT'); ?></a>
				</div>
			</div>
		</form>
	</div>
</div>