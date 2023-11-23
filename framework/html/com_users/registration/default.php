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

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<div class="row justify-content-center">
    <div class="com-users-registration registration col-lg-7 justify-content-center">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <div class="page-header">
                <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
            </div>
        <?php endif; ?>

        <form id="member-registration" action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="com-users-registration__form form-validate" enctype="multipart/form-data">
            <?php // Iterate through the form fieldsets and display each one. ?>
            <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
                <?php if ($fieldset->name === 'captcha' && $this->captchaEnabled) : ?>
                    <?php continue; ?>
                <?php endif; ?>
                <?php $fields = $this->form->getFieldset($fieldset->name); ?>
                <?php if (count($fields)) : ?>
                    <fieldset>
                        <?php // If the fieldset has a label set, display it as the legend. ?>
                        <?php if (isset($fieldset->label)) : ?>
                            <legend><?php echo Text::_($fieldset->label); ?></legend>
                        <?php endif; ?>
                        <?php echo $this->form->renderFieldset($fieldset->name, ['class' => 'as-form-group mb-3']); ?>
                    </fieldset>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($this->captchaEnabled) : ?>
                <?php echo $this->form->renderFieldset('captcha'); ?>
            <?php endif; ?>
            <div class="com-users-registration__submit control-group mt-3">
                <div class="controls">
                    <button type="submit" class="com-users-registration__register btn btn-lg btn-primary w-100 validate">
                        <?php echo Text::_('JREGISTER'); ?>
                    </button>
                    <input type="hidden" name="option" value="com_users">
                    <input type="hidden" name="task" value="registration.register">
                </div>
            </div>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>