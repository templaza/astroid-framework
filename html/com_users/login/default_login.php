<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="d-flex justify-content-center login-form ">
	<div class="col-lg-5 col-md-10 ">
		<div class="login item-title">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			<?php endif; ?>

			<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
				<div class="login-description text-center">
				<?php endif; ?>

				<?php if ($this->params->get('logindescription_show') == 1) : ?>
					<?php echo $this->params->get('login_description'); ?>
				<?php endif; ?>

				<?php if (($this->params->get('login_image') != '')) : ?>
					<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image mx-auto d-block" alt="<?php echo Text::_('COM_USERS_LOGIN_IMAGE_ALT') ?>" />
				<?php endif; ?>

				<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
				</div>
			<?php endif; ?>

			<form action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate">
				<?php /* Set placeholder for username, password and secretekey */
				$this->form->setFieldAttribute('username', 'hint', Text::_('COM_USERS_LOGIN_USERNAME_LABEL'));
				$this->form->setFieldAttribute('password', 'hint', Text::_('JGLOBAL_PASSWORD'));
				$this->form->setFieldAttribute('secretkey', 'hint', Text::_('JGLOBAL_SECRETKEY'));
				?>
				<div class="form-group">
					<div class="control-label">
						<label id="username-lbl" for="username" class="required">
							<?php echo Text::_('COM_USERS_LOGIN_USERNAME_LABEL'); ?><span class="text-danger">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<input name="username" id="username" value="" class="validate-username required form-control invalid" size="25" required="required" aria-required="true" autofocus="" aria-invalid="true" type="text">
					</div>
				</div>
				<div class="form-group">
					<div class="control-label">
						<label id="password-lbl" for="password" class="required">
							<?php echo Text::_('JGLOBAL_PASSWORD');  ?><span class="text-danger">&nbsp;*</span></label>
					</div>
					<div class="controls">
						<input name="password" id="password" value="" class="validate-password required form-control" size="25" maxlength="99" required="required" aria-required="true" type="password">
					</div>
				</div>
				<?php if ($this->tfa) : ?>
					<div class="form-group">
						<div class="group-control">
							<?php echo $this->form->getField('secretkey')->input; ?>
						</div>
					</div>
				<?php endif; ?>
				<div class="d-flex justify-content-between align-items-center">
					<div class="form-group d-flex justify-content-start">
						<div class="controls">
							<button type="submit" class="btn btn-primary"><?php echo Text::_('JLOGIN'); ?></button>
						</div>
					</div>
					<?php
					if (PluginHelper::isEnabled('system', 'remember')) : ?>
						<div class="form-check form-group d-flex justify-content-end">
							<label>
								<input id="remember" type="checkbox" name="remember" class="checkbox" value="yes">
								<?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME') ?>
							</label>
						</div>
					<?php endif; ?>
				</div>
				<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
				<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
		<div class="form-links">
			<ul class="list-group mt-3">
				<li class="list-group-item">
					<a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>">
						<?php echo Text::_('COM_USERS_LOGIN_RESET'); ?></a>
				</li>
				<li class="list-group-item">
					<a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
						<?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?></a>
				</li>
				<?php
				$usersConfig = ComponentHelper::getParams('com_users');
				if ($usersConfig->get('allowUserRegistration')) : ?>
					<li class="list-group-item">
						<a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
							<?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>