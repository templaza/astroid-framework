<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 * You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
$params = Astroid\Framework::getTemplate()->getParams();
$contact_details = $params->get('contact_details', 1);
if (!$contact_details) {
   return;
}
$phone = $params->get('contact_phone_number', '');
$mobile = $params->get('contact_mobile_number', '');
$email = $params->get('contact_email_address', '');
$openhours = $params->get('contact_open_hours', '');
$address = $params->get('contact_address', '');
$contact_display = $params->get('contact_display', 'icons');
?>

<div class="astroid-contact-info">
   <?php if (!empty($address)) { ?>
      <span class="astroid-contact-address">
         <?php if ($contact_display == "icons") : ?>
            <i class="fas fa-map-marker-alt"></i>
         <?php endif; ?>
         <?php if ($contact_display == "text") : ?>
            <?php echo JText::_('TPL_ASTROID_ADDRESS_LABEL'); ?>:
         <?php endif; ?>
         <?php echo $address; ?>
      </span>
   <?php } ?>

   <?php if (!empty($phone)) { ?>
      <span class="astroid-contact-phone">
         <?php if ($contact_display == "icons") : ?>
            <i class="fas fa-phone-alt"></i>
         <?php endif; ?>
         <?php if ($contact_display == "text") : ?>
            <?php echo JText::_('TPL_ASTROID_PHONE_LABEL'); ?>:
         <?php endif; ?>
         <a href="tel:<?php echo str_replace(' ', '', $phone); ?>"><?php echo $phone; ?></a>
      </span>
   <?php } ?>

   <?php if (!empty($mobile)) { ?>
      <span class="astroid-contact-mobile">
         <?php if ($contact_display == "icons") : ?>
            <i class="fas fa-mobile-alt"></i>
         <?php endif; ?>
         <?php if ($contact_display == "text") : ?>
            <?php echo JText::_('TPL_ASTROID_MOBILE_LABEL'); ?>:
         <?php endif; ?>
         <a href="tel:<?php echo $mobile; ?>"><?php echo $mobile; ?></a>
      </span>
   <?php } ?>

   <?php if (!empty($email)) { ?>
      <span class="astroid-contact-email">
         <?php if ($contact_display == "icons") : ?>
            <i class="far fa-envelope"></i>
         <?php endif; ?>
         <?php if ($contact_display == "text") : ?>
            <?php echo JText::_('JGLOBAL_EMAIL'); ?>:
         <?php endif; ?>
         <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
      </span>
   <?php } ?>

   <?php if (!empty($openhours)) { ?>
      <span class="astroid-contact-openhours">
         <?php if ($contact_display == "icons") : ?>
            <i class="far fa-clock"></i>
         <?php endif; ?>
         <?php if ($contact_display == "text") : ?>:
         <?php echo JText::_('TPL_ASTROID_OPENHOURS_LABEL'); ?>
      <?php endif; ?>
      <?php echo $openhours; ?>
      </span>
   <?php } ?>
</div>