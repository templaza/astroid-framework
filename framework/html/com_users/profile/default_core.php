<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<fieldset id="users-profile-core" class="com-users-profile__core">
    <legend class="mb-3 d-flex justify-content-between align-items-center">
        <span>
            <?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
        </span>
        <span>
            <?php if ($this->getCurrentUser()->id == $this->data->id) : ?>
                <a class="btn btn-sm btn-primary" href="<?php echo Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
                    <span class="icon-user-edit" aria-hidden="true"></span> <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
                </a>
            <?php endif; ?>
        </span>
    </legend>
    <ul class="list-group">
        <li class="list-group-item">
            <strong><?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?></strong>: <?php echo $this->escape($this->data->name); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></strong>: <?php echo $this->escape($this->data->username); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></strong>: <?php echo HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></strong>:
            <?php if ($this->data->lastvisitDate !== null) : ?>
                <?php echo HTMLHelper::_('date', $this->data->lastvisitDate, Text::_('DATE_FORMAT_LC1')); ?>
            <?php else : ?>
                <?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
            <?php endif; ?>
        </li>
    </ul>
</fieldset>
