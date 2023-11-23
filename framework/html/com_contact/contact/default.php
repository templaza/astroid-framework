<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;

$tparams = $this->item->params;
$canDo   = ContentHelper::getActions('com_contact', 'category', $this->item->catid);
$canEdit = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by === $this->getCurrentUser()->id);
$htag    = $tparams->get('show_page_heading') ? 'h2' : 'h1';
$htag2   = ($tparams->get('show_page_heading') && $tparams->get('show_name')) ? 'h3' : 'h2';

?>
<div class="com-contact contact">
    <?php if ($tparams->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($tparams->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <?php $show_contact_category = $tparams->get('show_contact_category'); ?>
        <?php if ($show_contact_category === 'show_no_link') : ?>
        <<?php echo $htag2; ?>>
            <span class="contact-category"><?php echo $this->item->category_title; ?></span>
        </<?php echo $htag2; ?>>
    <?php elseif ($show_contact_category === 'show_with_link') : ?>
        <?php $contactLink = RouteHelper::getCategoryRoute($this->item->catid, $this->item->language); ?>
        <<?php echo $htag2; ?>>
            <span class="contact-category"><a href="<?php echo $contactLink; ?>"><?php echo $this->escape($this->item->category_title); ?></a></span>
        </<?php echo $htag2; ?>>
    <?php endif; ?>

    <div class="row g-xl-5 g-4">
        <div class="col-md-4"><?php ob_start(); ?>
            <?php if ($this->item->image && $tparams->get('show_image')) : ?>
                <div class="com-contact__thumbnail thumbnail mb-3">
                    <?php echo LayoutHelper::render(
                        'joomla.html.image',
                        [
                            'src'      => $this->item->image,
                            'alt'      => $this->item->name,
                            'class'    => 'img-fluid'
                        ]
                    ); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->item->name && $tparams->get('show_name')) : ?>
                <div class="page-header">
                    <<?php echo $htag; ?> class="contact-title-first">
                        <?php if ($this->item->published == 0) : ?>
                            <span class="badge bg-warning text-light"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                        <?php endif; ?>
                        <span class="contact-name"><?php echo $this->item->name; ?></span>
                    </<?php echo $htag; ?>>
                </div>
            <?php endif; ?>
            <?php echo $this->item->event->afterDisplayTitle; ?>
            <?php if ($this->item->con_position && $tparams->get('show_position')) : ?>
                <div class="contact-position"><?php echo $this->item->con_position; ?></div>
            <?php endif; ?>
            <?php if ($this->params->get('show_info', 1)) : ?>
                <?php if (($this->item->name && $tparams->get('show_name')) || ($this->item->con_position && $tparams->get('show_position'))) :
                    echo '<hr>';
                endif; ?>
                <div class="com-contact__container">
                    <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('COM_CONTACT_DETAILS') . '</' . $htag2 . '>'; ?>
                    <div class="com-contact__info">
                        <?php echo $this->loadTemplate('address'); ?>
                        <?php if ($tparams->get('allow_vcard')) : ?>
                            <hr>
                            <?php echo Text::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?>
                            <a href="<?php echo Route::_('index.php?option=com_contact&view=contact&catid=' . $this->item->catslug . '&id=' . $this->item->slug . '&format=vcf'); ?>">
                                <?php echo Text::_('COM_CONTACT_VCARD'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($tparams->get('show_profile') && $this->item->user_id && PluginHelper::isEnabled('user', 'profile')) : ?>
                <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('COM_CONTACT_PROFILE') . '</' . $htag2 . '>'; ?>
                <?php echo $this->loadTemplate('profile'); ?>
            <?php endif; ?>
            <?php echo $this->item->event->beforeDisplayContent; ?>
            <?php if ($tparams->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                <div class="com-contact__tags">
                    <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
                    <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
                </div>
            <?php endif; ?>
            <?php if ($tparams->get('show_contact_list') && count($this->contacts) > 1) : ?>
                <form action="#" method="get" name="selectForm" id="selectForm">
                    <label for="select_contact"><?php echo Text::_('COM_CONTACT_SELECT_CONTACT'); ?></label>
                    <?php echo HTMLHelper::_(
                        'select.genericlist',
                        $this->contacts,
                        'select_contact',
                        'class="form-select" onchange="document.location.href = this.value"',
                        'link',
                        'name',
                        $this->item->link
                    );
                    ?>
                </form>
            <?php endif; ?>
        <?php
        $contact_left = ob_get_clean();
        if (is_string($contact_left)) echo trim($contact_left);
        ?></div>
        <div class="col">
            <?php if ($canEdit) : ?>
                <div class="icons">
                    <div class="float-end">
                        <div>
                            <?php echo HTMLHelper::_('contacticon.edit', $this->item, $tparams); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->item->misc && $tparams->get('show_misc')) : ?>
                <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('COM_CONTACT_OTHER_INFORMATION') . '</' . $htag2 . '>'; ?>
                <div class="com-contact__miscinfo contact-miscinfo">
                    <?php echo $this->item->misc; ?>
                </div>
            <?php endif; ?>
            <?php if ($tparams->get('show_links')) : ?>
                <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('COM_CONTACT_LINKS') . '</' . $htag2 . '>'; ?>
                <?php echo $this->loadTemplate('links'); ?>
            <?php endif; ?>

            <?php if ($tparams->get('show_email_form') && ($this->item->email_to || $this->item->user_id)) : ?>
                <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('COM_CONTACT_EMAIL_FORM') . '</' . $htag2 . '>'; ?>
                <?php echo $this->loadTemplate('form'); ?>
            <?php endif; ?>
            <?php if ($tparams->get('show_articles') && $this->item->user_id && $this->item->articles) : ?>
                <?php echo '<' . $htag2 . ' class="contact-title-second">' . Text::_('JGLOBAL_ARTICLES') . '</' . $htag2 . '>'; ?>
                <?php echo $this->loadTemplate('articles'); ?>
            <?php endif; ?>
            <?php if ($tparams->get('show_user_custom_fields') && $this->contactUser) : ?>
                <?php echo $this->loadTemplate('user_custom_fields'); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php echo $this->item->event->afterDisplayContent; ?>
</div>
