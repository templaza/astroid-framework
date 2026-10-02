<?php

/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);
echo '<div class="'.$containerClass.'"><div class="header-mobilemenu-trigger burger-menu-button align-self-center"><button data-bs-toggle="offcanvas" data-bs-target="#astroid-mobilemenu" aria-controls="astroid-mobilemenu" type="button" class="button"><span class="box"><span class="inner"><span class="visually-hidden">Mobile Menu Toggle</span></span></span></button></div></div>';
?>