<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 *	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;

extract($displayData);

$triggerClasses = [];
$triggerClasses[] = 'header-offcanvas-trigger';
$triggerClasses[] = 'burger-menu-button';
$triggerClasses[] = 'align-self-center';
$triggerClasses[] = $visibility;

// `active` class will be added when offcanvas menu opened
?>
<div class="<?php echo implode(' ', $triggerClasses); ?>" data-offcanvas="<?php echo $offcanvas; ?>" data-effect="<?php echo $effect; ?>" data-direction="<?php echo $direction; ?>">
    <button type="button" aria-label="Off-Canvas Toggle" class="button">
        <span class="box">
            <span class="inner"></span>
        </span>
    </button>
</div>