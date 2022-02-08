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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

JLoader::register('UsersHelperRoute', JPATH_SITE . '/components/com_users/helpers/route.php');

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');
?>
<form action="<?php echo Route::_('index.php', true, $params->get('usesecure', 0)); ?>" method="post" id="login-form" class="form-signin">
   <?php if ($params->get('pretext')) : ?>
      <div class="pretext">
         <?php echo $params->get('pretext'); ?>
      </div>
   <?php endif; ?>
   <div id="form-login-username" class="form-group">
      <?php if (!$params->get('usetext', 0)) : ?>
         <label for="modlgn-username"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
         <div class="input-group">
            <input id="modlgn-username" type="text" name="username" class="form-control" tabindex="0" size="18" />
         </div>
      <?php else : ?>
         <label for="modlgn-username"><?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
         <input id="modlgn-username" type="text" name="username" class="form-control" tabindex="0" size="18" />
      <?php endif; ?>
   </div>

   <div id="form-login-password" class="form-group">
      <?php if (!$params->get('usetext', 0)) : ?>
         <label for="modlgn-passwd"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
         <div class="input-group">
            <input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="0" size="18" />
         </div>
      <?php else : ?>
         <label for="modlgn-passwd"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
         <input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="0" size="18" />
      <?php endif; ?>
   </div>

   <?php if (count($twofactormethods) > 1) : ?>
      <div id="form-login-secretkey" class="form-group">
         <?php if (!$params->get('usetext', 0)) : ?>
            <label for="modlgn-secretkey">
               <?php echo Text::_('JGLOBAL_SECRETKEY'); ?>
               <i class="fas fa-info-circle hasTooltip" title="<?php echo Text::_('JGLOBAL_SECRETKEY_HELP'); ?>"></i>
            </label>
            <div class="input-group">
               <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" size="18" />
            </div>
         <?php else : ?>
            <label for="modlgn-secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
            <input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" size="18" />
         <?php endif; ?>
      </div>
   <?php endif; ?>
   <div class="d-flex justify-content-between">
      <div class="form-group d-flex justify-content-start">
         <?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
            <div class="custom-control custom-checkbox checkbox text-muted">
               <input type="checkbox" class="custom-control-input" id="modlgn-remember" name="remember" value="yes">
               <label class="custom-control-label" for="modlgn-remember"><?php echo Text::_('MOD_LOGIN_REMEMBER_ME'); ?></label>
            </div>
         <?php endif; ?>
      </div>
      <div class="form-check form-group d-flex justify-content-end">
         <a class="forget-password-link" href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>"><?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
      </div>
   </div>
   <div class="login-button">
      <button class="btn btn-lg btn-primary w-100" type="submit"><?php echo Text::_('JLOGIN'); ?></button>
   </div>
   <?php if ($params->get('posttext')) : ?>
      <div class="posttext">
         <?php echo $params->get('posttext'); ?>
      </div>
   <?php endif; ?>
   <input type="hidden" name="option" value="com_users" />
   <input type="hidden" name="task" value="user.login" />
   <input type="hidden" name="return" value="<?php echo $return; ?>" />
   <?php echo HTMLHelper::_('form.token'); ?>

   <?php $usersConfig = ComponentHelper::getParams('com_users'); ?>
   <ul class="login-helping-links">
      <?php if ($usersConfig->get('allowUserRegistration')) : ?>
         <li>
            <a class="login-help-link" href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>">
               <?php echo Text::_('MOD_LOGIN_REGISTER'); ?></a>
         </li>
      <?php endif; ?>
      <li>
         <a class="login-help-link" href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>">
            <?php echo Text::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
      </li>
   </ul>
</form>