<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2023 AstroidFrame.work.
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
            <span class="inner"><span class="visually-hidden">Off-Canvas Toggle</span></span>
        </span>
    </button>
</div>