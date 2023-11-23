<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;

$icon = $this->params->get('contact_icons') == 0;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<dl class="com-contact__address contact-address dl-horizontal">
    <?php
    if (
        ($this->params->get('address_check') > 0) &&
        ($this->item->address || $this->item->suburb  || $this->item->state || $this->item->country || $this->item->postcode)
    ) : ?>
        <dt>
            <?php if ($icon && !$this->params->get('marker_address')) : ?>
                <span class="icon-address d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_ADDRESS'); ?></span>
            <?php else : ?>
                <span class="<?php echo $this->params->get('marker_class'); ?>">
                    <?php echo $this->params->get('marker_address'); ?>
                </span>
            <?php endif; ?>
        </dt>
        <dd>
        <?php if ($this->item->address && $this->params->get('show_street_address')) : ?>
            <span class="contact-street d-block"><?php echo nl2br($this->item->address, false); ?></span>
        <?php endif; ?>
        <?php if ($this->item->suburb && $this->params->get('show_suburb')) : ?>
            <span class="contact-suburb d-block"><?php echo $this->item->suburb; ?></span>
        <?php endif; ?>
        <?php if ($this->item->state && $this->params->get('show_state')) : ?>
            <span class="contact-state d-block"><?php echo $this->item->state; ?></span>
        <?php endif; ?>
        <?php if ($this->item->postcode && $this->params->get('show_postcode')) : ?>
            <span class="contact-postcode d-block"><?php echo $this->item->postcode; ?></span>
        <?php endif; ?>
        <?php if ($this->item->country && $this->params->get('show_country')) : ?>
            <span class="contact-country d-block"><?php echo $this->item->country; ?></span>
        <?php endif; ?>
        </dd>
    <?php endif; ?>

<?php if ($this->item->email_to && $this->params->get('show_email')) : ?>
    <dt>
        <?php if ($icon && !$this->params->get('marker_email')) : ?>
            <span class="icon-envelope d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_EMAIL_LABEL'); ?></span>
        <?php else : ?>
            <span class="<?php echo $this->params->get('marker_class'); ?>">
                <?php echo $this->params->get('marker_email'); ?>
            </span>
        <?php endif; ?>
    </dt>
    <dd>
        <span class="contact-emailto">
            <?php echo $this->item->email_to; ?>
        </span>
    </dd>
<?php endif; ?>

<?php if ($this->item->telephone && $this->params->get('show_telephone')) : ?>
    <dt>
        <?php if ($icon && !$this->params->get('marker_telephone')) : ?>
                <span class="icon-phone d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_TELEPHONE'); ?></span>
        <?php else : ?>
            <span class="<?php echo $this->params->get('marker_class'); ?>">
                <?php echo $this->params->get('marker_telephone'); ?>
            </span>
        <?php endif; ?>
    </dt>
    <dd>
        <span class="contact-telephone">
            <?php echo $this->item->telephone; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->fax && $this->params->get('show_fax')) : ?>
    <dt>
        <?php if ($icon && !$this->params->get('marker_fax')) : ?>
            <span class="icon-fax d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_FAX'); ?></span>
        <?php else : ?>
            <span class="<?php echo $this->params->get('marker_class'); ?>">
                <?php echo $this->params->get('marker_fax'); ?>
            </span>
        <?php endif; ?>
    </dt>
    <dd>
        <span class="contact-fax">
        <?php echo $this->item->fax; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->mobile && $this->params->get('show_mobile')) : ?>
    <dt>
        <?php if ($icon && !$this->params->get('marker_mobile')) : ?>
            <span class="icon-mobile d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_MOBILE'); ?></span>
        <?php else : ?>
            <span class="<?php echo $this->params->get('marker_class'); ?>">
                <?php echo $this->params->get('marker_mobile'); ?>
            </span>
        <?php endif; ?>
    </dt>
    <dd>
        <span class="contact-mobile">
            <?php echo $this->item->mobile; ?>
        </span>
    </dd>
<?php endif; ?>
<?php if ($this->item->webpage && $this->params->get('show_webpage')) : ?>
    <dt>
        <?php if ($icon && !$this->params->get('marker_webpage')) : ?>
            <span class="icon-home d-lg-inline d-none" aria-hidden="true"></span><span class="d-lg-none"><?php echo Text::_('COM_CONTACT_WEBPAGE'); ?></span>
        <?php else : ?>
            <span class="<?php echo $this->params->get('marker_class'); ?>">
                <?php echo $this->params->get('marker_webpage'); ?>
            </span>
        <?php endif; ?>
    </dt>
    <dd>
        <span class="contact-webpage">
            <a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer">
            <?php echo PunycodeHelper::urlToUTF8($this->item->webpage); ?></a>
        </span>
    </dd>
<?php endif; ?>
</dl>
