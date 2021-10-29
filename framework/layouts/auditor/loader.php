<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2020 JoomDev.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 * 	DO NOT MODIFY THIS FILE DIRECTLY AS IT WILL BE OVERWRITTEN IN THE NEXT UPDATE
 *  You can easily override all files under /frontend/ folder.
 * 	Just copy the file to ROOT/templates/YOURTEMPLATE/html/frontend/header/ folder to create and override
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$document = Astroid\Framework::getDocument();

$document->addStyleDeclaration('#astroid-wrapper #astroid-sidebar-wrapper{background:#e9f2ee}.astroid-manager-navbar{background:#e9f2ee;box-shadow:0 0 10px #b0e2cc}.astroid-logo{box-shadow:0 0 10px #b0e2cc}body{background-color:#f9fdfb}#astroid-migration-loading{background:#fff;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;justify-content:center;align-items:center}#astroid-migration-loading.loaded span{color:#4caf50;transition:1s linear color}#astroid-migration-loading span{display:block;text-align:right;font-size:30px;color:#fff;transition:1s linear color}#astroid-migration-loading>div{max-width:400px}#astroid-migrating-loading{background:rgba(255,255,255,.9);position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;justify-content:center;align-items:center}#astroid-migrating-loading>div>div{display:block;text-align:right;font-size:18px;color:#4caf50}#astroid-migrating-loading>div{max-width:300px}');
?>
<div id="astroid-migration-loading">
    <div>
        <img src="<?php echo \JURI::root(); ?>/media/astroid/assets/images/logo-dark-wide.png" width="100%" />
        <span>auditor</span>
    </div>
</div>