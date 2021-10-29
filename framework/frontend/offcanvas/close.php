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

// class `offcanvas-close-btn` is required
?>
<div class="burger-menu-button active">
    <button aria-label="Off-Canvas Toggle" type="button" class="button close-offcanvas offcanvas-close-btn">
        <span class="box">
            <span class="inner"></span>
        </span>
    </button>
</div>