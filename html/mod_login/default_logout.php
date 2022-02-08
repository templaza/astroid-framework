<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.keepalive');
?>
<form action="<?php echo Route::_('index.php', true, $params->get('usesecure', 0)); ?>" method="post" id="login-form" class="form-vertical">
   <?php if ($params->get('greeting', 1)) : ?>
      <div class="login-greeting">
         <?php if (!$params->get('name', 0)) : ?>
            <?php echo Text::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name'), ENT_COMPAT, 'UTF-8')); ?>
         <?php else : ?>
            <?php echo Text::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username'), ENT_COMPAT, 'UTF-8')); ?>
         <?php endif; ?>
      </div>
   <?php endif; ?>
   <?php if ($params->get('profilelink', 0)) : ?>
      <ul class="unstyled">
         <li>
            <a href="<?php echo Route::_('index.php?option=com_users&view=profile'); ?>">
               <?php echo Text::_('MOD_LOGIN_PROFILE'); ?>
            </a>
         </li>
      </ul>
   <?php endif; ?>
   <div class="logout-button">
      <input type="submit" name="Submit" class="btn btn-primary" value="<?php echo Text::_('JLOGOUT'); ?>" />
      <input type="hidden" name="option" value="com_users" />
      <input type="hidden" name="task" value="user.logout" />
      <input type="hidden" name="return" value="<?php echo $return; ?>" />
      <?php echo HTMLHelper::_('form.token'); ?>
   </div>
</form>